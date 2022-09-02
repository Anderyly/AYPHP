<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

use Exception;

class View
{

    private static array $data = [];

    public static function assign($k, $v): void
    {
        self::$data[$k] = $v;
    }

    /**
     * 显示模板
     * @param string $filePath
     * @param array $data
     * @throws Exception
     */
    public static function view(string $filePath = '', array $data = []): void
    {
        if (!is_dir(CACHE)) {
            mkdir(CACHE, 0777, true);
        }

        if (empty($filePath)) {
            $filePath = APP_PATH . MODE . '/view/' . strtolower(CONTROLLER) . '/' . ACTION . '.html';
        } else {
            //
            $suffix = strchr($filePath, '.', false);
            $arr = explode('/', $filePath);
            $num = count($arr);
            switch ($num) {
                case ($num == 3) :
                    $filePath = empty($suffix) ? APP_PATH . $arr[0] . '/view/' . $arr[1] . '/' . $arr[2] . '.html' : APP_PATH . $arr[0] . '/view/' . $arr[1] . '/' . $arr[2];
                    break;
                case ($num == 2) :
                    $filePath = empty($suffix) ? APP_PATH . $arr[0] . '/view/' . $arr[1] . '.html' : APP_PATH . MODE . '/view/' . $filePath;
                    break;
                case ($num == 1) :
                    $filePath = empty($suffix) ? APP_PATH . MODE . '/view/' . $arr[0] . '.html' : APP_PATH . MODE . '/view/' . $arr[0];
                    break;
                default :
                    break;
            }
            //
        }

        $encryptFilePath = CACHE . md5(MODE . CONTROLLER . MODE . $filePath);

        if (!C('APP.CACHE')) {
            self::isFile(self::remPlacer($filePath, null), $data);
        } else {
            //
            if (is_file($encryptFilePath . '.html')) {
                $fileT = filemtime($encryptFilePath . '.html');
                if ((time() - $fileT) >= C('APP.CACHE_TIME')) {
                    self::isFile(self::remPlacer($filePath, $encryptFilePath . '.html'), $data);
                } else {
                    self::isFile($encryptFilePath . '.html', $data);
                }
                //
            } else {
                self::isFile(self::remPlacer($filePath, $encryptFilePath . '.html'), $data);
            }
            //
        }
    }

    /**
     * 模板替换
     * @param string $filePath 原模板地址
     * @param string $encryptFilePath 加密后的模板地址
     * @return string
     * @throws Exception
     */
    private static function remPlacer(string $filePath, string $encryptFilePath): string
    {
        $cache = C('APP.CACHE');

        if ($cache) {
            $content = @file_get_contents($filePath);
            if (!is_file($filePath)) {
                halt('找不到:' . $filePath . ' 模板');
            }

            // 引入模板
            $content = self::merge($content);
            $content = preg_replace(C('APP.CacheMatch'), C('APP.CacheReplace'), $content);
            @file_put_contents($encryptFilePath, $content);

            return $encryptFilePath;
        }
        return $filePath;
    }

    /**
     * 加载 赋值
     * @param string $path 模板地址
     * @param array $data 传递的数据
     * @throws Exception
     */
    private static function isFile(string $path, array $data = []): void
    {

        if (count($data) == 0) {
            extract(array_merge($data, self::$data), EXTR_OVERWRITE, '');
        } else {
            extract(self::$data, EXTR_OVERWRITE, '');
        }

        if (is_file($path)) {
            ob_start();
            include_once $path;
            echo trim(ob_get_clean());
            exit;
        } else {
            halt('找不到:' . $path . ' 模板');
        }
    }

    /**
     * @param string $content 模板内容
     * @throws Exception
     */
    private static function merge(string $content): array|string|null
    {
        if (str_contains($content, "{@")) {
            $count = substr_count($content, '{@');
            for ($i = 1; $i <= $count; $i++) {
                if (!isset($lll)) {
                    $lll = $content;
                } else {
                    if (!isset($kkk)) {
                        $kkk = '';
                    }
                    $lll = $kkk;
                }

                preg_match(C('APP.CacheTemplate1'), $lll, $sr);
                $filename = $sr[1];
                $arr = explode('/', $filename);

                if (count($arr) == 3) {
                    $filename = APP_PATH . $arr[0] . '/view/' . $arr[1] . '/' . $arr[2];
                } else if (count($arr) == 2) {
                    $filename = APP_PATH . MODE . '/view/' . $arr[0] . '/' . $arr[1];
                } else if (count($arr) == 1) {
                    $filename = APP_PATH . MODE . '/view/' . ACTION . '/' . $arr[0];
                }

                if (!strpos($filename, 'html')) {
                    $filename .= '.html';
                }
                $cls = file_get_contents($filename);
                if (!is_file(($filename))) {
                    halt('找不到:' . $filename . ' 模板');
                }
                $kkk = preg_replace(C('APP.CacheTemplate'), $cls, $lll, 1);
            }
            $content = $kkk;
        }

        return $content;
    }
}
