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
        $this->statement = $this->getMock('\PDOStatement', array('execute', 'fetch', 'fetchAll', 'rowCount'));
        $this->teachingCheckStatement = $this->getMock('\PDOStatement', array('execute', 'fetch'));
        $this->learningCheckStatement = $this->getMock('\PDOStatement', array('execute', 'fetch'));
        $this->mockData = array();
        $this->mockData['id'] = '';
        $this->mockData['first_name'] = 'Test';
        $this->mockData['last_name'] = 'User';
        $this->mockData['email'] = 'testuser@gmail.com';
        $this->mockData['irc_nick'] = 'testuser';
        $this->mockData['twitter_handle'] = '@testuser';
        $this->mockData['mentor_available'] = true;
        $this->mockData['apprentice_available'] = false;
        $this->mockData['timezone'] = 'America/Chicago';
    }

    /**
     * Destroy all the resources after each test
     */
    public function tearDown()
    {
        unset($this->db);
        unset($this->statement);
        unset($this->mockData);
    }

    /**
     * Ensure that an empty string paramenter will return null from the user service
     */
    public function testUserWithNoIDReturnsNull()
    {
        $id = '';
        $userService = new UserService($this->db);
        $returnedUser = $userService->retrieve($id);
        $this->assertNull($returnedUser, "Did not return a null");
    }

    /**
     * Ensure that a User object with an id will run the query and return
     * a fully populated User object
     */
    public function testUserWithIdReturnsPopulatedUser()
    {
        $id = 'abc123def4';
        $this->mockData['id'] = $id;
        $expectedQuery = "SELECT id, first_name, last_name, email, irc_nick, ";
        $expectedQuery .= "twitter_handle, mentor_available, apprentice_available, ";
        $expectedQuery .= "timezone FROM user WHERE id = :id";
        $teachingQuery = 'SELECT id_tag FROM teaching_skills WHERE id_user = :id';
        $learningQuery = 'SELECT id_tag FROM learning_skills WHERE id_user = :id';

        $this->db->expects($this->at(0))
            ->method('prepare')
            ->with($expectedQuery)
            ->will($this->returnValue($this->statement));

        $this->db->expects($this->at(1))
            ->method('prepare')
            ->with($teachingQuery)
            ->will($this->returnValue($this->statement));

        $this->db->expects($this->at(2))
            ->method('prepare')
            ->with($learningQuery)
            ->will($this->returnValue($this->statement));

        $this->statement->expects($this->exactly(3))
            ->method('execute')
            ->with(array('id' => $id))
            ->will($this->returnValue($this->statement));

        $this->statement->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($this->mockData));

        $this->statement->expects($this->exactly(2))
            ->method('fetchAll');

        $userService = new UserService($this->db);
        $returnedUser = $userService->retrieve($id);
        $this->assertEquals(
            $this->mockData['id'],
            $returnedUser->id,
            'ID was not the same'
        );
        $this->assertEquals(
            $this->mockData['first_name'],
            $returnedUser->firstName,
            'First name was not the same'
        );        
    }

    /**
     * Test to ensure null is returned when PDO throws an exception
     */
    public function testGetUserWhenPDOThrowsException()
    {
        $this->db->expects($this->once())
            ->method('prepare')
            ->will($this->throwException(new \PDOException));
        $id = 'bbccdd1134';
        $userService = new UserService($this->db);
        $retrievedUser = $userService->retrieve($id);
        $this->assertNull($retrievedUser); 
    }

    /**
     * Test to ensure that a new user can be created by the user service
     */
    public function testEnsureUserIsCreated()
    {
        $expectedQuery = 'INSERT INTO user (id, first_name, last_name, email, ';
        $expectedQuery .= 'irc_nick, twitter_handle, mentor_available, ';
        $expectedQuery .= 'apprentice_available, ';
        $expectedQuery .= 'timezone) VALUES (:id, :first_name, :last_name, :email, ';
        $expectedQuery .= ':irc_nick, :twitter_handle, :mentor_available, ';
        $expectedQuery .= ':apprentice_available, :timezone)';
        $teachingQuery = 'INSERT INTO teaching_skills (id_user, id_tag) VALUES (:user, :tag)';
        $learningQuery = 'INSERT INTO learning_skills (id_user, id_tag) VALUES (:user, :tag)';
        $teachingCheck = 'SELECT id_tag FROM teaching_skills WHERE id_user=:id';
        $learningCheck = 'SELECT id_tag FROM learning_skills WHERE id_user=:id';
        $user = new User();
        $user->id = '1932abed12';
        $user->firstName = 'Test';
        $user->lastName = 'User';
        $user->email = 'test.user@gmail.com';
        $user->ircNick = 'testUser';
        $user->twitterHandle = '@testUser';
        $user->mentor_available = true;
        $user->apprentice_available = false;
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
            'timezone' => $user->timezone,
        );

        $this->db->expects($this->at(0))
            ->method('prepare')
            ->with($expectedQuery)
            ->will($this->returnValue($this->statement));

        $this->db->expects($this->at(1))
            ->method('prepare')
            ->with($teachingCheck)
            ->will($this->returnValue($this->teachingCheckStatement));

        $this->db->expects($this->at(2))
            ->method('prepare')
            ->with($teachingQuery)
            ->will($this->returnValue($this->statement));

        $this->db->expects($this->at(3))
            ->method('prepare')
            ->with($learningCheck)
            ->will($this->returnValue($this->learningCheckStatement));

        $this->db->expects($this->at(4))
            ->method('prepare')
            ->with($learningQuery)
            ->will($this->returnValue($this->statement));

        $this->statement->expects($this->once())
            ->method('execute')
            ->with($statementParams)
            ->will($this->returnValue($this->statement));

        $this->teachingCheckStatement->expects($this->once())
            ->method('execute')
            ->with(array('id' => $user->id))
            ->will($this->returnValue($this->teachingCheckStatement));

        $this->learningCheckStatement->expects($this->once())
            ->method('execute')
            ->with(array('id' => $user->id))
            ->will($this->returnValue($this->teachingCheckStatement));

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
        $user->timezone = 'America/Chicago';
        $userService = new UserService($this->db);
        $result = $userService->create($user);
        $this->assertFalse($result);
    }

    /**
     * Test to ensure a user can be updated properly
     */
    public function testUserUpdate()
    {
        // create a user
        $user = new User();
        $user->id = '123abcde45';
        $user->firstName = 'Test';
        $user->lastName = 'User';
        $user->email = 'test.user@gmail.com';
        $user->ircNick = 'testUser';
        $user->twitterHandle = '@testUser';
        $user->mentorAvailable = true;
        $user->apprenticeAvailable = false;
        $user->timezone = 'America/Chicago';

        //build the array for the execute method
        $valueArray = array();
        $valueArray['first_name'] = $user->firstName;
        $valueArray['last_name'] = $user->lastName;
        $valueArray['email'] = $user->email;
        $valueArray['irc_nick'] = $user->ircNick;
        $valueArray['twitter_handle'] = $user->twitterHandle;
        $valueArray['mentor_available'] = $user->mentorAvailable;
        $valueArray['apprentice_available'] = $user->apprenticeAvailable;
        $valueArray['timezone'] = $user->timezone;

        // build the expected query for user
        $expectedQuery = "UPDATE user SET first_name=:first_name, last_name=:last_name, ";
        $expectedQuery .= "email=:email, irc_nick=:irc_nick, twitter_handle=:twitter_handle, ";
        $expectedQuery .= "mentor_available=:mentor_available, apprentice_available=:apprentice_available, ";
        $expectedQuery .= "timezone=:timezone WHERE id=:id";

        $this->db->expects($this->once())
            ->method('prepare')
            ->with($expectedQuery)
            ->will($this->returnValue($this->statement));

        $this->statement->expects($this->once())
            ->method('execute')
            ->with($valueArray)
            ->will($this->returnValue($this->statement));

        $this->statement->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(1));

        $userService = new UserService($this->db);
        $this->assertTrue($userService->update($user));
    }
}
