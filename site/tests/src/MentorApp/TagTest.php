<?php
/**
 * @author Stevan Goode <stevan@stevangoode.com>
 * @licence http://opensource.org/licences/MIT MIT
 * @package MentorApp
 */

namespace MentorApp;

class TagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that we can set things as we expect to be able to
     */
    public function testCanSet()
    {
        $tag = new Tag();
        $tag->id = 'abcdef1234';
        $tag->name = 'test123';
        $tag->authorized = true;
        $tag->added = new \DateTime('2013-10-19 20:38:01');

        $this->assertEquals(
            ['id' => 'abcdef1234', 'name' => 'test123', 'authorized' => true, 'added' => new \DateTime('2013-10-19 20:38:01')],
            get_object_vars($tag)
        );
    }
}
