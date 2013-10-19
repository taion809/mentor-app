<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */

namespace MentorApp;

/**
 * User class that is used to represent the data pertaining to a 
 * single user
 */
class User
{
    /**
     * @var string id unique identifier for user
     */
    public $id;

    /**
     * @var string first_name first name of the user
     */
    public $first_name;

    /**
     * @var string last_name last name of the user
     */
    public $last_name;

    /**
     * @var string email email address for the user
     */
    public $email;

    /**
     * @var string irc_nick irc nickname for the user
     */
    public $irc_nick;

    /**
     * @var string twitter_handle twitter hanle for the user
     */
    public $twitter_handle;

    /**
     * @var boolean mentor_available indicates whether user is accepting apprentices
     */
    public $mentor_available;

    /**
     * @var boolean apprentice_available indicates whether user is seeking a mentor
     */
    public $apprentice_available;

    /**
     * @var array teaching_skills an array of skills a user feels comfortable teaching
     */
    public $teaching_skills = array();

    /**
     * @var array learning_skills an array of skills a user wants to learn
     */
    public $learning_skills = array();

    /**
     * @var string timezone the time zone of the user
     */
    public $timezone;

    /**
     * __set magic method to prevent additional properties from being set
     * this will override the default behavior of creating a new property
     *
     * @param string name name of the property attempting to be set
     * @param string value value of the property attempting to be set
     */
    public function __set($name, $value)
    {
    }

}
