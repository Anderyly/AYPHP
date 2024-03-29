<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

use Exception;

class Autoloader
{
    // 站点根目录，可配置多个子目录
    protected array $domainRoot = [];

    public function __construct()
    {

        $dN = count(explode('/', rtrim(str_replace('\\', '/', __DIR__), '/')));
        $gN = count(explode('/', rtrim(str_replace('\\', '/', ROOT), '/')));
        $number = $dN - $gN;
        $str = '/';
        for ($i = 1; $i <= $number; $i++) {
            $str .= '../';
        }
        $this->domainRoot = [
            __DIR__ . $str,
        ];
    }

    /**
     * 清空当前web目录
     * @return $this
     */
    public function clear(): static
    {
        $this->domainRoot = [];
        return $this;
    }

    /**
     * 返回当前实例
     * @return Autoloader
     */
    public static function instance(): Autoloader
    {
        return new static;
    }

    /**
     * 设置web目录
     * @param string $root
     * @return $this
     * @throws Exception
     */
    public function setRoot(string $root = ''): static
    {
        if (!$root || !is_dir(realpath($root))) {
            throw new Exception('No root param export or invalid path');
        }
        $this->domainRoot[] = realpath($root);
        return $this;
    }

    /**
     * Autoloader 核心方法，加载对应文件
     * @param $class
     * @return bool
     */
    protected function autoloader($class): bool
    {
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        $file = str_replace('ay', 'ayphp', $file);
        foreach ($this->domainRoot as $path) {
            clearstatcache();
            $path = $path . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                require_once $path;
                if (class_exists($class, false)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 初始化
     * @return $this
     */
    public function init(): static
    {
        spl_autoload_register(array($this, 'autoloader'));
        return $this;
    }
}
