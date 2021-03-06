<?php
/**
 * ReST controllers must implement RestApi
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */
namespace Stream\Interfaces;
interface RestApi {
    public function get();
    public function post();
    public function delete();
}
