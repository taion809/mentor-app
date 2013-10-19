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
     * @var string user_table name of the user table
     */
    protected $user_table;

    /**
     * Constructor where the db and user instance are injected for testability
     *
     * @param \PDO db PDO instance
     */
    public function __construct(\PDO $db, $user_table = 'user')
    {
        $this->db = $db;
        $this->user_table = $user_table;
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
        
        $user_fields = implode(', ',get_object_vars($user));
        $query = 'SELECT ' . $user_fields . ' FROM ' . $this->user_table;
        $query .= ' WHERE id = :id';
        $statement = $this->db->prepare($query);
        $statement->execute(array('id' => $user->id));
        $values = $statement->fetch();
        foreach ($values as $key => $value) {
            $user->$key = htmlentities($value);
        }
        return $user;
    }
}
