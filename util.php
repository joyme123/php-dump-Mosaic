<?php
/**
 * 获取图片的颜色矩阵
 * @param 图片地址
 * @return 
 */
function getColorMat(Imagick $imagick){

    $it = new ImagickPixelIterator($imagick);
    
    $mat = array();
    
    while($row = $it->getNextIteratorRow()){
        if(count($row) == 0)
            break;
        $rowColor = array();
        foreach($row as &$pixel){
            $rowColor[] = $pixel->getColor();
        }
        $mat[] = $rowColor;
        unset($rowColor);
    }

    $it->destroy();     //销毁资源

    return $mat;
}

/**
 * 创建缩略图
 */
function thumbImage($filepath,$width,$height,$outFilepath){
    $imagick = new Imagick($filepath);
    $thumb = $imagick->cropThumbnailImage($width,$height);
    $imagick->writeImage( $outFilepath );
}

/**
 * 获取分割范围的索引，    //颜色分区:0~3,范围是：0～63为第0区，64～127为第1区，128～191为第2区，192～255为第3区
 * @param int 颜色值
 */
function getSplitRnageIndex($colorChannel){
    if($colorChannel >= 0 && $colorChannel <= 63){
        return 0;
    }else if($colorChannel >= 64 && $colorChannel <= 127){
        return 1;
    }else if($colorChannel >= 128 && $colorChannel <= 191){
        return 2;
    }else if($colorChannel >= 192 && $colorChannel <= 255){
        return 3;
    }
}

/**
 * 生成图片指纹
 */
function indexImage(Imagick $imagick){
    //http://www.ruanyifeng.com/blog/2013/03/similar_image_search_part_ii.html

    $mat = getColorMat($imagick);


    $indexs = array(
        array(
            array(0,0,0,0),array(0,0,0,0),array(0,0,0,0),array(0,0,0,0)
        ),
        array(
            array(0,0,0,0),array(0,0,0,0),array(0,0,0,0),array(0,0,0,0)
        ),
        array(
            array(0,0,0,0),array(0,0,0,0),array(0,0,0,0),array(0,0,0,0)
        ),
        array(
            array(0,0,0,0),array(0,0,0,0),array(0,0,0,0),array(0,0,0,0)
        ),
    );

    $row = count($mat);
    if($row <= 0)
        exit("图片读取不正确");
    $col = count($mat[0]);

    for($i = 0; $i < $row; $i++){
        for($j = 0; $j < $col;$j++){
            $colors = $mat[$i][$j];
            $indexs[getSplitRnageIndex($colors['r'])][getSplitRnageIndex($colors['g'])][getSplitRnageIndex($colors['b'])]++;
        }
    }

    $result = array();
    array_walk_recursive($indexs, function($value) use (&$result) {
        array_push($result, $value);
    });
    return $result;
}

/**
 * 获取图片指定区域的指纹
 */
function getImageAreaVector($mat,$row,$col,$size){

    $indexs = array(
        array(
            array(0,0,0,0),array(0,0,0,0),array(0,0,0,0),array(0,0,0,0)
        ),
        array(
            array(0,0,0,0),array(0,0,0,0),array(0,0,0,0),array(0,0,0,0)
        ),
        array(
            array(0,0,0,0),array(0,0,0,0),array(0,0,0,0),array(0,0,0,0)
        ),
        array(
            array(0,0,0,0),array(0,0,0,0),array(0,0,0,0),array(0,0,0,0)
        ),
    );

    for($i = $row; $i < $row + $size; $i++){
        for($j = $col; $j < $col + $size;$j++){
            $colors = $mat[$i][$j];
            $indexs[getSplitRnageIndex($colors['r'])][getSplitRnageIndex($colors['g'])][getSplitRnageIndex($colors['b'])]++;
        }
    }

    $result = array();
    array_walk_recursive($indexs, function($value) use (&$result) {
        array_push($result, $value);
    });
    return $result;
}