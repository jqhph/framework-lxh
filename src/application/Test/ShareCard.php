<?php

namespace Lxh\Test;

use Lxh\Exceptions\Forbidden;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Helper\Valitron\Validator;

class ShareCard
{
    /**
     * 卡片宽度
     *
     * @var int
     */
    protected $width = 600;

    /**
     * 卡片高度
     *
     * @var int
     */
    protected $height = 820;

    /**
     * 中文字体路径
     *
     * @var string
     */
    protected $font;

    /**
     * @var array
     */
    protected $imgPaths = [
        'logo' => '', 'head' => '', 'qrcode' => '', 'banner' => ''
    ];

    protected $options = [
        'logo' => [],
        'head' => [
            'w' => 100, 'h' => 100,
        ],
        'qrcode' => [],
        'banner' => [
            'w' => 600, 'h' => 273
        ],
        'tag' => [
            'w' => 300, 'h' => 28
        ]
    ];

    /**
     * 背景图
     *
     * @var resource
     */
    protected $bg;

    public function __construct()
    {
        $this->font = __DIR__ . '/fonts/msyh.ttc';
    }

    public function getByteStream()
    {
        ob_start();
        imagepng($this->bg);
        return ob_get_clean();
    }

    /**
     * 设置、获取参数
     *
     * @param $type
     * @param $key
     * @param null $v
     * @return $this|null
     */
    public function option($type, $key, $v = null)
    {
        if ($v !== null) {
            $this->options[$type] = $key;
            return $this;
        }

        return isset($this->options[$type][$key]) ? $this->options[$type][$key] : null;
    }

    public function logo($path)
    {
        $this->imgPaths['logo'] = &$path;
        return $this;
    }

    public function head($path)
    {
        $this->imgPaths['head'] = &$path;
        return $this;
    }

    public function qrcode($path)
    {
        $this->imgPaths['qrcode'] = &$path;
        return $this;
    }

    public function banner($path)
    {
        $this->imgPaths['banner'] = &$path;
        return $this;
    }

    protected function createHead()
    {
        return $this->create('head', function ($width, $height) {
            $startx = 21;//头部开始位置
            $starty = 21;

            return [$startx, $starty];
        });
    }

    protected function createLogo()
    {
        return $this->create('logo', function ($width, $height) {
            $startx = $this->width - $width - 25;
            $starty = 35;

            return [$startx, $starty];
        });
    }

    protected function createBannner()
    {
        return $this->create('banner', function ($w, $h) {
            $startx = 0;
            $starty = 250;

            return [$startx, $starty];
        });
    }

    protected function createQrcode()
    {
        return $this->create('qrcode', function ($w, $h) {
            $startx = $this->width - $w - 42;//头部开始位置
            $starty = 560;

            return [$startx, $starty];
        });
    }

    protected function create($type, callable $callback)
    {
        $path = $this->imgPaths[$type];
        if (($w = $this->option($type, 'w')) && $h = $this->option($type, 'h')) {
            $path = $this->scalePic($path, $w, $h);
        }

        $size = getimagesize($path);
        $height = $size[1];
        $width = $size[0];

        list($startx, $starty) = $callback($width, $height);

        $resource = $this->getPicType($size[2], $path);
        imagecopyresampled(
            $this->bg, $resource, $startx, $starty, 0, 0, $width, $height, $width, $height
        );
        return $resource;
    }

    protected function createBg()
    {
        $this->bg = imageCreatetruecolor($this->width, $this->height);// 生成背景图片
        $color = imagecolorallocate($this->bg, 255, 255, 255); //设置白色背景
        imagefill($this->bg, 0, 0, $color);//背景色填充
    }

    /**
     * 生成分享卡片
     */
    public function build()
    {
        $this->createBg();
        $this->createHead();
        $this->createQrcode();
        $this->createBannner();
        $this->createLogo();

        $tag = $this->createTag('北京');
        $tagw = $this->option('tag', 'w');
        $tagh = $this->option('tag', 'h');
        imagecopyresampled(
            $this->bg, $tag, 0, 250, 0, 0,$tagw, $tagh, $tagw, $tagh
        );

        return $this;
    }
    /*
       // 指定字体内容
        $content = "用户昵称：远行歌\n推荐人ID：1984457";
        //指定字体颜色
        $col = imagecolorallocatealpha($this->bg, 0, 0, 0, 20);
        $font = __DIR__ . '/fonts/msyh.ttc';
        //给图片添加文字
//        imagestring($this->bg, 5, 220, 30, $content,$col);
        imagefttext(
            $this->bg, 14, 0, 175, 125, $col, $font, $content
        );

        $content = "扫码加入,月入过万不是梦";
        imagefttext(
            $this->bg, 13, 0, 220, 290, $col, $font, $content
        );
     */

