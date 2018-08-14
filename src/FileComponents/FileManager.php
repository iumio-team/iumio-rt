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


namespace Mercure\FileComponents;
use Mercure\Pattern\SingletonPattern;

/**
 * Class FileManager
 * @package Mercure\FileComponents
 * @category Routing
 * @licence  MIT License
 * @link https://mercurue.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class FileManager extends SingletonPattern
{
    /** Create an element on the server
     * @param string $path Element Path
     * @param string $type Element type
     * @return int Result
     * @throws \Exception Generate Error
     */
    public function create(string $path, string $type):int
    {
        try {
            switch ($type) {
                case "directory":
                    if (!is_dir($path)) {
                        mkdir($path);
                    }
                    break;
                case "file":
                    if (!is_file($path)) {
                        touch($path);
                    }
                    break;
            }
        } catch (\Exception $exception) {
            throw new \Exception("FileManager : Cannot create $type element => ".$exception);
        }

        return (1);
    }

    /** Move an element on the server
     * @param string $path Element Path
     * @param string $to Move to
     * @param bool $symlink Is symlink
     * @return int Result
     * @throws \Exception Generate Error
     */
    public function move(string $path, string $to, bool $symlink = false):int
    {
        try {
            if ($symlink != false) {
                symlink($path, $to);
            } else {
                rename($path, $to);
            }
        } catch (\Exception $exception) {
            throw new \Exception("FileManager : Cannot move $path to $to => ".$exception);
        }

        return (1);
    }


    /** Copy an element on the server
     * @param string $path Element Path
     * @param string $to Move to
     * @param string $type Element type
     * @param bool $symlink Is symlink
     * @return int Result
     * @throws \Exception Generate Error
     */
    public function copy(string $path, string $to, string $type, bool $symlink = false):int
    {
        try {
            if ($symlink != false) {
                @symlink($path, $to);
            } elseif ($symlink == false && $type == "directory") {
                $this->recursiveCopy($path, $to);
            } elseif ($symlink == false && $type == "file") {
                copy($path, $to);
            } else {
                throw new \Exception("FileManager on Copy: Element type is not regonized");
            }
        } catch (\Exception $exception) {
            throw new \Exception("FileManager : Cannot move $path to $to => ".$exception);
        }

        return (1);
    }

    /** Check if an element existed on the server
     * @param string $path Element Path
     * @return bool If element exist
     */
    public function exist(string $path):bool
    {
        return (file_exists($path));
    }

    /** Delete an element on the server
     * @param string $path Element Path
     * @param string $type Element type
     * @return int Result
     * @throws \Exception Generate Error
     */
    public function delete(string $path, string $type):int
    {
        try {
            switch ($type) {
                case "directory":
                    if (is_link($path)) {
                        unlink($path);
                    } elseif (is_dir($path)) {
                        try {
                            $this->recursiveRmdir($path);
                        } catch (\Exception $e) {
                            throw new \Exception("FileManager delete error =>" . $e->getMessage());
                        }
                    }
                    break;
                case "file":
                    if (is_link($path)) {
                        unlink($path);
                    } elseif (is_file($path)) {
                        try {
                            unlink($path);
                        } catch (\Exception $e) {
                            throw new \Exception("FileManager delete error =>" . $e->getMessage());
                        }
                    }
                    break;
            }
        } catch (\Exception $exception) {
            throw new \Exception("FileManager : Cannot delete $type element => ".$exception);
        }

        return (1);
    }

    /** Recursive remove directory
     * @param string $dir dir path
     */
    private function recursiveRmdir(string $dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        $this->recursiveRmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /** Copy directory recursivly
     * @param string $src directory source
     * @param string $dst directory destination
     */
    private function recursiveCopy(string $src, string $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    $this->recursiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /** Check if element is readable
     * @param string $path Element path
     * @return bool Is element is readable or not
     */
    public function checkIsReadable(string $path):bool
    {
        return (is_readable($path));
    }


    /** Check if element is executable
     * @param string $path Element path
     * @return bool Is element is executable or not
     */
    public function checkIsExecutable(string $path):bool
    {
        return (is_executable($path));
    }

    /** Check if element is writable
     * @param string $path Element path
     * @return bool Is element is writable or not
     */
    public function checkIsWritable(string $path):bool
    {
        return (is_writable($path));
    }
}
