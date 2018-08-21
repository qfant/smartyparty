<?php

/**

 * byte数组与字符串转化类

 */

class Bytes {


    /**

     * 转换一个String字符串为byte数组

     * @param $str 需要转换的字符串

     * @param $bytes 目标byte数组

     * @author Zikie

     */
    public static function getBytes($string) {
        $bytes = array();
        for($i = 0; $i < strlen($string); $i++){
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }


    /**

     * 将字节数组转化为String类型的数据

     * @param $bytes 字节数组

     * @param $str 目标字符串

     * @return 一个String类型的数据

     */

    public static function toStr($bytes) {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }

        return $str;
    }


    /**

     * 转换一个int为byte数组

     * @param $byt 目标byte数组

     * @param $val 需要转换的字符串

     *

     */

    public static function integerToBytes($val) {
        $byt = array();
        $byt[0] = ($val & 0xff);
        $byt[1] = ($val >> 8 & 0xff);
        $byt[2] = ($val >> 16 & 0xff);
        $byt[3] = ($val >> 24 & 0xff);
        return $byt;
    }


    /**

     * 从字节数组中指定的位置读取一个Integer类型的数据

     * @param $bytes 字节数组

     * @param $position 指定的开始位置

     * @return 一个Integer类型的数据

     */

    public static function bytesToInteger($bytes, $position) {
        $val = 0;
        $val = $bytes[$position + 3] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 2] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 1] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position] & 0xff;
        return $val;
    }


    /**

     * 转换一个shor字符串为byte数组

     * @param $byt 目标byte数组

     * @param $val 需要转换的字符串

     *

     */

    public static function shortToBytes($val) {
        $byt = array();
        $byt[0] = ($val & 0xff);
        $byt[1] = ($val >> 8 & 0xff);
        return $byt;
    }


    /**

     * 从字节数组中指定的位置读取一个Short类型的数据。

     * @param $bytes 字节数组

     * @param $position 指定的开始位置

     * @return 一个Short类型的数据

     */

    public static function bytesToShort($bytes, $position) {
        $val = 0;
        $val = $bytes[$position + 1] & 0xFF;
        $val = $val << 8;
        $val |= $bytes[$position] & 0xFF;
        return $val;
    }

    function strToHex($string)//字符串转十六进制
    {
        $hex="";
        for($i=0;$i<strlen($string);$i++)
            $hex.=dechex(ord($string[$i]));
        $hex=strtoupper($hex);
        return $hex;
    }

    function bytesToHex($bytes)//字符串转十六进制
    {
        $hex="0123456789ABCDEF";
        $str='';
        foreach($bytes as $ch) {
            $str.=$hex{$ch>>4& 0x0f};
            $str.=$hex{$ch& 0x0f};
        }
        $str=strtoupper($str);
        return $str;
    }
    function hexToBytes($str)
    {
        $bytes = array();
        $hex="0123456789ABCDEF";
        for($i=0;$i<strlen($str);$i+=2){
            $bytes[]=  (strpos($hex, $str{$i}) <<4 )|strpos($hex,$str{$i+1});
        }
        return $bytes;
    }
}
?>