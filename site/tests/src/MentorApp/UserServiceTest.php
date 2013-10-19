<?php
namespace MentorApp;
class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create resources and mocks that will be used by the tests
     */
    public function setUp()
    {
        $this->db = $this->getMock('\PDOTestHelper', array('prepare'));
        $this->statement = $this->getMock('\PDOStatement', array('execute', 'fetch'));
    }

    /**
     * Destroy all the resources after each test
     */
    public function tearDown()
    {
        unset($this->db);
        unset($this->statement);
    }

    /**
     * Ensure that a User object with no id property set, will return the same
     * User object
     */
    public function testUserWithNoIDReturnsEmptyUser()
    {
        $user = new User();
        $userService = new UserService($this->db);
        $returnedUser = $userService->retrieve($user);
        $this->assertSame($user, $returnedUser, 'Returned object not the same');
    }

    /**
     * Ensure that a User object with an id will run the query and return
     * a fully populated User object
     */
    public function testUserWithIdReturnsPopulatedUser()
    {
        $user = new User();
        $user->id = 'abc123def4';
        $expectedQuery = "SELECT id, first_name, last_name, email, irc_nick, ";
        $expectedQuery .= "twitter_handle, mentor_available, apprentice_available, ";
        $expectedQuery .= "teaching_skills, learning_skills, timezone FROM user WHERE id = :id";
        $mockData = array(
            'id' => $user->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@gmail.com',
            'irc_nick' => 'testuser',
            'twitter_handle' => 'testuser',
            'mentor_available' => true,
            'apprentice_available' => false,
            'teaching_skills' => 'php, oop, mysql, testing',
            'learning_skills' => 'tdd',
            'timezone' => 'America/Chicago',
        );
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($expectedQuery)
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('execute')
            ->with(array('id' => $user->id))
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($mockData));
        $userService = new UserService($this->db);
        $returnedUser = $userService->retrieve($user);
        $this->assertEquals(
            $mockData['id'],
            $returnedUser->id,
            'ID was not the same'
        );
        $this->assertEquals(
            $mockData['first_name'],
            $returnedUser->firstName,
            'First name was not the same'
        );        
    }
}
