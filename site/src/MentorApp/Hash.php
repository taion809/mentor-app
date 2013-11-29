<?php
/**
 * @author Tyler Etters <tyler@etters.co>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */
namespace MentorApp;
/**
 * Add basic hashing method.
 */
trait Hash {

    /**
     * Trait method to create a random hash value for an ID
     * @return string
     */
    public function generate()
    {
        $parts  = md5(uniqid('', TRUE));
        $parts .= md5(microtime());
        $parts .= md5(rand());
        for($i=0; $i<16; $i++) {
            $parts = str_shuffle($parts);
        }
        $hash = substr($parts, 0, 10);
        if (!$this->exists($hash)) {
            return $hash;
        }
        return $this->generate();
    }

    /**
     * Validate the hash based on the pattern it's created with
     *
     * @param string $id identifer to validate
     * @return boolean
     */
    public function validateHash($id)
    {
        if (preg_match('/^[0-9a-f]{10}$/', $id) && is_string($id)) {
            return true;
        }
        return false;
    }
    
    /**
     * @param $id string
     */
    abstract public function exists($id);

}
