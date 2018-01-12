# php-dump-Mosaic

使用环境:php7.0 + imagick扩展

## 支持以下指令

 -  1.php getImage.php

这个负责从网上下载素材照片，不过素材已经下好了，在assets里面，不用重复下载

 -  2.php dumpAssets.php 20  
处理素材图片，参数`20`代表生成的缩略图大小

 -  3.php dump.php src.jpg 20
生成最终要生成的图片，src.jpg是要处理的源图像，生成的图片名为dest-dump,`20`是多少个像素作为一个处理单位，和第二条指令中的`20`需要保持一致。

效果图如下：
![](https://github.com/joyme123/php-dump-Mosaic/blob/master/dest-dump.jpg)