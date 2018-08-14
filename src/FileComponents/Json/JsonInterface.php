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

/**
 * Interface
 * @package Mercure\Json
 * @category Routing
 * @licence  MIT License
 * @link https://mercure.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

interface JsonInterface
{

    /** Read a json file
     * @param string $path File path
     * @param bool $create Create the file if does not exist
     * @throws \Exception
     */
    public function parse(string $path, bool $create = false): void;

    /** Get a element on content
     * @param string $key Element key
     * @return null|mixed The result (if not isset or all content is null, return null)
     */
    public function get(string $key);


    /** Return the file content
     * @return array|null If not empty : is an array
     */
    public function getContent(): ?array;

    /** Set an new content
     * @param array|null $content Array if not empty or null if empty content
     */
    public function setContent(?array $content): void;


    /** Override a json file content
     * @param bool $prettyPrint If use a pretty print format to override it
     * @return bool If file is override
     * @throws \Exception
     */
    public function override(bool $prettyPrint = false): bool;

    /** Clear the Json Reader object
     * @return bool If the Json Reader object is empty
     */
    public function clearObject(): bool;
}