    protected function createTag($content, $length = null)
    {
        $chineseWordsLength = $length;
        if ($length === null) {
            $length = mb_strlen($content);
            // 过滤非中文字符
            preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $content, $matches);
            $chineseWordsLength = mb_strlen(join('', $matches[0]));
        }

        $font = $this->font;
        $height = $this->option('tag', 'h');
        $im = ImageCreate($this->option('tag', 'w'), $height);

        $chineseWordWidth = 13;
        $wordWidth = 6;
        $minWidth = 55;
        $minWidthL = 63;

        if ($chineseWordsLength < $length || ($chineseWordsLength - 2)) {
            $chineseWordsLength = $chineseWordsLength - 2;
            $tmp = $chineseWordsLength * $chineseWordWidth + ($length - $chineseWordsLength) * $wordWidth;
            if ($chineseWordsLength < 5) $tmp -= 5;
            $minWidth += $tmp;
            $minWidthL += $tmp;

        }

        $white = ImageColorAllocate ($im, 255, 255, 255);
        $fontColor = ImageColorAllocate ($im, 255, 255, 254);
        $bg = ImageColorAllocate ($im, 255, 100, 97);
        imageColorTransparent($im, $white);//透明

        $coordinates = [
            $minWidth, $height,
            0, $height,
            0, 0,
            $minWidthL, 0,
        ];
        ImageFilledPolygon($im, $coordinates, 4, $bg);

        imagefttext(
            $im, 12, 0, 13, 19, $fontColor, $font, $content
        );

        return $im;
    }

    /**
     * @function 等比缩放函数(以保存的方式实现)
     * @param string $picname 被缩放的处理图片源
     * @param int $maxX 缩放后图片的最大宽度
     * @param int $maxY 缩放后图片的最大高度
     * @param string $pre 缩放后图片名的前缀名
     * @return string 返回后的图片名称(带路径),如a.jpg --> s_a.jpg
     */
    public function scalePic($picname, $maxX = 100, $maxY = 100, $pre = 's_')
    {
        $info = getimagesize($picname); //获取图片的基本信息
        $width = $info[0];//获取宽度
        $height = $info[1];//获取高度
        //判断图片资源类型并创建对应图片资源
        $im = $this->getPicType($info[2], $picname);
        //计算缩放比例
        $scale = ($maxX / $width) > ($maxY / $height) ? $maxY / $height : $maxX / $width;
        //计算缩放后的尺寸
        $sWidth = floor($width * $scale);
        $sHeight = floor($height * $scale);
        //创建目标图像资源
        $nim = imagecreatetruecolor($sWidth, $sHeight);
        //等比缩放
        imagecopyresampled($nim, $im, 0, 0, 0, 0, $sWidth, $sHeight, $width, $height);

        //输出图像
        $newPicName = $this->write($picname, $pre, $nim);
        //释放图片资源
        imagedestroy($im);
        imagedestroy($nim);
        return $newPicName;
    }

    /**
     * function 判断并返回图片的类型(以资源方式返回)
     * @param int $type 图片类型
     * @param string $picname 图片名字
     * @return resource 返回对应图片资源
     */
    protected function getPicType($type, $picname)
    {
        $im=null;
        switch($type) {
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
     * @param resource $nim 要输出的图像资源
     * @return string 返回新的图片名
     */
    public function write($picname, $pre, $nim)
    {
        $info = getimagesize($picname);
        $picInfo = pathInfo($picname);
        $newPicName = $picInfo['dirname'].'/'.$pre.$picInfo['basename'];//输出文件的路径
        switch($info[2]) {
            case 1:
                imagegif($nim, $newPicName);
                break;
            case 2:
                imagejpeg($nim, $newPicName);
                break;
            case 3:
                imagepng($nim, $newPicName);
                break;
            case 4:
                imagewbmp($nim, $newPicName);
                break;
        }
        return $newPicName;
    }

}
