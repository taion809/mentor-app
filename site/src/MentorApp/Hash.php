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
        $finalLength = 10;
        $shuffle = str_shuffle(md5(rand()) . md5(rand()) . md5(rand()));
        $length = strlen($shuffle);
        $hash = substr($shuffle, rand(0, $length - $finalLength), $finalLength);
        return $hash;
        echo $hash . "\n";
    }

}
