<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */

namespace MentorApp;

/**
 * Entity class to handle the relationship of a mentor and an apprentice
 */
class PartnershipManager
{
    /**
     * Use the hash trait to generate the id
     */
    use Hash;

    /** 
     * @var \PDO $db instance of PDO
     */
    protected $db;

    /**
     * Constructor
     *
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create a partnership between a mentor and an apprentice
     *
     * @param \MentorApp\User $mentor mentoring user instance
     * @param \MentorApp\User $apprentice apprentice user instance
     * @return boolean
     */
    public function create(User $mentor, User $apprentice)
    {
        $id = $this->generate();
        try {
            $query = "INSERT INTO partnerships (id, id_mentor, id_apprentice) VALUES (:id, :mentor, :apprentice)";
            $query .= " ON DUPLICATE KEY UPDATE mentor_id = :mentor";
            $statement = $this->db->prepare($query);
            $statement->execute(['id' => $id, 'mentor' => $mentor->id, 'apprentice' => $apprentice->id]);
            $rowCount = $statement->rowCount();
            if ($rowCount < 1) {
                return false;
            }
        } catch(\PDOException $e) {
            // log it
            return false;
        }
        return true;
    }

    /**
     * Removes a partnership
     *
     * @param string $id id of the relationship to delete
     * @return boolean
     */
    public function delete($id)
    {
        try {
            $query = "DELETE FROM partnership WHERE id = :id";
            $statement = $this->db->prepare($query);
            $statement->execute(['id' => $id]);
            $rowCount = $statement->rowCount();
            if ($rowCount < 1) {
                return false;
            }
        } catch(\PDOException $e) {
            // log it... 
            return false;
        }
        return true;
    }

    /**
     * Method to fulfill the abstract Hash trait method and verify the id
     * being generated doesn't already exist
     *
     * @param string $id ID to validate/verify
     * @return boolean true if id exists, false if it doesn't
     */
    public function exists($id)
    {
        if ($id === '' || !preg_match('/^[A-Fa-f0-9]{10}$/', $id)) {
            throw new \RuntimeException('Yeah...so, we had a problem we couldn\'t resolve, sorry!');
        }
        try {
            $query = "SELECT id FROM `partnership` WHERE id = :id";
            $statement = $this->db->prepare($query);
            $statement->execute(['id' => $id]);
            if ($statement->rowCount() > 0) {
                return true;
            }
        } catch (\PDOException $e) {
            // log it
            throw new \RuntimeException('Yeah...so, we had a problem we couldn\'t resolve, sorry!');
        }
        return false;
    }
}
