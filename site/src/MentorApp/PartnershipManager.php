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
        $id = '';
        try {
            $query = "INSERT INTO partnerships (id, mentor_id, apprentice_id) VALUES (:id, :mentor, :apprentice)";
            $query .= " ON DUPLICATE KEY UPDATE mentor_id = :mentor";
            $statement = $this->db->prepare($query);
            $statement->execute(['id' => $id, 'mentor' => $mentor->id, 'apprentice' => $apprentice->id]);
            $rowCount = $this->db->rowCount();
            if ($rowCount < 1) {
                return false;
            }
        } catch(\PDOException $e) {
            // log it
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
            $query = "DELETE FROM partnerships WHERE id = :id";
            $statement = $this->db->prepare($query);
            $statement->execute(['id' => $id]);
            $rowCount = $this->db->rowCount();
            if ($rowCount < 1) {
                return false;
            }
        } catch(\PDOException $e) {
            // log it... 
        }
        return true;
    }
}
