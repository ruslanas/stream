<?php

/**
 * RestResource
 *
 * PHP version 5
 *
 * @category ReST
 * @package  Stream_Traits
 * @author   Ruslanas Balciunas <ruslanas.com@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/ruslanas/stream
 */
namespace Stream\Traits;

use Stream\Exception\UnknownMethodException;

/**
 * RestResource
 *
 * @category ReST
 * @package  Stream_Traits
 * @author   Ruslanas Balciunas <ruslanas.com@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/ruslanas/stream
 */
trait RestResource
{
    /**
     * Undocumented function
     *
     * @return mixed
     * 
     * @throws UnknownMethodException
     */
    public function delete()
    {
        throw new UnknownMethodException("Method Not Allowed");
    }

    /**
     * Undocumented function
     *
     * @return mixed
     * 
     * @throws UnknownMethodException
     */
    public function post()
    {
        throw new UnknownMethodException("Method Not Allowed");
    }

    /**
     * Undocumented function
     *
     * @return mixed
     * 
     * @throws UnknownMethodException
     */
    public function get()
    {
        throw new UnknownMethodException("Method Not Allowed");
    }

    /**
     * Undocumented function
     *
     * @return mixed
     * 
     * @throws UnknownMethodException
     */
    final public function head()
    {
        $this->get();
        return null;
    }

}