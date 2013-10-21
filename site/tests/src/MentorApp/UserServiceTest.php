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

    /**
     * Test to ensure the user object is returned when PDO throws an exception
     */
    public function testGetUserWhenPDOThrowsException()
    {
        $this->db->expects($this->once())
            ->method('prepare')
            ->will($this->throwException(new \PDOException));
        $user = new User();
        $user->id = 'bbccdd1134';
        $userService = new UserService($this->db);
        $retrievedUser = $userService->retrieve($user);
        $this->assertSame($user, $retrievedUser);
    }

    /**
     * Test to ensure that a new user can be created by the user service
     */
    public function testEnsureUserIsCreated()
    {
        $expectedQuery = 'INSERT INTO user (id, first_name, last_name, email, ';
        $expectedQuery .= 'irc_nick, twitter_handle, mentor_available, ';
        $expectedQuery .= 'apprentice_available, teaching_skills, learning_skills, ';
        $expectedQuery .= 'timezone) VALUES (:id, :first_name, :last_name, :email, ';
        $expectedQuery .= ':irc_nick, :twitter_handle, :mentor_available, ';
        $expectedQuery .= ':apprentice_available, :teaching_skills, :learning_skills, ';
        $expectedQuery .= ':timezone)';
        $user = new User();
        $user->id = '1932abed12';
        $user->firstName = 'Test';
        $user->lastName = 'User';
        $user->email = 'test.user@gmail.com';
        $user->ircNick = 'testUser';
        $user->twitterHandle = '@testUser';
        $user->mentor_available = true;
        $user->apprentice_available = false;
        $user->teaching_skills = 'OOP, TDD';
        $user->learning_skills = '';
        $user->timezone = 'America/Chicago';
        $statementParams = array(
            'id' => $user->id,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'email' => $user->email,
            'irc_nick' => $user->ircNick,
            'twitter_handle' => $user->twitterHandle,
            'mentor_available' => $user->mentorAvailable,
            'apprentice_available' => $user->apprenticeAvailable,
            'teaching_skills' => $user->teachingSkills,
            'learning_skills' => $user->learningSkills,
            'timezone' => $user->timezone
        );
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($expectedQuery)
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('execute')
            ->with($statementParams)
            ->will($this->returnValue($this->statement));
        $userService = new UserService($this->db);
        $savedUser = $userService->create($user);
        $this->assertTrue($savedUser);
    }

    /**
     * Test to ensure that PDOException causes the UserService::create to return false
     */
    public function testPDOExceptionCausesServiceToReturnFalse()
    {
        $this->db->expects($this->once())
            ->method('prepare')
            ->will($this->throwException(new \PDOException));
        $user = new User();
        $user->id = '123abcde45';
        $user->firstName = 'Test';
        $user->lastName = 'User';
        $user->email = 'test.user@gmail.com';
        $user->ircNick = 'testUser';
        $user->twitterHandle = '@testUser';
        $user->mentorAvailable = true;
        $user->apprenticeAvailable = false;
        $user->teachingSkills = 'OOP';
        $user->learningSkills = '';
        $user->timezone = 'America/Chicago';
        $userService = new UserService($this->db);
        $result = $userService->create($user);
        $this->assertFalse($result);
    }
}
