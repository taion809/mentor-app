<?php
/**
 * @author Stevan Goode <stevan@stevangoode.com>
 * @licence http://opensource.org/licences/MIT MIT
 * @package MentorApp
 */

namespace MentorApp;

/**
 * Class to interface with the data store and perform necessary actions with Tag objects
 */
class TagService
{
    /**
     * Use the Hash trait to take care of hash generation
     */
    use Hash;

    /**
     * @var \PDO $db PDO instance of the data store connection
     */
    protected $db;

    /**
     * The standard constructor
     *
     * @param \PDO $db The data store connection
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Fetches a tag from the data store
     *
     * @param string $name Name of the tag to be retrieved
     * @return Tag The retrieved tag
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    public function retrieve($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }

        try {
            $query = 'SELECT * FROM `tag` WHERE `name` = :name';
            $stmt = $this->db->prepare($query);
            $stmt->execute([':name' => $name]);

            if (!$stmt->rowCount()) {
                return null;
            }

            $fields = $stmt->fetch(\PDO::FETCH_ASSOC);

            $tag = new Tag();
            $tag->id = $fields['id'];
            $tag->name = $fields['name'];
            $tag->added = new \DateTime($fields['added']);
            $tag->authorized = ($fields['authorized'] == 1);
        } catch (\PDOException $e) {
            // log the exception
            return null;
        }
        return $tag;
    }

    /**
     * Searches for a tag based on a partial textual match
     *
     * @param string $term The term to search for
     * @return array The matching tags
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    public function searchByTerm($term)
    {
        if (empty($term)) {
            throw new \InvalidArgumentException('No search term supplied');
        }

        try {
            $query = 'SELECT * FROM `tag` WHERE `name` LIKE "%:term%"';
            $stmt = $this->db->prepare($query);
            $stmt->execute([':term' => $term]);
            if (!$stmt->rowCount()) {
                return [];
            }
            $return = [];

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tag = new Tag();
                $tag->id = $row['id'];
                $tag->name = $row['name'];
                $tag->added = new \DateTime($row['added']);
                $tag->authorized = ($row['authorized'] == 1);

                $tags[] = $tag;
            }
        } catch (\PDOException $e) {
            // log exception
            return [];
        }
            return $tags;
    }

    /**
     * Saves a tag to the database
     *
     * @param Tag $tag The tag to save
     * @return TagService
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    public function save(Tag $tag)
    {
        if (empty($tag->name)) {
            throw new \InvalidArgumentException('Tag is missing a name');
        }
        if ($tag->id === null) {
            $tag->id = $this->generate();
        }
        $id = $tag->id;
        $name = $tag->name;
        $authorized = $tag->authorized ? 1 : 0;
        $added = $tag->added;
        try {
            $query = 'INSERT INTO `tag` (
                `id`,
                `name`,
                `authorized`,
                `added`
            ) VALUES (
                :id,
                :name,
                :authorized,
                :added
            ) ON DUPLICATE KEY UPDATE
                `authorized` = :authorized
            ';

            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id, 'name' => $name, 'authorized' => $authorized, 'added' => $added]);
        } catch (\PDOException $e) {
            // log exception
            return false;
        }
        return true;
    }

    /**
     * Method to delete an existing tag
     *
     * @param string $id the ID of the tag
     * @return boolean
     */
    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_REGEX, ['regex' =>'/^[a-f0-9]{10}$/'])) {
            return false;
        }
        try {
            $query = "DELETE FROM tags WHERE id = :id";
            $statement = $this->db->prepare($query);
            $statement->execute(array('id' => $id));
            $rowCount = $statement->rowCount();
            if ($rowCount < 1) {
                return false;
            }
        } catch(\PDOException $e) {
            // log it
        }
        return true;
    }

    /**
     * Exists method satisfies the contract of the trait and determines
     * whether or not the generated id exists in the system
     *
     * @param string id the id to check
     * @return boolean returns true if id exists and false otherwise
     */
    public function exists($id)
    {
        try {
            $query = "SELECT id FROM `tags` WHERE id = :id";
            $statement = $this->db->prepare($query);
            $statement->execute(['id' => $id]);
            if($statement->rowCount() > 0) {
                return true;
            }
            return false;
        } catch (\PDOException $e) {
            // log
            throw new \RuntimeException('Rut Roh! Something terrible happened and we couldn\'t fix it...');
        }
        return false;
    }
}
