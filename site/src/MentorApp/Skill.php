<?php
/**
 * @author Stevan Goode <stevan@stevangoode.com>
 * @licence http://opensource.org/licences/MIT MIT
 * @package MentorApp
 */

namespace MentorApp;

/**
 * Skill class that is used to represent a single skill
 */
class Skill
{
    /**
     * @var string id The ID hash for the skill
     */
    public $id;

    /**
     * @var string name The unique name for the skill
     */
    public $name;

    /**
     * @var boolean authorized If the skill has been authorized for public use
     */
    public $authorized;

    /**
     * @var \DateTime added The date\time the skill was added to the system
     */
    public $added;
}
