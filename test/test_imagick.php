<?php
function getColorMat(Imagick $imagick){

    $it = new ImagickPixelIterator($imagick);
    
    $mat = array();
    
    while($row = $it->getNextIteratorRow()){
        if(count($row) == 0)
            break;
        $mat[] = $row;
    }

    $it->destroy();     //销毁资源

    return $mat;
}

$start = memory_get_usage();

    $imagick = new Imagick("../src.jpg");
    $mat = getColorMat($imagick);

$end = memory_get_usage();

echo '初始内存占用'.($start / (1024 * 1024))."MB\n";
echo '结束内存占用'.($end / (1024 * 1024))."MB\n";
echo '共占用:'.(($end - $start) / (1024 * 1024))."MB\n";


// file_put_contents("mat.txt",serialize($mat));