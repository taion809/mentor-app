<?php
/**
 * @author PHPMentoring
 * @licence http://opensource.org/licences/MIT MIT
 * @package MentorApp
 */

namespace MentorApp;

class TagServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup mock dependencies for the test
     */
    protected function setUp()
    {
        $this->db = $this->getMock('\PDOTestHelper', array('prepare'));
        $this->statement = $this->getMock('\PDOStatement', array('execute', 'fetch', 'fetchAll', 'rowCount'));
    }

    /**
     * Tear Down the mock dependencies for the class
     */
    protected function tearDown()
    {
       unset($this->db);
       unset($this->statement);
    } 

    /**
     * Test to ensure that the retrieve method returns a populate
     * tag object when a tag matching the search term exists
     */
    public function testRetrieveReturnsPopulatedTagEntity()
    {
        $tagData = array(
            'id' => null,
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
     * Test to ensure an InvalidArgumentException is thrown when the search
     * term is an empty string
     */
    public function testRetrieveWithEmptyNameParamThrowsException()
    {
        $this->setExpectedException('\\InvalidArgumentException');
        $service = new TagService($this->db);
        $service->retrieve('');
    }

    /**
     * Test to ensure that null is return if no tag can be found by with
     * the search term
     */
    public function testRetrieveReturnsNullIfTagNotFound()
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

    /**
     * Test to ensure that a search with the correct term will return
     * the approriate Tag objects
     */
    public function testSearchByTermReturnsArrayOfTagsMatchingTerm()
    {
        $tagData = array(
            array('id' => 'abc123def4', 'name' => 'Test Tag 1', 'added' => '2013-10-01', 'authorized' => 1),
            array('id' => 'abad1f567e', 'name' => 'Test Tag 2', 'added' => '2013-10-02', 'authorized' => 1),
            array('id' => 'fe12cdab7e', 'name' => 'Test Tag 3', 'added' => '2013-10-03', 'authorized' => 0),
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
        $this->assertEquals($tagData[1]['id'], $tags[1]->id);
        $this->assertEquals($tagData[2]['id'], $tags[2]->id);
    }

    /**
     * Test to ensure that an empty term cannot be searched for, expects
     * an InvalidArgumentException
     */
    public function testSearchByTermThrowsExceptionForEmptySearchTerm()
    {
        $this->setExpectedException('\\InvalidArgumentException');
        $service = new TagService($this->db);
        $service->searchByTerm('');
    }

    /**
     * Test to ensure that an empty array is return when no matching
     * tags are found
     */
    public function testSearchByTermReturnsEmptyArrayWhenNoMatchingTagsFound()
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

    /**
     * Test to ensure that tag passed in without a name throws an 
     * InvalidArgumentException
     */ 
    public function testSaveThrowsExceptionForNamelessTag()
    {
        $tag = $this->getMock('\\MentorApp\\Tag');
        $tag->name = '';

        $this->setExpectedException('\\InvalidArgumentException');
        $service = new TagService($this->db);
        $service->save($tag);
    }

    /**
     * Test to ensure the save method functions properly when the Tag has an ID set
     */
    public function testEnsureSaveMethodUpdatesTable()
    {
        $tag = new Tag();
        $tag->id = 'ab12e4bb12';
        $tag->name = 'PHP';
        $tag->added = '2013-11-18 20:58:12';
        $tag->authorized = true;

        $query = 'INSERT INTO `tag` (
                `id`,
                `name`,
                `authorized`,
                `added`
            ) VALUES (
                :id,
                :name,
                :authorized,
                :added
            ) ON DUPLICATE KEY UPDATE
                `authorized` = :authorized
            ';
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->will($this->returnValue($this->statement));

        $this->statement->expects($this->once())
            ->method('execute')
            ->with(['id' => $tag->id, 'name' => $tag->name, 'authorized' => $tag->authorized, 'added' => $tag->added])
            ->will($this->returnValue($this->statement));
        $tagService = new TagService($this->db);
        $savedTag = $tagService->save($tag);
        $this->assertTrue($savedTag, 'Save method returned false');
    }

    /**
     * Test to ensure that a tag with no id has an ID created and the save
     * completes correctly
     */
    public function testEnsureThatNewObjectIsCreatedWithNoIdValue()
    {
        $tag = new Tag();
        $tag->name = 'OOP';
        $tag->authorized = true;
        $tag->added = '2013-11-18 21:18:30';

        $query = 'INSERT INTO `tag` (
                `id`,
                `name`,
                `authorized`,
                `added`
            ) VALUES (
                :id,
                :name,
                :authorized,
                :added
            ) ON DUPLICATE KEY UPDATE
                `authorized` = :authorized
            ';
        $this->db->expects($this->at(0))
            ->method('prepare')
            ->with('SELECT id FROM `tags` WHERE id = :id')
            ->will($this->returnValue($this->statement)); 
        $this->db->expects($this->at(1))
            ->method('prepare')
            ->with($query)
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->at(0))
            ->method('execute')
            ->with($this->isType('array'))
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->at(2))
            ->method('execute')
            ->with($this->isType('array'))
            ->will($this->returnValue($this->statement));
        $this->statement->expects($this->once())
            ->method('rowCount')
            ->will($this->returnValue(0));

        $tagService = new TagService($this->db);
        $tagReturn = $tagService->save($tag);
        $this->assertTrue($tagReturn);
    }

    /**
     * Test to ensure that when a \PDO Exception is thrown in save when
     * updating a tag entry returns false
     */
    public function testSaveReturnsFalseOnPDOException()
    {
        $tag = new Tag();
        $tag->id = '123edf23ea';
        $tag->name = 'TDD';
        $tag->authorized = false;
        $tag->added = '2013-11-19 13:56:12';

        $query = 'INSERT INTO `tag` (
                `id`,
                `name`,
                `authorized`,
                `added`
            ) VALUES (
                :id,
                :name,
                :authorized,
                :added
            ) ON DUPLICATE KEY UPDATE
                `authorized` = :authorized
            ';
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->will($this->returnValue($this->statement));
 
        $this->statement->expects($this->once())
            ->method('execute')
            ->will($this->throwException(new \PDOException));
            
        $tagService = new TagService($this->db);
        $tagReturn = $tagService->save($tag);
        $this->assertFalse($tagReturn);
    }

    /**
     * Test to ensure that when a PDO exeception is encountered in exists and
     * throws a \RuntimeException
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Rut Roh! Something terrible happened and we couldn't fix it...
     */
    public function testEnsureRuntimeExceptionIsThrownOnPDOExceptionInExists()
    {
        $tag = new Tag();
        $tag->name = 'TDD';
        $tag->authorized = true;
        $tag->added = '2013-11-18 21:25:30';

        $this->db->expects($this->once())
            ->method('prepare')
            ->will($this->throwException(new \PDOException));

        $tagService = new TagService($this->db);
        $tagReturn = $tagService->save($tag);
    }
}
