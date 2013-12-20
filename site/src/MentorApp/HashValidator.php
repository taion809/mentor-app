<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */
namespace MentorApp;

/**
 * Helper class to allow for hashes to be validated against the expected
 * pattern
 */
class HashValidator
{
    /**
     * @var string pattern regex to validate the hash
     */
    protected $pattern = '/^[0-9a-f]{10}$/';

    /**
     * Validate method to ensure the hash fits the pattern provided
     *
     * @param string $hash hash to validate
     * @return boolean returns true if valid hash, false otherwise
     */
    public function validate($hash)
    {
        if (preg_match($this->pattern, $hash)) {
            return true;
        }
        return false;
    }

    /**
     * Set the pattern
     *
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Get the pattern
     *
     * @return string $pattern
     */
    public function getPattern()
    {
        return $this->pattern;
    } 
}
