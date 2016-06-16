<?php

/**
 * @filename Captchas.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-5 14:57:28
 * @version 1.0
 * @Description
 * 验证码生成
 */
class Captchas {

    public function Style1($length = 4, $mode = 4, $type = 'png', $width = 75, $height = 20) {
        $randval = $this->randString($length, $mode);
        $_SESSION['captcha1'] = $randval;
        //$width = ($length * 9 + 10) > $width ? $length * 9 + 10 : $width;
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($width, $height);
        } else {
            $im = imagecreate($width, $height);
        }

        $backColor = imagecolorallocate($im, 255, 255, 255);    //背景色（随机）
        $borderColor = imagecolorallocate($im, 255, 255, 255);                    //边框色
        $pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));                 //点颜色
        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
        $stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
        for ($i = 0; $i < 25; $i++) {
            $fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
        }

        @imagestring($im, 5, 5, 3, $randval, $stringColor);
        $this->output($im, $type);
    }

    private function randString($len = 6, $type = '', $addChars = '') {

        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz0123456789' . $addChars;
                break;
            default :
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($len > 10) {//位数过长重复字符串一定次数
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }
        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str = substr($chars, 0, $len);
        } else {
            // 中文随机字
            for ($i = 0; $i < $len; $i++) {
                $str.= substr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
            }
        }
        return $str;
    }

    private function output($im, $type = 'png') {
        header("Content-type: image/" . $type);
        $ImageFun = 'Image' . $type;
        $ImageFun($im);
        imagedestroy($im);
    }

}
