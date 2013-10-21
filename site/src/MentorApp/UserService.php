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
     * @var string userTable name of the user table
     */
    protected $userTable;

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
        'teachingSkills' => 'teaching_skills',
        'learningSkills' => 'learning_skills',
        'timezone' => 'timezone'
    );

    /**
     * Constructor where the db and user instance are injected for testability
     *
     * @param \PDO db PDO instance
     */
    public function __construct(\PDO $db, $userTable = 'user')
    {
        $this->db = $db;
        $this->userTable = $userTable;
    }

    /**
     * Retrieve method to pull user information from the database and return a
     * the User instance populated with the correct information
     *
     * @param \MentorApp\User user user instance with the id property set
     * @return \MentorApp\User user instance populated with the rest of the
     * data
     */
    public function retrieve(\MentorApp\User $user)
    {
        if ($user->id == null || $user->id === "") {
            return $user;
        }
        $user_fields = implode(', ', $this->mapping);
        $query = 'SELECT ' . $user_fields . ' FROM ' . $this->userTable;
        $query .= ' WHERE id = :id';
        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('id' => $user->id));
            $values = $statement->fetch();
        } catch(\PDOException $e) {
            // log the error
            return $user;
        }
        foreach ($this->mapping as $key => $value) {
            $user->$key = htmlentities($values[$value]);
        }
        return $user;
    }

    /**
     * Save the User record, a fully populate user instance should be 
     * passed in and acted upon by the service. 
     *
     * @todo Create a check for the ID pattern match
     * @param \MentorApp\User user user instance
     * @return boolean indication of whether the user was saved correctly
     */
    public function create(\MentorApp\User $user)
    {
        $fields = implode(', ', $this->mapping);
        $valueKeys = '';
        $statementValues = array();
        foreach($this->mapping as $key => $field) {
            $valueKeys .= ':' . $field . ', ';
            $statementValues[$field] = $user->$key;
        }
        $query = 'INSERT INTO '. $this->userTable . ' (' . $fields . ') VALUES (' . substr($valueKeys, 0, -2) . ')';
        try {
            $statement = $this->db->prepare($query);
            $statement->execute($statementValues);
        } catch(\PDOException $e) {
            // log errors
           return false; 
        }
        return true;
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
