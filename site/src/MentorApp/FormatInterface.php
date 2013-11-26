<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */
namespace MentorApp;

/**
 * Interface used for formatting API output
 */
interface FormatInterface
{
    /**
     * Format method returns formatted out for the object it is used with
     * @return mixed formatted output
     */
    public function format();
}
