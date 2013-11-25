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
     * @param $id string
     */
    abstract public function exists($id);

}
