<?php

/**
 * @filename FileUpload.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-23 13:38:46
 * @version 1.0
 * @Description
 * 文件上传
 */
class FileUpload {

    /**
     * 文件上传
     */
    function upload() {
        //文件保存目录路径
        $save_path = IMAGE_DIR;
        //文件保存目录URL
        $save_url = IMAGE_URL;
        //定义允许上传的文件扩展名
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );
        if (!is_dir($save_path) || !file_exists($save_path)) {
            mkdir($save_path, 0777, TRUE);
        }

        //PHP上传失败
        if ($_FILES['imgFile']['error'] != 0) {
            $error = $_FILES['imgFile']['error'] + 500;
            if (isset(Constant::$maps[$error])) {
                Constant::uploadMessage($error);
            }
            Constant::uploadMessage(Constant::UPLOAD_ERR_UNKOWN);
        }

        //原文件名
        $file_name = $_FILES['imgFile']['name'];
        //服务器上临时文件名
        $tmp_name = $_FILES['imgFile']['tmp_name'];
        //文件大小
        $file_size = $_FILES['imgFile']['size'];
        //检查文件名
        if (!$file_name) {
            Constant::uploadMessage(Constant::UPLOAD_ERR_EMPTY_NAME);
        }
        //检查目录
        if (@is_dir($save_path) === false) {
            //die($save_path);
            Constant::uploadMessage(Constant::UPLOAD_ERR_DIR_NOT_EXISTS);
        }
        //检查目录写权限
        if (@is_writable($save_path) === false) {
            Constant::uploadMessage(Constant::UPLOAD_ERR_WRITE_PERMISSION);
        }
        //检查是否已上传
        if (@is_uploaded_file($tmp_name) === false) {
            Constant::uploadMessage(Constant::UPLOAD_ERR_FAILED);
        }
        //检查文件大小
        if ($file_size > Constant::UPLOAD_MAX_SIZE) {
            Constant::uploadMessage(Constant::UPLOAD_ERR_SIZE);
        }
        //检查目录名
        $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
        if (empty($ext_arr[$dir_name])) {
            Constant::uploadMessage(Constant::UPLOAD_ERR_DIR_NAME_INCORRECT);
        }
        //获得文件扩展名
        $temp_arr = explode(".", $file_name);
        $file_ext = array_pop($temp_arr);
        $file_ext = trim($file_ext);
        $file_ext = strtolower($file_ext);
        //检查扩展名
        if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
            Constant::uploadMessage(Constant::UPLOAD_ERR_NOT_SUPPORT_EXT, "只允许" . implode(",", $ext_arr[$dir_name]) . "格式");
        }
        //创建文件夹
        if ($dir_name !== '') {
            $save_path .= $dir_name . "/";
            $save_url .= $dir_name . "/";
            if (!file_exists($save_path)) {
                mkdir($save_path, 0777, TRUE);
            }
        }
        $ymd = date("Ymd");
        $save_path .= $ymd . "/";
        $save_url .= $ymd . "/";
        if (!file_exists($save_path)) {
            mkdir($save_path, 0777, TRUE);
        }
        //新文件名
        $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
        //移动文件
        $file_path = $save_path . $new_file_name;
        if (move_uploaded_file($tmp_name, $file_path) === false) {
            Constant::uploadMessage(Constant::UPLOAD_FAILED);
        }
        @chmod($file_path, 0644);
        $file_url = $save_url . $new_file_name;
        Constant::uploadMessage(Constant::SUCCESS, $file_url);
    }

    /**
     * 图片管理
     */
    function manage() {
        //根目录路径，可以指定绝对路径，比如 /var/www/attached/
        $root_path = IMAGE_DIR ;
        //根目录URL，可以指定绝对路径，比如 http://www.yoursite.com/attached/
        $root_url = IMAGE_URL;
        //图片扩展名
        $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

        //目录名
        $dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
        if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
            echo "Invalid Directory name.";
            exit;
        }
        if ($dir_name !== '') {
            $root_path .= $dir_name . "/";
            $root_url .= $dir_name . "/";
            if (!file_exists($root_path)) {
                die($root_path);
                mkdir($root_path, 0777, TRUE);
            }
        }

        //根据path参数，设置各路径和URL
        if (empty($_GET['path'])) {
            $current_path = realpath($root_path) . '/';
            $current_url = $root_url;
            $current_dir_path = '';
            $moveup_dir_path = '';
        } else {
            $current_path = realpath($root_path) . '/' . $_GET['path'];
            $current_url = $root_url . $_GET['path'];
            $current_dir_path = $_GET['path'];
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
        //echo realpath($root_path);
        //排序形式，name or size or type
        //不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $current_path)) {
            echo 'Access is not allowed.';
            exit;
        }
        //最后一个字符不是/
        if (!preg_match('/\/$/', $current_path)) {
            echo 'Parameter is not valid.';
            exit;
        }
        //目录不存在或不是目录
        if (!file_exists($current_path) || !is_dir($current_path)) {
            echo 'Directory does not exist.';
            exit;
        }

        //遍历目录取得文件信息
        $file_list = array();
        if ($handle = opendir($current_path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') {
                    continue;
                }
                $file = $current_path . $filename;
                if (is_dir($file)) {
                    $file_list[$i]['is_dir'] = true; //是否文件夹
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                    $file_list[$i]['filesize'] = 0; //文件大小
                    $file_list[$i]['is_photo'] = false; //是否图片
                    $file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
                } else {
                    $file_list[$i]['is_dir'] = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = $filename; //文件名，包含扩展名
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                $i++;
            }
            closedir($handle);
        }

        function cmp_func($a, $b) {
            $order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);
            if ($a['is_dir'] && !$b['is_dir']) {
                return -1;
            } else if (!$a['is_dir'] && $b['is_dir']) {
                return 1;
            } else {
                if ($order == 'size') {
                    if ($a['filesize'] > $b['filesize']) {
                        return 1;
                    } else if ($a['filesize'] < $b['filesize']) {
                        return -1;
                    } else {
                        return 0;
                    }
                } else if ($order == 'type') {
                    return strcmp($a['filetype'], $b['filetype']);
                } else {
                    return strcmp($a['filename'], $b['filename']);
                }
            }
        }

        usort($file_list, 'cmp_func');

        $result = array();
        //相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveup_dir_path;
        //相对于根目录的当前目录
        $result['current_dir_path'] = $current_dir_path;
        //当前目录的URL
        $result['current_url'] = $current_url;
        //文件数
        $result['total_count'] = count($file_list);
        //文件列表数组
        $result['file_list'] = $file_list;

        //输出JSON字符串
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($result);
    }

}
