<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp
 */
namespace MentorApp;

/**
 * Interface used for formatting API output
 */
interface SerializerInterface
{
    /**
     * fromArray converts an array to a value array
     * 
     * @param array $values an array of properties to create an object
     * @return mixed instance of a value object
     */
    public function fromArray(Array $values);

    /**
     * toArray converts a value object to an array
     *
     * @param mixed $valueObject value object to convert
     * @return array an array of values representing the value object
     */
    public function toArray($valueObject); 
}
