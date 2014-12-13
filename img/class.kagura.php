<?php

class kagura
{
    var $config = array();
    var $thumb_width = 80;
    var $thumb_height = 80;
    var $cropped = 0;
    var $default_size = array('default','small','medium','big','thumb', 'landscape');
    
    function init(){
        $this->connect_db();
        $this->load_config();
        $this->router();
    }

    function connect_db()
    {
        if ($_SERVER['HTTP_HOST']=='localhost'){
            $dbHost="localhost";
            $dbUser="root";
            $dbPass="";
            $dbname="kagura";
        } else {
            $dbHost = "localhost";
            $dbUser = "";
            $dbPass = "";
            $dbname = "";
        }
        $koneksi_db = @mysql_connect($dbHost, $dbUser, $dbPass);
        if (!$koneksi_db){
            echo 'Database maintenance problem! Please try again';
            exit();
        } else {
            @mysql_select_db($dbname, $koneksi_db);
        }
    }
    
    function load_config()
    {
        $sp = "SELECT config_name,config_value FROM config";
        $gp = mysql_query($sp);
        $cp = mysql_num_rows($gp);
        
        if ($cp!=0)
        {
            while($dp = mysql_fetch_array($gp))
            {
                $this->config[$dp['config_name']] = $dp['config_value'];
            }
        }
    }
    
    function router(){
        $uri = str_replace(SITEPATH,'',$_SERVER['REQUEST_URI']);
        $uri = ltrim($uri,'/');
        
        $routes = array(
            'img/captcha.jpg' => 'img&mode=captcha',
            'img/(.*)/(.*)\.(.*)' => 'img&mode=$1&src=$2.$3',
            'img/(.*)/(.*)/(.*)\.(.*)' => 'img&mode=$1&size=$2&src=$3.$4'
        );
        
        foreach ($routes as $from => $to) {
            if (preg_match('#^'.$from.'$#', $uri)) {
                $to = preg_replace('#^'.$from.'$#', $to, $uri);
                $mod = '?content='.$to;
                $url = parse_url($mod);
                $query = array();
                parse_str($url['query'], $query);
                $_GET = $query;
            }
        }
    }
    
    function send_header($content_type,$crop){
        header('Content-type: '.$content_type);
        switch($content_type){
            case "image/png":
                imagepng($crop, NULL, 9);
            break;
            case "image/gif":
                imagegif($crop, NULL, 100);
            break;
            case "image/jpeg":
            case "image/pjpeg":
            default:
                imagejpeg($crop, NULL, 100);
            break;
        }
        imagedestroy($crop);
    }
    
    function render(){
            
        if (isset($_GET['mode']) && $_GET['mode']=='captcha'){
            $this->captcha();
            exit();
        }
        
        $mode = '';
        $size = '';
        $src = 'none.jpg';
        $thumb_width = $this->thumb_width;
        $thumb_height = $this->thumb_height;
                
        if (isset($_GET['mode'])){
            $mode = $_GET['mode'];
        }
        
        if (isset($_GET['src'])){
            $src = $_GET['src'];
        }
        
        if (isset($_GET['size'])){
            $size = $_GET['size'];
        }
        
        $path = '../images/';
        
        if (!empty($mode)){
            $path = $path.$mode.'/';
        }
        
        $filename = $path.$src;
        
        if (!file_exists($filename)){
            $filename = '../images/none.jpg';
        }
        
        if (!empty($mode)){
            if (!empty($size) && in_array($size,$this->default_size)==true){
                $thumb_width = $this->config[$size.'_width'];
                $thumb_height = $this->config[$size.'_height'];
            } else {
                $thumb_width = $this->config[$mode.'_width'];
                $thumb_height = $this->config[$mode.'_height'];
            }
        }
        
        if ($thumb_width==0 || $thumb_height==0){
            $thumb_width = $this->thumb_width;
            $thumb_height = $this->thumb_height;
        }
        
        $img = getimagesize($filename);
        
        switch($img['mime']){
            case "image/png":
                $image = imagecreatefrompng($filename);
                $content_type = 'image/png';
            break;
            case "image/gif":
                $image = imagecreatefromgif($filename);
                $content_type = 'image/gif';
            break;
            case "image/jpeg":
            case "image/pjpeg":
            default:
                $image = imagecreatefromjpeg($filename);
                $content_type = 'image/jpg';
            break;
        }
        
        $crop = imagecreatetruecolor($thumb_width,$thumb_height);
        $black = imagecolorallocate($crop, 0, 0, 0);
        imagecolortransparent($crop, $black);
        
        list($width, $height) = getimagesize($filename);
        
        if ($this->cropped==1){
            if ($width>$height)
            {
                // horizontal
                $percentage = $thumb_width/$width;
                $new_width = $thumb_width;
                $new_height = intval($height*$percentage);
            } else {
                // vertical
                $percentage = $thumb_height/$height;
                $new_width = intval($width*$percentage);
                $new_height = $thumb_height;
            }
        } else {
            $original_aspect = $width / $height;
            $thumb_aspect = $thumb_width / $thumb_height;
            
            if ( $original_aspect >= $thumb_aspect )
            {
                if ( ($thumb_width>$width) || ($thumb_height>$height) )
                {
                    // if pic smaller than resize image
                    $new_width = $width;
                    $new_height = $height;
                } else {
                    if ($width>$height)
                    {
                        // horizontal
                        $percentage = $thumb_width/$width;
                        $new_width = $thumb_width;
                        $new_height = intval($height*$percentage);
                    } else {
                        // vertical
                        $percentage = $thumb_height/$height;
                        $new_width = intval($width*$percentage);
                        $new_height = $thumb_height;
                    }
                }
            } else {
                if ( ($thumb_width>$width) || ($thumb_height>$height) )
                {
                    // if pic smaller than resize image
                    $new_width = $width;
                    $new_height = $height;
                } else {
                    if ($width>$height)
                    {
                        // horizontal
                        $percentage = $thumb_width/$width;
                        $new_width = $thumb_width;
                        $new_height = intval($height*$percentage);
                    } else {
                        // vertical
                        $percentage = $thumb_height/$height;
                        $new_width = intval($width*$percentage);
                        $new_height = $thumb_height;
                    }
                }
            }
        }
        imagecopyresampled($crop,
                           $image,
                           0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                           0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                           0, 0,
                           $new_width, $new_height,
                           $width, $height);
                           
        $this->send_header($content_type,$crop);
    }

    function captcha(){
        session_cache_expire(2);
        session_start();
        
        $ttf = "captcha/font.ttf";
        $font = 13;
        $str = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";
        $rand = substr(str_shuffle($str),0,6);
        $no = rand(1,10);
        
        $image = imagecreatefromjpeg("captcha/bg$no.jpg");
        $black = imagecolorallocate($image,0,0,0);
        
        $y = (imagesy($image)-imagefontheight($font))/2;
        
        $gd = gd_info();
        
        if ($gd['FreeType Support']==1){
            imagettftext($image,$font,0,15,30,$black,$ttf,$rand);
        } else {
            imagestring($image,$font,8,$y,$rand,$black);
        }
        
        $_SESSION['RandVal'] = md5($rand);
        
        header('Content-type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
    }
    
}
