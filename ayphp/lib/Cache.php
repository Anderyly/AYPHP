<?php
/**
 * 文本缓存类
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2019
 */

namespace ay\lib;

use ay\lib\Dir;

class Cache
{

    private string $path = TEMP . "cache/data/";

    /***
     * @return Cache
     */
    public static function instance(): Cache
    {
        if (!file_exists(TEMP . "cache/data/")) {
            mkdir(TEMP . "cache/data/", 0777, true);
        }
        return new self();
    }

    /***
     * @param string $name
     * @return array|string|null
     */
    public function get(string $name = "")
    {
        if (empty($name)) $name = MODE . "_" . CONTROLLER . "_" . ACTION;
        $path = $this->path . $name . ".txt";
        $fileT = @filemtime($path);
        if (!file_exists($path)) {
            file_put_contents($path, '{}');
        }
        return json_decode(file_get_contents($path), true);

    }

    /***
     * @param string $name
     * @param array|string $data
     * @return bool
     */
    public function set(string $name = '', array|string $data = ''): bool
    {
        if (empty($name)) $name = MODE . "_" . CONTROLLER . "_" . ACTION;
        if (!C('APP.CACHE')) {
            return false;
        }
        $path = $this->path . $name . ".txt";
        $data = json_encode($data);
        file_put_contents($path, $data);
        return true;
    }

    /***
     * @param string $name
     * @return bool
     */
    public function del(string $name = ""): bool
    {
        if (empty($name)) $name = MODE . "_" . CONTROLLER . "_" . ACTION;
        $path = $this->path . $name . ".txt";
        unlink($path);
        return true;
    }

    /***
     * @return bool
     */
    public function delAll(): bool
    {
        return Dir::instance()->del($this->path);
    }


}