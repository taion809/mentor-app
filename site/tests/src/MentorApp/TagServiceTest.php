<?php
/**
 * @author PHPMentoring
 * @licence http://opensource.org/licences/MIT MIT
 * @package MentorApp
 */

namespace MentorApp;

/**
 * Temporarily here. There is a real implementation in the user branch.
 */
class PDOTestHelper extends \PDO
{
    public function __construct() {}
}

class TagServiceTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->db = $this->getMock('\\MentorApp\\PDOTestHelper', array('prepare'));
        $this->statement = $this->getMock('\PDOStatement', array('execute', 'fetch', 'fetchAll', 'rowCount'));
    }

    /**
     * @test
     */
    public function retrieveReturnsPopulatedTagEntity()
    {
        $tagData = array(
            'name' => 'Testing',
            'added' => '2013-10-01 09:34:03',
            'authorized' => true,
        );
        $this->statement->expects($this->once())
            ->method('execute');

        $this->statement->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(1));

        $this->statement->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($tagData));

        $this->db->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue($this->statement));

        $service = new TagService($this->db);
        $tag = $service->retrieve($tagData['name']);

        $this->assertEquals($tagData['name'], $tag->name);
        $this->assertTrue($tag->authorized);
    }

    /**
     * @test
     */
    public function retrieveWithEmptyNameParamThrowsException()
    {
        $this->setExpectedException('\\InvalidArgumentException');
        $service = new TagService($this->db);
        $service->retrieve('');
    }


    /**
     * @test
     */
    public function retrieveReturnsNullIfTagNotFound()
    {
        $this->statement->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(0));

        $this->db->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue($this->statement));

        $service = new TagService($this->db);

        $this->assertNull($service->retrieve('Tag Does Not Exist'));
    }

    /** @test */
    public function searchByTermReturnsArrayOfTagsMatchingTerm()
    {
        $tagData = array(
            array('name' => 'Test Tag 1', 'added' => '2013-10-01', 'authorized' => 1),
            array('name' => 'Test Tag 2', 'added' => '2013-10-02', 'authorized' => 1),
            array('name' => 'Test Tag 3', 'added' => '2013-10-03', 'authorized' => 0),
        );

        $this->statement->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(count($tagData)));

        $this->db->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue($this->statement));

        $this->statement->expects($this->at(2))
            ->method('fetch')
            ->will($this->returnValue($tagData[0]));

        $this->statement->expects($this->at(3))
            ->method('fetch')
            ->will($this->returnValue($tagData[1]));

        $this->statement->expects($this->at(4))
            ->method('fetch')
            ->will($this->returnValue($tagData[2]));

        $service = new TagService($this->db);
        $tags = $service->searchByTerm('Test');

        $this->assertCount(count($tagData), $tags);
        $this->assertEquals($tagData[0]['name'], $tags[0]->name);
    }

    /** @test */
    public function searchByTermThrowsExceptionForEmptySearchTerm()
    {
        $this->setExpectedException('\\InvalidArgumentException');
        $service = new TagService($this->db);
        $service->searchByTerm('');
    }

    /** @test */
    public function searchByTermReturnsEmptyArrayWhenNoMatchingTagsFound()
    {
        $this->statement->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(0));

        $this->db->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue($this->statement));

        $service = new TagService($this->db);
        $tags = $service->searchByTerm('Unused Term');
        $this->assertEmpty($tags);
    }

    /** @test */
    public function saveThrowsExceptionForNamelessTag()
    {
        $tag = $this->getMock('\\MentorApp\\Tag');
        $tag->name = '';

        $this->setExpectedException('\\InvalidArgumentException');
        $service = new TagService($this->db);
        $service->save($tag);
    }
}
