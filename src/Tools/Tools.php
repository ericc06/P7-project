<?php
// src/Tools/Tools.php

namespace App\Tools;

class Tools
{
    public function getRandAlphaNumStr($len = 10)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLen = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomString .= $chars[rand(0, $charsLen - 1)];
        }
        return $randomString;
    }

    public function getRandAlphaNumStrLow($len = 10)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charsLen = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomString .= $chars[rand(0, $charsLen - 1)];
        }
        return $randomString;
    }

    public function getRandNumStr($len = 10)
    {
        $chars = '0123456789';
        $charsLen = strlen($chars);
        $randomNumString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomNumString .= $chars[rand(0, $charsLen - 1)];
        }
        return $randomNumString;
    }

    public function getRandValFromArray($array)
    {
        $key = array_rand($array);
        $value = $array[$key];
        return $value;
    }
}
