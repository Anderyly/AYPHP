<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

use Exception;

class Dir
{

    public static function instance(): Dir
    {
        return new self();
    }

    /**
     * @param string $dir_name 目录名
     * @return string
     */
    public function dirPath(string $dir_name): string
    {
        $dir_name = str_ireplace("\\", "/", $dir_name);
        return substr($dir_name, "-1") == "/" ? $dir_name : $dir_name . "/";
    }

    /**
     * 获得扩展名
     * @param string $file 文件名
     * @return string
     */
    public function getExt(string $file): string
    {
        return strtolower(substr(strrchr($file, "."), 1));
    }

    /**
     * 遍历目录内容
     * @param string $dir_name 目录名
     * @param string $ext 读取的文件扩展名
     * @param int $display 是否显示子目录
     * @param array $list
     * @return array
     */
    public function tree(string $dir_name = '', string $ext = '', int $display = 0, array $list = []): array
    {
        if (empty($dir_name)) {
            $dir_name = '.';
        }
        $dirPath = $this->dirPath($dir_name);
        static $id = 0;
        if (is_array($ext)) {
            $ext = implode("|", (array)($ext));
        }
        foreach (glob($dirPath . '*') as $v) {
            $id++;
            if (is_dir($v) || !$ext || preg_match("/\.($ext)/i", $v)) {
                $path =  phpstr_replace("\\", "/", realpath($v));
                $list[$id]['type'] = filetype($v);
                $list[$id]['filename'] = basename($v);
                $list[$id]['path'] = $path;
                $list[$id]['spath'] = ltrim(str_replace(dirname($_SERVER['SCRIPT_FILENAME']), '', $path), '/');
                $list[$id]['filemtime'] = filemtime($v);
                $list[$id]['fileatime'] = fileatime($v);
                $list[$id]['size'] = is_file($v) ? filesize($v) : $this->getDirSize($v);
                $list[$id]['is_write'] = is_writeable($v) ? 1 : 0;
                $list[$id]['is_read'] = is_readable($v) ? 1 : 0;
            }
            if ($display) {
                if (is_dir($v)) {
                    $list = $this->tree($v, $ext, $display = 1, $list);
                }
            }
        }
        return $list;
    }

    /**
     * 获取目录大小
     * @param string $dir_name 目录名
     * @return int
     */
    public function getDirSize(string $dir_name): int
    {
        $s = 0;
        foreach (glob($dir_name . '/*') as $v) {
            $s += is_file($v) ? filesize($v) : $this->getDirSize($v);
        }
        return $s;
    }

    /**
     * 只显示目录树
     * @param string $dir_name 目录名
     * @param int $display
     * @param int $pid 父目录ID
     * @param array $dirs 目录列表
     * @return array
     */
    public function treeDir(string $dir_name = '', int $display = 0, int $pid = 0, array $dirs = []): array
    {
        if (empty($dir_name)) {
            $dir_name = '.';
        }
        static $id = 0;
        $dirPath = $this->dirPath($dir_name);
        foreach (glob($dirPath . "*") as $v) {
            if (is_dir($v)) {
                $id++;
                $dirs [$id] = ["id" => $id, 'pid' => $pid, "name" => basename($v), "path" => $v];
                if ($display) {
                    $dirs = $this->treeDir($v, $display, $id, $dirs);
                }
            }
        }
        return $dirs;
    }

    /**
     * 删除目录及文件，支持多层删除目录
     * @param string $dir_name 目录名
     * @return bool
     */
    public function del(string $dir_name): bool
    {
        if (is_file($dir_name)) {
            unlink($dir_name);
            return true;
        }
        $dirPath = $this->dirPath($dir_name);

        if (!is_dir($dirPath)) {
            return true;
        }

        foreach (glob($dirPath . "*") as $v) {
            is_dir($v) ? $this->del($v) : unlink($v);
        }
        return @rmdir($dir_name);
    }

    /**
     * 批量创建目录
     * @param string $dir_name
     * @param int $auth 权限
     * @return bool
     */
    public function create(string $dir_name, int $auth = 0755): bool
    {
        $dirPath = $this->dirPath($dir_name);
        if (is_dir($dirPath)) {
            return true;
        }
        $dirs = explode('/', $dirPath);
        $dir = '';
        foreach ($dirs as $v) {
            $dir .= $v . '/';
            if (is_dir($dir)) {
                continue;
            }
            mkdir($dir, $auth);
        }
        return is_dir($dirPath);
    }

    /**
     * 复制目录
     * @param string $old_dir
     * @param string $new_dir
     * @param bool $strip_space 去空白去注释
     * @return bool
     * @throws Exception
     */
    public function copy(string $old_dir, string $new_dir, bool $strip_space = false): bool
    {
        $old_dir = $this->dirPath($old_dir);
        $new_dir = $this->dirPath($new_dir);
        if (!is_dir($old_dir)) {
            halt("复制失败：" . $old_dir . "目录不存在");
        }
        if (!is_dir($new_dir)) {
            $this->create($new_dir);
        }
        foreach (glob($old_dir . '*') as $v) {
            $to = $new_dir . basename($v);
            if (is_file($to)) {
                continue;
            }
            if (is_dir($v)) {
                $this->copy($v, $to, $strip_space);
            } else {
                if ($strip_space) {
                    $data = file_get_contents($v);
                    file_put_contents($to, $data);
                } else {
                    copy($v, $to);
                }
                chmod($to, "0777");
            }
        }
        return true;
    }
}
