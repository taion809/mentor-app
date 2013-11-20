<?php
namespace MentorApp;

class PartnershipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Method to set up the dependencies for use in the test
     */
    public function setUp()
    {
        $this->db = $this->getMock('\PDOTestHelper', ['prepare']);
        $this->statement = $this->getMock('\PDOStatement', ['execute', 'rowCount']);
    }

    /**
     * Method to tear down the dependencies at the end of each test
     */
    public function tearDown()
    {
        unset($this->db);
        unset($this->statement);
    }

    /**
     * Test to create a partnership between 2 users
     */
    public function testCreatePartnership()
    {
        $mentor = new User();
        $mentor->id = '1aef234567';
        $apprentice = new User();
        $apprentice->id = '12aef34568';
        $query = "INSERT INTO partnerships (id, id_mentor, id_apprentice) VALUES (:id, :mentor, :apprentice)";
        $query .= " ON DUPLICATE KEY UPDATE mentor_id = :mentor";

        // stubs for the Hash functionality
        $this->db->expects($this->at(0))
            ->method('prepare')
            ->with('SELECT id FROM `partnership` WHERE id = :id')
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->at(0))
            ->method('execute')
            ->with($this->isType('array'))
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->at(1))
            ->method('rowCount')
            ->will($this->returnValue(0));

        // stubs for the save functionality
           $this->db->expects($this->at(1))
            ->method('prepare')
            ->with($query)
            ->will($this->returnValue($this->statement));
         $this->statement->expects($this->at(2))
            ->method('execute')
            ->with($this->arrayHasKey('id'))
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->at(3))
            ->method('rowCount')
            ->will($this->returnValue(1));
        
        $partnershipManager = new PartnershipManager($this->db);
        $result = $partnershipManager->create($mentor, $apprentice);
        $this->assertTrue($result);
    }

    /**
     * Test to ensure false is return when there are 0 affected rows
     */
    public function testCreatePartnershipInsertFails()
    {
        $mentor = new User();
        $mentor->id = '1234567';
        $apprentice = new User();
        $apprentice->id = '1234568';
        $query = "INSERT INTO partnerships (id, id_mentor, id_apprentice) VALUES (:id, :mentor, :apprentice)";
        $query .= " ON DUPLICATE KEY UPDATE mentor_id = :mentor";
        $this->db->expects($this->at(0))
            ->method('prepare')
            ->with('SELECT id FROM `partnership` WHERE id = :id')
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->at(0))
            ->method('execute')
            ->with($this->isType('array'))
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->at(0))
            ->method('rowCount')
            ->will($this->returnValue(0));
        $this->db->expects($this->at(1))
            ->method('prepare')
            ->with($query)
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->at(2))
            ->method('execute')
            ->with($this->arrayHasKey('id'))
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->at(1))
            ->method('rowCount')
            ->will($this->returnValue(0));
        $partnershipManager = new PartnershipManager($this->db);
        $result = $partnershipManager->create($mentor, $apprentice);
        $this->assertFalse($result);
    }

    /**
     * Test to ensure that when an exception is thrown it is caught and
     * returns false
     */
    public function testExceptionReturnsFalse()
    {
        $mentor = new User();
        $mentor->id = '1234567890';
        $apprentice = new User();
        $apprentice->id = '12344';
        $this->db->expects($this->at(0))
            ->method('prepare')
            ->with('SELECT id FROM `partnership` WHERE id = :id')
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('execute')
            ->with($this->isType('array'))
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(0));
        $this->db->expects($this->at(1))
            ->method('prepare')
            ->will($this->throwException(new \PDOException));
        $partnershipManager = new PartnershipManager($this->db);
        $result = $partnershipManager->create($mentor, $apprentice);
        $this->assertFalse($result);
    }

    /**
     * Test to ensure that a partnership can be deleted
     */
    public function testPartnershipDeleted()
    {
        $id = '12345678';
        $query = "DELETE FROM partnership WHERE id = :id";
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('execute')
            ->with(['id' => $id])
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(1));
        $partnershipManager = new PartnershipManager($this->db);
        $result = $partnershipManager->delete($id);
        $this->assertTrue($result);
    }

    /**
     * Test to ensure that a partnership returns false if there are zero
     * affected rows
     */
    public function testPartnershipDeletedNoAffectedRows()
    {
        $id = '12345678';
        $query = "DELETE FROM partnership WHERE id = :id";
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('execute')
            ->with(['id' => $id])
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(0));
        $partnershipManager = new PartnershipManager($this->db);
        $result = $partnershipManager->delete($id);
        $this->assertFalse($result);
    }

    /**
     * Test to ensure false is returned when PDO throws an exception
     */
    public function testDeleteReturnsFalseOnException()
    {
        $id = 12345;
        $this->db->expects($this->once())
            ->method('prepare')
            ->will($this->throwException(new \PDOException));
        $partnershipManager = new PartnershipManager($this->db);
        $result = $partnershipManager->delete($id);
        $this->assertFalse($result);
    }
}

