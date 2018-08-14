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
use PHPUnit\Framework\TestCase;

/**
 * Class FileManagerTest
 * Test class for FileManager
 * @package Mercure\Tests\File
 * @category Routing
 * @licence  MIT License
 * @link https://mercurue.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class FileManagerTest extends TestCase
{

    /**
     * Create an instance of FileManager (test for SingletonPattern)
     */
    public function testInstance()
    {
        $instance1 = FileManager::getInstance();
        $instance2 = FileManager::getInstance();
        $this->assertEquals($instance1, $instance2);
    }

    /**
     * Create directory and file
     */
    public function testCreate()
    {
        $i = FileManager::getInstance();
        //$i = new FileManager();
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "fic", "directory"));
        $this->assertTrue(is_dir(__DIR__ . DIRECTORY_SEPARATOR . "fic"));
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "fic/hello.txt", "file"));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "fic/hello.txt"));
    }

    /**
     * Move directory empty and not empty
     * Move file
     */
    public function testMove()
    {
        $i = FileManager::getInstance();
        $this->assertEquals(1, $i->move(
            __DIR__ . DIRECTORY_SEPARATOR . "fic",
            __DIR__ . DIRECTORY_SEPARATOR . "foc"
        ));
        $this->assertEquals(1, $i->move(
            __DIR__ . DIRECTORY_SEPARATOR . "foc/hello.txt",
            __DIR__ . DIRECTORY_SEPARATOR . "foc/hi.txt"
        ));
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "moved", "directory"));
        $this->assertEquals(1, $i->move(
            __DIR__ . DIRECTORY_SEPARATOR . "foc/hi.txt",
            __DIR__ . DIRECTORY_SEPARATOR . "moved/hi.txt"
        ));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "moved/hi.txt"));
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "ini-moved", "directory"));
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "ini-moved/disallow.txt", "file"));
        $this->assertEquals(1, $i->move(
            __DIR__ . DIRECTORY_SEPARATOR . "ini-moved",
            __DIR__ . DIRECTORY_SEPARATOR . "moved/ini-moved"
        ));
        $this->assertTrue(is_dir(__DIR__ . DIRECTORY_SEPARATOR . "moved/ini-moved"));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "moved/ini-moved/disallow.txt"));
    }


    /**
     * Copy directory empty or not and symlink or not
     * Copy file symlink or not
     */
    public function testCopy()
    {
        $i = FileManager::getInstance();
        $this->assertEquals(1, $i->copy(
            __DIR__ . DIRECTORY_SEPARATOR . "foc",
            __DIR__ . DIRECTORY_SEPARATOR . "moved/foc",
            "directory"
        ));
        $this->assertTrue(is_dir(__DIR__ . DIRECTORY_SEPARATOR . "moved/foc"));
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "blue.txt", "file"));
        $this->assertEquals(1, $i->copy(
            __DIR__ . DIRECTORY_SEPARATOR . "blue.txt",
            __DIR__ . DIRECTORY_SEPARATOR . "moved/blue.txt",
            "file"
        ));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "moved/blue.txt"));
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "oncopy", "directory"));
        $this->assertTrue(is_dir(__DIR__ . DIRECTORY_SEPARATOR . "oncopy"));
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "oncopy/red.txt", "file"));
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "oncopy/orange.txt", "file"));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "oncopy/red.txt"));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "oncopy/orange.txt"));
        $this->assertEquals(1, $i->copy(
            __DIR__ . DIRECTORY_SEPARATOR . "oncopy",
            __DIR__ . DIRECTORY_SEPARATOR . "moved/oncopy",
            "directory"
        ));
        $this->assertTrue(is_dir(__DIR__ . DIRECTORY_SEPARATOR . "moved/oncopy"));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "moved/oncopy/red.txt"));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "moved/oncopy/orange.txt"));
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "oncopysyms", "directory"));
        $this->assertTrue(is_dir(__DIR__ . DIRECTORY_SEPARATOR . "oncopysyms"));
        $this->assertEquals(1, $i->copy(
            __DIR__ . DIRECTORY_SEPARATOR . "oncopysyms",
            __DIR__ . DIRECTORY_SEPARATOR . "moved/oncopysyms",
            "directory",
            true
        ));
        $this->assertTrue(is_link(__DIR__ . DIRECTORY_SEPARATOR . "moved/oncopysyms"));

        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "iumio.txt", "file"));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "iumio.txt"));
        $this->assertEquals(1, $i->copy(
            __DIR__ . DIRECTORY_SEPARATOR . "iumio.txt",
            __DIR__ . DIRECTORY_SEPARATOR . "moved/iumio.txt",
            "file",
            true
        ));
        $this->assertTrue(is_link(__DIR__ . DIRECTORY_SEPARATOR . "moved/iumio.txt"));
    }

    /**
     * Check if directory or file exist
     */
    public function testExist()
    {
        $i = FileManager::getInstance();
        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "iumia", "directory"));
        $this->assertEquals(
            is_dir(__DIR__ . DIRECTORY_SEPARATOR . "iumia"),
            $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "iumia")
        );
        $this->assertEquals(
            file_exists(__DIR__ . DIRECTORY_SEPARATOR . "iumia"),
            $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "iumia")
        );

        $this->assertEquals(1, $i->create(__DIR__ . DIRECTORY_SEPARATOR . "iumiu.txt", "file"));
        $this->assertEquals(
            file_exists(__DIR__ . DIRECTORY_SEPARATOR . "iumiu.txt"),
            $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "iumiu.txt")
        );

        $this->assertEquals(
            file_exists(__DIR__ . DIRECTORY_SEPARATOR . "iumiw.txt"),
            $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "iumiw.txt")
        );
        $this->assertEquals(
            is_dir(__DIR__ . DIRECTORY_SEPARATOR . "iumix"),
            $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "iumix")
        );
    }

    /**
     * Delete any file or directory (empty or not)
     */
    public function testDelete()
    {
        $i = FileManager::getInstance();
        $this->assertEquals(1, $i->delete(__DIR__ . DIRECTORY_SEPARATOR . "iumia", "directory"));
        $this->assertEquals(false, $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "iumia"));

        $this->assertEquals(1, $i->delete(__DIR__ . DIRECTORY_SEPARATOR . "foc", "directory"));
        $this->assertEquals(false, $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "foc"));

        $this->assertEquals(1, $i->delete(__DIR__ . DIRECTORY_SEPARATOR . "moved", "directory"));
        $this->assertEquals(false, $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "moved"));

        $this->assertEquals(1, $i->delete(__DIR__ . DIRECTORY_SEPARATOR . "oncopy", "directory"));
        $this->assertEquals(false, $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "oncopy"));

        $this->assertEquals(1, $i->delete(__DIR__ . DIRECTORY_SEPARATOR . "oncopysyms", "directory"));
        $this->assertEquals(false, $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "oncopysyms"));

        $this->assertEquals(1, $i->delete(__DIR__ . DIRECTORY_SEPARATOR . "blue.txt", "file"));
        $this->assertEquals(false, $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "blue.txt"));

        $this->assertEquals(1, $i->delete(__DIR__ . DIRECTORY_SEPARATOR . "iumio.txt", "file"));
        $this->assertEquals(false, $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "iumio.txt"));

        $this->assertEquals(1, $i->delete(__DIR__ . DIRECTORY_SEPARATOR . "iumiu.txt", "file"));
        $this->assertEquals(false, $i->exist(__DIR__ . DIRECTORY_SEPARATOR . "iumiu.txt"));
    }
}
