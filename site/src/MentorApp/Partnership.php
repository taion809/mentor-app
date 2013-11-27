<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */
namespace MentorApp;

class Partnership
{
    /**
     * @var string $id identifier for the partnership
     */
    public $id;

    /**
     * @var \MentorApp\User $mentor identifier for the Mentor
     */
    public $mentor;

    /**
     * @var \MentorApp\User $apprentice identifier for the apprentice
     */
    public $apprentice;
}
