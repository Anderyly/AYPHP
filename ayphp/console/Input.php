<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2024
 */

declare (strict_types=1);

namespace ay\console;

class Input
{

    /**
     * @var mixed|null
     */
    private static $tokens;

    public function __construct($argv = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
            // 去除命令名
            array_shift($argv);
        }

        self::$tokens = $argv;


    }

    public function get()
    {
        return self::$tokens;
    }

}
