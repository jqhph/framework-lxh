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
    protected $height = 775;

    /**
     * 中文字体路径
     *
     * @var string
     */
    protected $font;

    protected $borderMargin = 20;

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
        'qrcode' => [
            'w' => 162, 'h' => 162
        ],
        'banner' => [
            'w' => 600,
            'h' => 273,
            'y' => 235
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

    /**
     * @var string
     */
    protected $tagText = '';

    /**
     * @var string
     */
    protected $userText = '';

    /**
     * @var string
     */
    protected $titleText = '';

    /**
     * @var string
     */
    protected $qrcodeText = '';

    /**
     * @var string
     */
    protected $subTitle = '';

    /**
     * @var string
     */
    protected $descText = '';

    /**
     * @var string
     */
    protected $priceText = '';

    /**
     * @var array
     */
    protected $discountText = [];

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

//        $size = getimagesize($path);
//        $width = $size[0];
//        $height = $size[1];
//
//        // 图片宽度比规定的宽度要大
//        if ($width > $this->option('banner', 'w')) {
//
//        }


        return $this;
    }

    protected function createHead()
    {
        return $this->create('head', function ($width, $height) {
            $startx = $this->borderMargin;//头部开始位置
            $starty = $this->borderMargin;

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

    protected function bannerY()
    {
        return $this->option('banner', 'y');
    }


    protected function createBannner()
    {
        return $this->create('banner', function ($w, $h) {
            $startx = 0;
            $starty = $this->bannerY();

            return [$startx, $starty];
        });
    }

    protected function qrcodeY()
    {
        return $this->bannerY() + $this->option('banner', 'h') + $this->borderMargin;
    }

    protected function createQrcode()
    {
        return $this->create('qrcode', function ($w, $h) {
            $startx = $this->width - $w - 42;//头部开始位置
            $starty = $this->qrcodeY();

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
        $this->bg = imagecreatetruecolor($this->width, $this->height);// 生成背景图片
        $color = imagecolorallocate($this->bg, 255, 255, 255); //设置白色背景
        imagefill($this->bg, 0, 0, $color);//背景色填充
    }

    public function tagText($text)
    {
        $this->tagText = &$text;

        return $this;
    }

    public function userText($text)
    {
        $this->userText = &$text;

        return $this;
    }

    public function title($text)
    {
        $this->titleText = &$text;

        return $this;
    }

    public function qrcodeText($text)
    {
        $this->qrcodeText = &$text;

        return $this;
    }

    public function subTitle($text)
    {
        $max = 15;
        if (($l = mb_strlen($text, 'utf8')) > $max) {
            $f = mb_substr($text, 0, $max);
            $n = mb_substr($text, $max, $l);
            $text = "$f\n{$n}";
        }

        $this->subTitle = $text;

        return $this;
    }

    public function desc($text)
    {
        $this->descText = &$text;

        return $this;
    }

    public function price($text)
    {
        $this->priceText = $text . ' 元';

        return $this;
    }


    public function discount($text, $title = '原价 ')
    {
        $this->discountText = [
            'price' => $text . ' 元',
            'title' => $title
        ];

        return $this;
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

        if ($this->tagText) {
            $tag = $this->createTag($this->tagText);
            $tagw = $this->option('tag', 'w');
            $tagh = $this->option('tag', 'h');
            imagecopyresampled(
                $this->bg, $tag, 0, $this->bannerY(), 0, 0, $tagw, $tagh, $tagw, $tagh
            );
        }

        $this->createTitle();
        $this->createSubTitle();
        $this->createUserText();
        $this->createPrice();
        if ($this->descText) {
            $this->createDesc();
        }
        if ($this->discountText) {
            $this->createDiscount();
        }
        if ($this->qrcodeText) {
            $this->createQrcodeText();
        }

        return $this;
    }

    protected function createUserText()
    {
        $col = imagecolorallocatealpha($this->bg, 0, 0, 0, 20);

        $size = 14;
        $width = $this->borderMargin + $this->option('head', 'w') + 18;
        $height = $this->borderMargin + 40;

        imagefttext(
            $this->bg, $size, 0, $width, $height, $col, $this->font, $this->userText
        );
    }

    /**
     * 子标题
     */
    protected function createSubTitle()
    {
        $col = imagecolorallocatealpha($this->bg, 85, 85, 85, 20);

        $size = 18;
        $width = $this->borderMargin;
        $height = $this->bannerY() + $this->option('banner', 'y') + 90;

        imagefttext(
            $this->bg, $size, 0, $width, $height, $col, $this->font, $this->subTitle
        );
        imagefttext(
            $this->bg, $size, 0, $width + 1, $height, $col, $this->font, $this->subTitle
        );
    }

    protected function createDesc()
    {
        $col = imagecolorallocatealpha($this->bg, 119, 119, 119, 20);

        $size = 13;
        $width = $this->borderMargin;
        $height = $this->bannerY() + $this->option('banner', 'y') + 160;

        imagefttext(
            $this->bg, $size, 0, $width, $height, $col, $this->font, $this->descText
        );
    }

    protected function createQrcodeText()
    {
        $col = imagecolorallocatealpha($this->bg, 0, 0, 0, 20);

        $size = 15;

        list($w, $h) = $this->getTextSize($size, $this->qrcodeText);

        $startx = $this->width - $w - 42 - 16;//头部开始位置
        $starty = $this->qrcodeY() + $h + 180;

        imagefttext(
            $this->bg, $size, 0, $startx, $starty, $col, $this->font, $this->qrcodeText
        );
        imagefttext(
            $this->bg, $size, 0, $startx + 1, $starty, $col, $this->font, $this->qrcodeText
        );
    }

    protected function createPrice()
    {
        $col = imagecolorallocatealpha($this->bg, 255, 69, 0, 20);
        $other = imagecolorallocatealpha($this->bg, 60, 60, 60, 20);

        $size = 18;
        $width = $this->borderMargin;
        $height = $this->bannerY() + $this->option('banner', 'y') + 200;

        imagefttext(
            $this->bg, $size, 0, $width, $height, $col, $this->font, $this->priceText
        );
        imagefttext(
            $this->bg, $size, 0, $width+1, $height, $col, $this->font, $this->priceText
        );

        list($priceW, $priceH) = $this->getTextSize($size, $this->priceText);

        imagefttext(
            $this->bg, 13, 0, $width + $priceW + 8, $height, $other, $this->font, '起'
        );
    }

    protected function createDiscount()
    {
        $col = imagecolorallocatealpha($this->bg, 85, 85, 85, 50);

        $price = $this->discountText['price'];
        $title = $this->discountText['title'];

        $size = 16;
        $width = $this->borderMargin;
        $height = $this->bannerY() + $this->option('banner', 'y') + 236;
        imagefttext(
            $this->bg, $size, 0, $width, $height, $col, $this->font, $title
        );

        list($titleW, $titleH) = $this->getTextSize($size, $title);

//        $pw = $this->getTextSize($size, $price);
        $px = $width + $titleW + 5;
//        $this->createLine($px - 4, $px + $pw, $height, $col);

        imagefttext(
            $this->bg, $size, 0, $px, $height, $col, $this->font, $price
        );
    }

    protected function createLine($startX, $length, $height, $color)
    {
        imageline($this->bg, $startX, $height, $startX + $length, $height, $color);
    }

    /**
     * 计算文字宽高
     *
     * @param $fontSize
     * @param $text
     * @return array
     */
    protected function getTextSize($fontSize, $text)
    {
        $box = @imageTTFBbox($fontSize, 0, $this->font, $text);
        $width = abs($box[4] - $box[0]);
        $height = abs($box[5] - $box[1]);

        return [$width, $height];
    }
    /**
     * 标题
     */
    protected function createTitle()
    {
        $col = imagecolorallocatealpha($this->bg, 0, 0, 0, 20);

        $size = 24;
        $width = $this->borderMargin;
        $height = 190;

        imagefttext(
            $this->bg, $size, 0, $width, $height, $col, $this->font, $this->titleText
        );
        imagefttext(
            $this->bg, $size, 0, $width + 1, $height, $col, $this->font, $this->titleText
        );
    }


    protected function createTag($content)
    {
        $fontSize = 12;
        $font = $this->font;
        $height = $this->option('tag', 'h');
        $im = ImageCreate($this->option('tag', 'w'), $height);

        // 计算文字宽度
        list($tw, $th) = $this->getTextSize($fontSize, $content);

        $minWidth = 10 + $tw + 12;
        $minWidthL = 18 + $tw + 12;

        $white = ImageColorAllocate ($im, 255, 255, 255);
        $fontColor = ImageColorAllocate ($im, 255, 255, 254);
        $bg = ImageColorAllocate ($im, 255,69,0);
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
