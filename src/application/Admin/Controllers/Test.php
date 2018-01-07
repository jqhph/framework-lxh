<?php

namespace Lxh\Admin\Controllers;

use Lxh\Debug\Code;
use Lxh\Exceptions\Exception;
use Lxh\Exceptions\NotFound;
use Lxh\Helper\Console;
use Lxh\Helper\Util;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Status;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Lxh\Test\ShareCard;

class Test extends Controller
{
    public function actionTest(Request $req, Response $resp, & $params)
    {
        return $this->render('test');
//     header ('Content-type: image/png');
        $resp->contentType('image/png');

        $card = new ShareCard();
        $card->logo('C:\Users\hasee\Desktop\work\zl/logo.gif');
        $card->qrcode(__DATA_ROOT__.'qrcode.png');
        $card->banner('C:\Users\hasee\Desktop\work\zl/banner2.png');
        $card->head('C:\Users\hasee\Desktop\work\zl/wxtoux.jpg');
        $card->userText(
            "微信昵称：远行歌\n推荐人ID：18887"
        );
        $card->title('广州长隆野生动物园');
        $card->subTitle('探索发现 求证分享，空中缆车720度观赏');
        $card->desc('AAAAA景区');
        $card->price(164);
        $card->discount(250);
        $card->tagText('广州人 来哈哈哈');
        $card->qrcodeText('长按识别二维码');

        return $card->build()->getByteStream();

        $im = $this->createTag('广州', 2);

        $resp->contentType('image/png; charset=utf-8');

        $logo = 'C:\Users\hasee\Desktop\work\zl/logo.gif';
        $head = 'C:\Users\hasee\Desktop\work\zl/wxtoux.jpg';
        $qrcode = __DATA_ROOT__.'qrcode.png';
        $bg = 'C:\Users\hasee\Desktop\work\zl/bg2.png';

        return $this->img1($logo, $head, $qrcode, $bg, null);
    }

    protected function createLogo($bg, $bg, $logo)
    {
        imagecopyresampled($bg, $logo, 0, 0, 0, 0, $source_w, $source_h, $source_w, $source_h);
    }

    protected function createHead()
    {

    }

    protected function createBanner()
    {

    }

    protected function createQrcode()
    {

    }

    protected function img1($logoImg, $headImg, $qrcode, $bg, $path = null)
    {
        $source_w = 690;
        $source_h = 382;

//        $logoImg = $this->scalePic($logoImg);
        $headImg = $this->scalePic($headImg, 125, 125);
//var_dump($logoImg);die;
        // 取logo图片大小
        $logo_size = getimagesize($logoImg);
        $logo_height = $logo_size['1'];
        $logo_width = $logo_size['0'];
        $logo_start_y = 23;//中间开始Y坐标，因为头部的图片底部有空白，所以减去15
        $logo_start_x = 15;

        //取头部图片大小
        $head_size = getimagesize($headImg);
        $head_height = $head_size['1'];
        $head_width = $head_size['0'];
        $head_start_x = 19;//头部开始位置
        $head_start_y = $logo_height + 45;

         //取底部图片大小
        $qrcode_size = getimagesize($qrcode);
        $qrcode_height = $qrcode_size[1];
        $qrcode_width = $qrcode_size[0];
        $qrcode_start_x = floor(($source_w-$qrcode_width)/2) + 220;//底部图片x坐标
        $qrcode_start_y = $source_h-$qrcode_height - 150;//底部图片y坐标
//        ddd($logo_size, $head_size, $qrcode_size);
        $head = imagecreatefromjpeg($headImg);
        $logo = imagecreatefromgif($logoImg);
        $qrcode = imagecreatefrompng($qrcode);
        $bg = imagecreatefrompng($bg);

        $bg_img = imageCreatetruecolor($source_w,$source_h);//生成背景图片
        $color = imagecolorallocate($bg_img, 255, 255, 255); //设置白色背景
        imagefill($bg_img, 0, 0, $color);//背景色填充
//        imageColorTransparent($bg_img, $color);//透明
        imagecopyresampled($bg_img, $bg, 0, 0, 0, 0, $source_w, $source_h, $source_w, $source_h);
        imagecopyresampled($bg_img, $head, $head_start_x, $head_start_y,0,0,$head_width,$head_height,$head_width,$head_height);
        imagecopyresampled($bg_img, $logo, $logo_start_x, $logo_start_y,0,0,$logo_width,$logo_height,$logo_width,$logo_height);
        imagecopyresampled($bg_img, $qrcode,$qrcode_start_x , $qrcode_start_y,0,0,$qrcode_width,$qrcode_height,$qrcode_width,$qrcode_height);

        // 指定字体内容
        $content = "用户昵称：远行歌\n推荐人ID：1984457";
        //指定字体颜色
        $col = imagecolorallocatealpha($bg_img, 0, 0, 0, 20);
        $font = __DIR__ . '/fonts/msyh.ttc';
        //给图片添加文字
//        imagestring($bg_img, 5, 220, 30, $content,$col);
        imagefttext(
            $bg_img, 14, 0, 175, 125, $col, $font, $content
        );

        $content = "扫码加入,月入过万不是梦";
        imagefttext(
            $bg_img, 13, 0, 220, 290, $col, $font, $content
        );

        return $this->getByteStream($bg_img, $path);
    }

