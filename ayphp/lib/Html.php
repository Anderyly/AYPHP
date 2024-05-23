<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2024
 */

namespace ay\lib;

class Html
{
    public static function title($title)
    {
        return "<title>$title</title>\n";
    }

    public static function meta($name, $value)
    {
        if ($name == 'charset') return "<meta charset='$value'>\n";
        return "<meta name='$name' content='$value'>\n";
    }

    public static function favicon($url)
    {
        return "<link rel='icon' href='$url' type='image/x-icon' />\n<link rel='shortcut icon' href='$url' type='image/x-icon' />\n";
    }

    public static function icon($name, $class = '')
    {
        $class = empty($class) ? ('icon-' . $name) : ('icon-' . $name . ' ' . $class);
        return "<i class='$class'></i>";
    }

    public static function rss($url, $title = '')
    {
        return "<link href='$url' title='$title' type='application/rss+xml' rel='alternate' />";
    }

    public static function a($href = '', $title = '', $id = '', $class = '', $attr = '')
    {
        $str = "<a href={$href}";

        if (!empty($id)) {
            $str .= ' id="' . $id . '"';
        }

        if (!empty($class)) {
            $str .= ' class="' . $class . '"';
        }

        $str .= " {$attr}>" . $title . '</a>';
        return $str;
    }


}