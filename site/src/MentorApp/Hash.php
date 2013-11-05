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
        $part1 = md5(serialize($this));
        $part2 = md5(get_class($this));
        $part3 = md5(rand());
        $shuffle = str_shuffle($part1 . $part2 . $part3);
        return substr($shuffle, 0, 10);
    }

}
