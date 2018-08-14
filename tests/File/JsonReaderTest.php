<?php

/*
 *
 *  This is a mercure component
 *
 *  (c) RAFINA DANY <dany.rafina@iumio.com>
 *
 *  iumio Mercure, an iumio component [https://iumio.com] [https://mercure.iumio.com]
 *
 *  To get more information about licence, please check the licence file
 *
*/

namespace Mercure\Tests\File;

use Mercure\FileComponents\FileManager;
use Mercure\FileComponents\Json\JsonReader;
use PHPUnit\Framework\TestCase;

class JsonReaderTest extends TestCase
{
    /**
     * Create an instance of JsonReader (test for SingletonPattern)
     */
    public function testInstance()
    {
        $instance1 = JsonReader::getInstance();
        $instance2 = JsonReader::getInstance();
        $this->assertSame($instance2, $instance1);
    }

    /**
     * Create a JSON file
     */
    public function testCreate()
    {
        $instance = JsonReader::getInstance();
        $instance->parse(__DIR__.DIRECTORY_SEPARATOR."hello.json", true);
        $fim = FileManager::getInstance();
        $this->assertTrue($fim->exist(__DIR__.DIRECTORY_SEPARATOR."hello.json"));
    }

    /**
     * Write into a JSON file
     */
    public function testWrite()
    {
        $instance = JsonReader::getInstance();
        $instance->parse(__DIR__.DIRECTORY_SEPARATOR."hello.json");
        $content = $instance->getContent();
        $this->assertTrue(is_array($content));
        $content = ["hello" => "world"];
        $instance->setContent($content);
        $this->assertSame($instance->getContent(), ["hello" => "world"]);
        $this->assertTrue($instance->override(true));
        $this->assertTrue($instance->clearObject());
        $this->assertNull($instance->getContent());
    }

    /**
     * Read each element on a JSON with key
     */
    public function testRead()
    {
        $instance = JsonReader::getInstance();
        $instance->parse(__DIR__.DIRECTORY_SEPARATOR."hello.json");
        $content = $instance->getContent();
        $this->assertTrue(is_array($content));
        $this->assertEquals("world", $instance->get("hello"));
        $this->assertNull($instance->get("hi"));
    }

    /**
     * Use Singleton pattern to retreive the object
     */
    public function testRestore()
    {
        $instance = JsonReader::getInstance();
        $this->assertTrue(is_array($instance->getContent()));
        $content = $instance->getContent();
        $this->assertTrue(is_array($content));
        $this->assertEquals("world", $instance->get("hello"));
    }

    /**
     * Clear the file (empty it)
     */
    public function testClear()
    {
        $instance = JsonReader::getInstance();
        $instance->clear();
        $this->assertEquals(
            [],
            (array)json_decode(file_get_contents(__DIR__.DIRECTORY_SEPARATOR."hello.json"))
        );
    }

    /**
     * Delete the JSON file create recently
     */
    public function testRemove()
    {
        $instance = JsonReader::getInstance();
        $this->assertTrue($instance->remove());
        $this->assertFalse(file_exists(__DIR__.DIRECTORY_SEPARATOR."hello.json"));
    }
}
