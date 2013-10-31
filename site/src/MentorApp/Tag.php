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
}
