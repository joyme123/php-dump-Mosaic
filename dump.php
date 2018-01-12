<?php
require_once("util.php");



/**
 * 余弦值计算
 */
function calculate($v1,$v2){
    $len = count($v1);
    $len2 = count($v2);
    if($len != $len2){
        exit("维度不同，无法计算,$len != $len2");
    }

    $sum1 = 0;
    $sum2 = 0;
    $sum3 = 0;

    for($i = 0; $i < $len; $i++){
        $sum1 += $v1[$i] * $v2[$i];
        $sum2 += $v1[$i] * $v1[$i];
        $sum3 += $v2[$i] * $v2[$i];
    }

    return $sum1 / (sqrt($sum2) * sqrt($sum3));
}


/**
 * 找出最相似的矩阵对应的图片
 */
function findSimilar($assets,$vector){
    $max = 0;
    $result = "";
    foreach($assets as $asset){
        $v = unserialize($asset[1]);
        $similar = calculate($v,$vector);
        if($similar > $max){
            $max = $similar;
            $result = $asset[0];
        }
    }

    return $result;     //返回图片地址
}

if(count($argv) >= 3){

    $start_memory = memory_get_usage();     //开始占用内存


    $filepath = $argv[1];       //要处理的图片路径
    $unit = $argv[2];           //处理的最小单位

    $imagick = new Imagick($filepath);
    $width = $imagick->getImageWidth();
    $height = $imagick->getImageHeight();
    
    $new_width = $width;
    $new_height = $height;

    if($width % $unit > 0){
        $new_width = $width - $width % $unit;
    }
    
    if($height % $unit > 0){
        $new_height = $height - $height % $unit;
    }

    $imagick->cropImage($new_width,$new_height,0,0);        ////图片处理成合适的尺寸
    $imagick->writeImage("dest.jpg");
    unset($imagick);                    //销毁资源

    //初始化资源
    $content = file_get_contents("assets.txt");
    $assets = explode("\n",$content);
    unset($content);                    //销毁资源
    array_pop($assets);                 //去除最后一个空行

    $len = count($assets);
    for($index = 0; $index < $len; $index++){
        $assets[$index] = explode("|",$assets[$index]);
    }

    $destImagick = new Imagick("dest.jpg");
    $mat = getColorMat($destImagick);

    $loop_count = 0;

    for($i = 0;$i * $unit < $new_height; $i++){
        for( $j = 0; $j * $unit < $new_width; $j++){
            $vector = getImageAreaVector($mat,$i * $unit,$j * $unit,$unit);
            $find_img = findSimilar($assets,$vector);
            $find_imagick = new Imagick($find_img);
            $destImagick->compositeImage ( $find_imagick , imagick::COMPOSITE_DEFAULT ,$j * $unit ,$i * $unit);
            $loop_count++;
        }
    }

    $destImagick->writeImage("dest-dump.jpg");

    $end_memory = memory_get_usage();     //结束占用内存
    echo "总共有".$loop_count."张贴图\n";
    echo '共占用内存:'.(($end_memory - $start_memory) / (1024 * 1024))."MB\n";

}else{
    echo "请传一个需要处理的图片\n";   
};

