<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */
namespace MentorApp;

class PartnershipSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup method - runs before each test
     */
    public function setUp()
    {
        $this->serializer = new PartnershipArraySerializer();
    }

    /**
     * Teardown method - runs after each test
     */
    public function tearDown()
    {
        unset($this->serializer);
    }

    /**
     * Test to ensure a partnership method returns an array of partnership data
     */
    public function testPartnershipInstanceReturnsArray()
    {
        $partnership = new Partnership();
        $partnership->id = 'bdef3421de';
        $partnership->mentorId = 'bbbce21212';
        $partnership->apprenticeId = 'cbdea12dee3';

        $partnerArray = $this->serializer->toArray($partnership);
        $this->assertEquals($partnerArray['id'], $partnership->id);
    }

    /**
     * Test to ensure when a non-partnership instance is passed in an empty array
     * is returned
     */
    public function testDifferentInstanceReturnsEmptyArray()
    {
        $partnership = new User();
        $array = $this->serializer->toArray($partnership);
        $this->assertEmpty($array);
    }

    /**
     * Test to ensure an array with partnership data is converted into a
     * Partnership instance
     */
    public function testPartnershipArrayReturnsPartnershipInstance()
    {
        $partnershipArray = array(
            'id' => '12dedabce3',
        );

        $partnership = $this->serializer->fromArray($partnershipArray);
        $this->assertEquals($partnership->id, $partnershipArray['id']);
        $this->assertNull($partnership->mentor);
        $this->assertNull($partnership->apprentice);
    }
}
