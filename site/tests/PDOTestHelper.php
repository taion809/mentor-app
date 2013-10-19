<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

class PDOTestHelper extends \PDO
{
    /**
     * Override the constructor so it can be mocked
     */
    public function __construct()
    {
    }
}
