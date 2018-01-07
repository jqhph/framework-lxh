function qrcode(shortUrl) {
    console.log(shortUrl);
    $(".infoRight").html('');
    $(".infoRight").qrcode({
        text: shortUrl,
        width: 100,
        height: 100
    });
}
/*
SELECT
a.*
FROM
    `sline_tuan` a
LEFT JOIN `sline_allorderlist` b ON (
    a.id = b.aid
AND b.typeid = 13
AND a.webid = b.webid
)
WHERE
a.ishidden = 0
AND a.endtime > unix_timestamp(now())
ORDER BY
IFNULL(b.displayorder, 9999) ASC,
    a.modtime DESC,
    a.addtime DESC
LIMIT 0, 10
 */
/*生成图片*/
function makePictureFn(domParent) {
    var that = this;
    $(".hasGetImg img").remove();
    this.popContent.popShow = !this.popContent.popShow;

    var dom = domParent == 1 ? $('#popContainerCon') : $('#popContainerCon2'); //你要转变的dom

    setTimeout(function () {
        var width = dom.width();
        var height = dom.height();
        var type = "png";
        var scaleBy = 3; //缩放比例
        var canvas = document.createElement('canvas');
        canvas.width = width * scaleBy;
        canvas.height = height * scaleBy;
        var context = canvas.getContext('2d');
        context.scale(scaleBy, scaleBy);

        var opts = {
            scale: scaleBy, // 添加的scale 参数
            canvas: canvas, //自定义 canvas
            logging: true, //日志开关
            width: width, //dom 原始宽度
            height: height, //dom 原始高度
            useCORS: true
        };

        html2canvas(dom[0], opts).then(function (canvas) {
            var context = canvas.getContext('2d');
            // context.clearRect(0,imgTop,canvas.width,imgHeight);
            var image = new Image();

            // 引用外部图片，需设置 crossOrigin 属性，否则 toDataURL 调用异常
            image.setAttribute('crossOrigin', 'anonymous');
            image.src = $('.addCity img').attr('src');
            image.onload = function (e) {

                // 把图片绘制在canvas特定位置上
                var imgHeight = $('.addCity').height();
                var canvasTop = $('.addCity').position().top * scaleBy;

                context.drawImage(image, 0, 0, image.width, image.height, 0, canvasTop, canvas.width, imgHeight * scaleBy);

                // 绘制文字背景矩形
                context.beginPath();
                context.fillStyle = '#ec252d';
                context.fillRect(0, canvasTop, that.addList.cityName.length * 40 + 20 * 2, 70);

                // 绘制三角形
                context.beginPath();
                context.moveTo(that.addList.cityName.length * 40 + 20 * 2, canvasTop);
                context.lineTo(that.addList.cityName.length * 40 + 20 * 2 + 20, canvasTop);
                context.lineTo(that.addList.cityName.length * 40 + 20 * 2, canvasTop + 70);
                context.fill();

                // 绘制文本
                context.font = '40px arial';
                context.fillStyle = "#fff";

                // 画布上输出文本之前，检查字体的宽度：
                context.fillText(that.addList.cityName, 20, canvasTop + 50);

                //如果想要生成图片 引入canvas2Image.js
                var img = Canvas2Image.convertToImage(canvas, canvas.width, canvas.height, type);
                $(".hasGetImg").append(img);

                that.popContent.popContainerHidden = true;
                that.hasGetImgShow = true;
            };
        });
    }, 1000);
}