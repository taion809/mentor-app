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
     * Test to make sure arbitrarily setting an object property doesn't alter
     * the interface of the User object
     */
    public function testCannotSetArbitraryProperty()
    {
        $user = new User();
        $user->first_name = 'Test';
        $user->favorite_ice_cream = 'Rocky Road'; 
        $user_data = get_object_vars($user);
        $properties = array_keys($user_data);
        $this->assertFalse(
            in_array('favorite_ice_cream', $properties),
            'A property was arbitrarily set on the User DAO'
        );
    }

    /**
     * Test to ensure that addTeaching 
     */
} 
