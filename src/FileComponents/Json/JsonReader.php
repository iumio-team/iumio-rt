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

namespace Mercure\FileComponents\Json;
use Mercure\FileComponents\FileManager;
use Mercure\Pattern\SingletonPattern;

/**
 * Class JsonReader
 * @package Mercure\Json
 * @category Routing
 * @licence  MIT License
 * @link https://mercure.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class JsonReader extends SingletonPattern implements JsonInterface
{
    protected $content = null;
    protected $path = null;

    /** Read a json file
     * @param string $path File path
     * @param bool $create Create the file if does not exist
     * @throws \Exception
     */
    public function parse(string $path, bool $create = false):void {
        if (null === $this->path) {
            if (false === $create && !file_exists($path)) {
                throw new \Exception("Mercure Reader : Cannot open file $path".
                    " --> File does not exist : Please set the correct filepath");
            }
            if (false === $create && !is_readable($path)) {
                throw new \Exception("Mercure BadPermission : Cannot open file $path".
                    " --> File not readable. Please set the correct permission");
            }

            if (false === $create && false === $this->formatIsValid($path)){
                throw new \Exception("Mercure Parse : ".json_last_error_msg());
            }

            $this->createJson($path);
            $raw = (true === $create)? "" : json_decode(file_get_contents($path));
            $this->content = ("" === $raw || empty($raw)) ? [] : (array)$raw;
            $this->path = $path;
        }
    }

    /** Get a element on content
     * @param string $key Element key
     * @return null|mixed The result (if not isset or all content is null, return null)
     */
    public function get(string $key)
    {
       return (null === $this->content)? null : ($this->content[$key] ??  null);
    }


    /** Create a json file
     * @param string $path File path
     * @return bool If file was created or not
     */
    public function createJson(string $path):bool {
        $instance = FileManager::getInstance();
        if ($instance->exist($path)) {
            return (true);
        }
        $instance->create($path, "file");
        return ($instance->exist($path));
    }


    /** Return the file content
     * @return array|null If not empty : is an array
     */
    public function getContent():?array
    {
        return ($this->content);
    }

    /** Set an new content
     * @param array|null $content Array if not empty or null if empty content
     */
    public function setContent(?array $content):void
    {
        $this->content = $content;
    }

    /** Check if this file is a valid JSON format
     * @param string $path The file path
     * @return bool If an error was generated
     */
    public function formatIsValid(string $path): bool {
        json_decode(file_get_contents($path));
        return (json_last_error() == JSON_ERROR_NONE);
    }


    /** Override a json file content
     * @param bool $prettyPrint If use a pretty print format to override it
     * @return bool If file is override
     * @throws \Exception
     */
    public function override(bool $prettyPrint = false):bool {
        $instance = FileManager::getInstance();
        if (null === $this->path) {
            throw new \Exception("Mercure BadFilePath : Path is empty --> Cannot override any file");
        }
        if (!$instance->exist($this->path)) {
            throw new \Exception("Mercure FileNotFound : File $this->path does not exist");
        }

        $prettyPrint = (false === $prettyPrint)? 0 : JSON_PRETTY_PRINT;
        file_put_contents($this->path, (null === $this->content)? json_encode([],
            $prettyPrint) : json_encode($this->content, $prettyPrint));
        return (true);
    }

	/** Remove the current json file
	 * @return bool If it is removed
	 * @throws \Exception
	 */
    public function remove():bool {
		$instance = FileManager::getInstance();
		if (null === $this->path) {
			throw new \Exception("Mercure BadFilePath : Path is empty --> Cannot remove".
				" a file which not exist");
		}
		$instance->delete($this->path, "file");
		if (!$instance->exist($this->path)) {
			$this->clearObject();
			return (true);
		}
		throw new \Exception("Mercure Unexpected : File ".$this->path.
			" has not removed properly");
	}

    /** Clear the Json Reader object
     * @return bool If the Json Reader object is empty
     */
    public function clearObject():bool {
        $this->content = null;
        $this->path = null;
        return (true);
    }

	/**
	 * Clear the file
	 * @throws \Exception
	 */
	public function clear():void {
		$this->content = [];
		$this->override();
	}
}
