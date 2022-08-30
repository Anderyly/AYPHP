<?php

namespace ay\drive;

class Url {

    public static function instance(): Url
    {
        return new self();
    }

    public function get($str, $data = null): string
    {
        $arr = explode('/', trim($str, '/'));
        $len = count($arr);

        if ($len == 3) {
            $mode = $arr[0];
        } else {
            $mode = MODE;
        }

        if (defined('BIND')) {
            $mode = match (true) {
                $len == 3 => $arr[0],
                BIND == MODE => CIND . '.php',
                default => MODE,
            };
        }

        $path = match ($len) {
            3 => URL . '/' . $mode . '/' . $arr[1] . '/' . $arr[2],
            2 => URL . '/' . $mode . '/' . $arr[0] . '/' . $arr[1],
            default => URL . '/' . $mode . '/' . CONTROLLER . '/' . $arr[0],
        };

        $path .= '.' . C('REWRITE');

        if (is_array($data)) {
            $path .= '?';
            foreach ($data as $k => $v) {
                $path .= $k . '=' . $v . '&';
            }
            $path = rtrim($path, '&');
        } else {
            $path .= $data;
        }

        return $path;

    }
}