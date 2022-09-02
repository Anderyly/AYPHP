<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

use ay\lib\Db;

class Model extends Db
{
    public function __construct()
    {
        $option = C('DATABASE');
        parent::__construct($option);
    }

    public static function instance()
    {
        $obj = "\\" . get_called_class();
        return new $obj;
    }

}
