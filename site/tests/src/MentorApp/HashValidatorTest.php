<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */
namespace MentorApp;

/**
 * Tests for the hash validation
 */
class HashValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup method - called before each test
     */
    public function setUp()
    {
        $this->validator = new HashValidator();
    }

    /**
     * Teardown method - called after each test
     */
    public function tearDown()
    {
        unset($this->validator);
    }

    /**
     * Test to ensure a hash validates correctly
     */
    public function testEnsureHashValidatesCorrectly()
    {
        $hash = 'cde431ca12';
        $expectedPattern = '/^[0-9a-f]{10}$/';
        $this->assertTrue($this->validator->validate($hash));
        $this->assertEquals($expectedPattern, $this->validator->getPattern());
    }

    /**
     * Test to ensure hash validate returns false with bad hash
     */
    public function testEnsureHashFailsCorrectly()
    {
        $hash = 'cd3123';
        $expectedPattern = '/^[0-9a-f]{10}$/';
        $this->assertFalse($this->validator->validate($hash));
        $this->assertEquals($expectedPattern, $this->validator->getPattern());
    }
}
