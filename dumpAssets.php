<?php
require_once("util.php");


if(count($argv) < 2){
    exit("请传入生成的缩略图大小，如php dumpAssets 50为生成50*50大小的缩略图\n");
}

$size = $argv[1];

@unlink("assets.txt");      //删除文件

$directory = "assets";
$mydir = dir($directory); 
while($file = $mydir->read())
{ 
    if((is_file("$directory/$file"))) 
    {
        thumbImage("$directory/$file",$size,$size,"$directory/thumb/$file");
        $s = serialize(indexImage(new Imagick("$directory/thumb/$file")));
        file_put_contents("assets.txt","$directory/thumb/$file|$s\n",FILE_APPEND);
    } 
     
} 
$mydir->close(); 
