<?php

/**
 * 签到模块处理程序
 *
 * @author zhouchong
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
define('ALAN_NAME', 'wei_qian');

class Wei_qianModuleProcessor extends WeModuleProcessor {

    public function respond() {
        $content = $this->message['content'];
        //这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
        // return $this->respText('您触发了demo模块');
        global $_GPC, $_W;
        load()->func('file');
        $path = IA_ROOT . '/attachment/' . ALAN_NAME . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . time() . '.png';
        if (!is_dir(IA_ROOT . '/attachment/' . ALAN_NAME . '/' . date('Y') . '/' . date('m') . '/' . date('d'))) {
            @mkdirs(IA_ROOT . '/attachment/' . ALAN_NAME . '/' . date('Y') . '/' . date('m') . '/' . date('d'), '0777');
        }

        $config = Array('mode' => 1, 'qrcode_background' => IA_ROOT . '/addons/alan_qrcode/template/images/qrcode_background.jpg', 'qrleft' => 160, 'qrtop' => 230, 'qrwidth' => 155, 'qrheight' => 155, 'qrcode_logo' => '', 'logowidth' => '', 'logoheight' => '', 'logoqrwidth' => '', 'logoqrheight' => '', 'integral' => 12, 'balance' => 1);
        $result['url'] = $this->createMobileUrl('Guanzhu', array('fmopenid' => $this->message['from']));
        $result['url'] = substr($result['url'], 2);
        $result['url'] = $_W['siteroot'] . $result['url'];
        //$result['url']="http://www.baidu.com";
        $save_path = $this->qrcode_logo($config, $path, $result['url'], true);


        $account_api = WeAccount::create();
        //任意指定一个文件上传
        $result1 = $account_api->uploadMedia($save_path, 'image');

        return $this->respImage($result1['media_id']);
        //return $this->respText($result1['media_id']);
    }

    function qrcode_logo($config, $path, $_wx_url, $is_merge = false) {
        require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        load()->func('file');
        $errorCorrectionLevel = 'L'; //设置容错级别
        $matrixPointSize = ($config['logoqrwidth'] > 0 ? $config['logoqrwidth'] : 4); //生成图片大小
        if ($matrixPointSize > 10) {
            $matrixPointSize = 20;
        }
        QRcode::png($_wx_url, $path, $errorCorrectionLevel, $matrixPointSize, 1); //生成二维码图片 无logo
        $logo = $config['qrcode_logo'] == IA_ROOT . '/addons/alan_qrcode/template/images/110*110.png' ? realpath($config['qrcode_logo']) : realpath(IA_ROOT . '/attachment/' . $config['qrcode_logo']); //上传至服务器的logo图片
        $QR = $path; //已经生成的原始二维码图
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR); //二维码图片宽度
            $QR_height = imagesy($QR); //二维码图片高度
            $logo_width = $config['logowidth'] ? $config['logowidth'] : imagesx($logo); //logo图片宽度
            $logo_height = $config['logoheight'] ? $config['logoheight'] : imagesy($logo); //logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        file_delete($path);
        $time = time() + 1;
        $p = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $time . '.png';
        $base_save_path = IA_ROOT . '/attachment/' . ALAN_NAME . '/' . $p;
        imagepng($QR, $base_save_path); //输出带logo的二维码图片
        $save_path = IA_ROOT . '/attachment/' . ALAN_NAME . '/' . $p;
        imagedestroy($QR); //销毁图片资源句柄
        if ($is_merge) {
            //logo 缩放
            $src = imagecreatefrompng($save_path);
            $dest = imagecreatefromjpeg(realpath($config['qrcode_background']));
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $width = $config['qrleft'] ? $config['qrleft'] : 30;
            $height = $config['qrtop'] ? $config['qrtop'] : 100;
            $qr_width = $config['qrwidth'] ? $config['qrwidth'] : 430;
            $qr_height = $config['qrheight'] ? $config['qrheight'] : 430;
            imagecopymerge($dest, $src, $width, $height, 0, 0, $qr_width, $qr_height, 100);
            $p = date('Y') . '/' . date('m') . '/' . date('d') . '/' . ($time + 3) . '.png';
            $_save_path = IA_ROOT . '/attachment/' . ALAN_NAME . '/' . $p;
            imagepng($dest, $_save_path);
            imagedestroy($dest);
            imagedestroy($src);
            file_delete($save_path);
            $save_path = IA_ROOT . '/attachment/' . ALAN_NAME . '/' . $p;
        }
        return $save_path;
    }

}
