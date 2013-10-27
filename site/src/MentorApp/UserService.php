<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */

namespace MentorApp;

/**
 * Class to interface with the data store and perform the necessary actions on 
 * the provided User instance
 *
 * @method \MentorApp\User retrieve(\MentorApp\User $user)
 */
class UserService
{
    /**
     * @var \PDO db PDO instance to be used by the rest of the class
     */
    protected $db;

    /**
     * @var array mapping mapping of User properties to database fields
     */
    protected $mapping = array(
        'id' => 'id',
        'firstName' => 'first_name',
        'lastName' => 'last_name',
        'email' => 'email',
        'ircNick' => 'irc_nick',
        'twitterHandle' => 'twitter_handle',
        'mentorAvailable' => 'mentor_available',
        'apprenticeAvailable' => 'apprentice_available',
        'timezone' => 'timezone'
    );

    /**
     * Constructor where the db and user instance are injected for testability
     *
     * @param \PDO db PDO instance
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieve method to pull user information from the database and return a
     * the User instance populated with the correct information
     *
     * @param string id ID to search for and retrieve
     * @return \MentorApp\User user instance populated with the rest of the
     * data
     */
    public function retrieve($id)
    {
        if (!is_string($id) || $id == '') {
            return null;
        }
        $user_fields = implode(', ', $this->mapping);
        $query = 'SELECT ' . $user_fields . ' FROM user ';  
        $query .= 'WHERE id = :id';
        $teachingTagQuery = 'SELECT id_tag FROM teaching_skills WHERE id_user = :id';
        $learningTagQuery = 'SELECT id_tag FROM learning_skills WHERE id_user = :id';
        try {
            $statement = $this->db->prepare($query);
            $teachingStatement = $this->db->prepare($teachingTagQuery);
            $learningStatement = $this->db->prepare($learningTagQuery);
            $statement->execute(array('id' => $id));
            $teachingStatement->execute(array('id' => $id));
            $learningStatement->execute(array('id' => $id));
            $values = $statement->fetch();
            $teachingSkills = $teachingStatement->fetchAll();
            $learningSkills = $learningStatement->fetchAll();
        } catch (\PDOException $e) {
            // log the error
            return null;
        }
        $user = new User();
        foreach ($this->mapping as $key => $value) {
            $user->$key = htmlentities($values[$value]);
        }
        $user->teachingSkills = $teachingSkills;
        $user->learningSkills = $learningSkills;
        return $user;
    }

    /**
     * Save the User record, a fully populate user instance should be 
     * passed in and acted upon by the service. 
     *
     * @todo Create a check for the ID pattern match
     * @todo Interact with the relational table for tags/skills
     * @param \MentorApp\User user user instance
     * @return boolean indication of whether the user was saved correctly
     */
    public function create(\MentorApp\User $user)
    {
        $fields = implode(', ', $this->mapping);
        $valueKeys = '';
        $statementValues = array();
        foreach ($this->mapping as $key => $field) {
            $valueKeys .= ':' . $field . ', ';
            $statementValues[$field] = $user->$key;
        }
        $query = 'INSERT INTO user (' . $fields . ') VALUES (' . substr($valueKeys, 0, -2) . ')';
        try {
            $statement = $this->db->prepare($query);
            $statement->execute($statementValues);
            $this->saveTags($user->id, $user->teachingSkills);
            $this->saveTags($user->id, $user->learningSkills, 'learning');
        } catch (\PDOException $e) {
            // log errors
            return false;
        }
        return true;
    }

    /**
     * Method to handle the saving of skills to a specific user
     *
     * @param string user_id id of the user
     * @param array tags an array of tag instances to attach to the user
     * @param string type the type of skills to be saved 
     */
    private function saveTags($user_id, Array $tags, $type="teaching")
    {
        if ($type !== "teaching" && $type !== "learning") {
            return false;
        } 
        $table = $type . "_skills";
        $query = "INSERT INTO $table (id_user, id_tag) VALUES (:user, :tag)";
        $statement = $this->db->prepare($query);
        foreach ($tags as $tag) {
            try {
                $statement->execute(array('user' => $user_id, 'tag' => $tag->id));
            } catch (\PDOException $e) {
                // log it 
                // maybe rethrow it and catch it in the create/update methods?
            }
        }
    } 

    /**
     * Method to override the default mapping array
     *
     * @var array mapping mapping array
     */
    public function setMapping(Array $mapping)
    {
        $this->mapping = $mapping;
    }
}
