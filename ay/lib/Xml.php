<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Xml
{
    
    public static function instance(): Xml
    {
        return new self();
    }

    /**
     * 创建xml文件
     * @param array $data     数据
     * @param string $root     根据节点
     * @param string $encoding 编码
     * @return string          XML字符串
     */
    public function create(array $data, string $root = '', string $encoding = "UTF-8"): string
    {
        $xml = '';
        $root = empty($root) ? "root" : $root;
        $xml .= "<?xml version=\"1.0\" encoding=\"$encoding\"?>";
        $xml .= "<$root>";
        $xml .= $this->formatXml($data);
        $xml .= "</$root>";
        return $xml;
    }

    /**
     * 将XML字符串或文件转为数组
     * @param string $xml XML字符串
     * @return array|bool        解析后的数组
     */
    public function toArray(string $xml): bool|array
    {
        $arrData = $this->compile($xml);
        $k = 1;
        return $arrData ? $this->getData($arrData, $k) : false;
    }


    /**
     * 解析XML文件
     * @param string $xml
     * @return array
     */
    private function compile(string $xml): array
    {
        $xmlRes = xml_parser_create('utf-8');
        xml_parser_set_option($xmlRes, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($xmlRes, XML_OPTION_CASE_FOLDING, 0);
        xml_parse_into_struct($xmlRes, $xml, $arr, $index);
        xml_parser_free($xmlRes);
        return $arr;
    }

    /**
     * 格式化XML
     * @param $data
     * @return string
     */
    private function formatXml($data): string
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        $xml = '';
        foreach ($data as $k => $v) {
            if (is_numeric($k)) {
                $k = "item id=\"$k\"";
            }
            $xml .= "<$k>";
            if (is_object($v) || is_array($v)) {
                $xml .= $this->formatXml($v);
            } else {
                $xml .= str_replace(array("&", "<", ">", "\"", "'", "-"), array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;", "&#45;"), $v);
            }
            list($k, ) = explode(" ", $k);
            $xml .= "</$k>";
        }
        return $xml;
    }

    /**
     * 解析编译后的内容为数组
     * @param array $arrData 数组数据
     * @param int $i       层级
     * @return array    数组
     */
    private function getData(array $arrData, int &$i): array
    {
        $data = array();
        for ($j = $i; $j < count($arrData); $j++) {
            $name = $arrData[$j]['tag'];
            $type = $arrData[$j]['type'];
            switch ($type) {
                case "attributes":
                    $data[$name]['att'][] = $arrData[$j]['attributes'];
                    break;
                case "complete": //内容标签
                    $data[$name][] = $arrData[$j]['value'] ?? '';
                    break;
                case "open": //块标签
                    $k = isset($data[$name]) ? count($data[$name]) : 0;
                    $data[$name][$k] = $this->getData($arrData, ++$j);
                    break;
                case "close":
                    return $data;
            }
        }
        return $data;
    }
}