    protected function createTag($content, $length = 2)
    {
        $font = __DIR__ . '/fonts/msyh.ttc';
        $im = ImageCreate (180, 40);

        $widths = [
            2 => [55, 85],
            3 => [70, 105],
            4 => [90, 125]
        ];

        $white = ImageColorAllocate ($im, 255, 255, 255);
        $black = ImageColorAllocate ($im, 255, 100, 97);
        imageColorTransparent($im, $white);//透明

        $coordinates = [
            $widths[$length][0], 75,
            0, 75,
            0, 0,
            $widths[$length][1], 0,
        ];
        ImageFilledPolygon($im, $coordinates, 4, $black);

        imagefttext(
            $im, 15, 0, 17, 28, $white, $font, $content
        );

//        return $im;
        ImagePng ($im);
        ImageDestroy ($im);
    }

    public function getByteStream($img, $path)
    {
        ob_start();
        imagepng($img);
        return ob_get_clean();
    }

    /**
     * @function 等比缩放函数(以保存的方式实现)
     * @param string $picname 被缩放的处理图片源
     * @param int $maxX 缩放后图片的最大宽度
     * @param int $maxY 缩放后图片的最大高度
     * @param string $pre 缩放后图片名的前缀名
     * @return string 返回后的图片名称(带路径),如a.jpg --> s_a.jpg
     */
    function scalePic($picname,$maxX=100,$maxY=100,$pre='s_')
    {
        $info = getimagesize($picname); //获取图片的基本信息
        $width = $info[0];//获取宽度
        $height = $info[1];//获取高度
        //判断图片资源类型并创建对应图片资源
        $im = $this->getPicType($info[2],$picname);
        //计算缩放比例
        $scale = ($maxX/$width)>($maxY/$height)?$maxY/$height:$maxX/$width;
        //计算缩放后的尺寸
        $sWidth = floor($width*$scale);
        $sHeight = floor($height*$scale);
        //创建目标图像资源
        $nim = imagecreatetruecolor($sWidth,$sHeight);
        //等比缩放
        imagecopyresampled($nim,$im,0,0,0,0,$sWidth,$sHeight,$width,$height);

        //输出图像
        $newPicName = $this->outputImage($picname,$pre,$nim);
        //释放图片资源
        imagedestroy($im);
        imagedestroy($nim);
        return $newPicName;
    }

    /**
     * function 判断并返回图片的类型(以资源方式返回)
     * @param int $type 图片类型
     * @param string $picname 图片名字
     * @return 返回对应图片资源
     */
    function getPicType($type,$picname)
    {
        $im=null;
        switch($type)
        {
            case 1:  //GIF
                $im = imagecreatefromgif($picname);
                break;
            case 2:  //JPG
                $im = imagecreatefromjpeg($picname);
                break;
            case 3:  //PNG
                $im = imagecreatefrompng($picname);
                break;
            case 4:  //BMP
                $im = imagecreatefromwbmp($picname);
                break;
            default:
                die("不认识图片类型");
                break;
        }
        return $im;
    }

    /**
     * function 输出图像
     * @param string $picname 图片名字
     * @param string $pre 新图片名前缀
     * @param resourse $nim 要输出的图像资源
     * @return 返回新的图片名
     */
    function outputImage($picname,$pre,$nim)
    {
        $info = getimagesize($picname);
        $picInfo = pathInfo($picname);
        $newPicName = $picInfo['dirname'].'/'.$pre.$picInfo['basename'];//输出文件的路径
        switch($info[2])
        {
            case 1:
                imagegif($nim,$newPicName);
                break;
            case 2:
                imagejpeg($nim,$newPicName);
                break;
            case 3:
                imagepng($nim,$newPicName);
                break;
            case 4:
                imagewbmp($nim,$newPicName);
                break;
        }
        return $newPicName;
    }
    // Create a basic QR code
//        $qrCode = new QrCode('Life is too short to be generating QR codes');
//        $qrCode->setSize(100);
//
//        // Set advanced options
//        $qrCode->setWriterByName('png');
//        $qrCode->setMargin(1);
//        $qrCode->setEncoding('UTF-8');
//        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::MEDIUM);
////        $qrCode->setLabel('Scan the code', 16);
////        $qrCode->setValidateResult(true);
//
//        $resp->contentType($qrCode->getContentType());
//
//        $qrCode->writeFile(__DATA_ROOT__.'qrcode.png');

}
