<?php
/**
 * @author Tyler Etters <tyler@etters.co>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */

/**
 * Add basic hashing method.
 */
trait Hash {

    /**
     * @return string
     */
    public function generateHash()
    {
        $parts  = md5(uniqid('', TRUE));
        $parts .= md5(microtime());
        $parts .= md5(rand());
        for($i=0; $i<16; $i++) {
            $parts = str_shuffle($parts);
        }
        return substr($parts, 0, 10);
    }
    
    /**
     * @param $id string
     */
    abstract public function exists($id);

}
