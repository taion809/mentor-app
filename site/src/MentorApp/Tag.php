<?php
/**
 * @author Stevan Goode <stevan@stevangoode.com>
 * @licence http://opensource.org/licences/MIT MIT
 * @package MentorApp
 */

namespace MentorApp;

/**
 * Tag class that is used to represent a single tag
 */
class Tag
{
    /**
     * @var string name The unique name for the tag
     */
    public $name;

    /**
     * @var boolean authorized If the tag has been authorized for public use
     */
    public $authorized;

    /**
     * @var \DateTime added The date\time the tag was added to the system
     */
    public $added;

    /**
     * __set magic method to prevent additional properties from being set
     * this will override the default behavior of creating a new property
     *
     * @param string $name name of the property attempting to be set
     * @param string $value value of the property attempting to be set
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        throw new \Exception('You cannot set the unknown property: '.$name);
    }
}
