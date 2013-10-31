<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace MentorApp;

/**
 * Test to test the basic functionality of the User DAO
 * since we're overriding __set, this will make sure that 
 * is working properly
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test to add skills to the User object
     *
     * @todo - write test once Tag class is merged into master - dummy test
     */
    public function testAddTeachingSkills()
    {
        $this->assertTrue(true);
    }
} 
