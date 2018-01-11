<?php
$url = "http://desk.zol.com.cn/1024x768_c{%1}/{%2}.html";

$color_num = 11;
$page_num = 7;

for($i = 1; $i <= $color_num;$i++){
    for($page = 1; $page <= $page_num;$page++){
        $new_url = str_replace("{%1}",$i,$url);
        $new_url = str_replace("{%2}",$page,$new_url);
        $content = file_get_contents($new_url);

        file_put_contents("text.txt",$content);

        $match_images = array();
        // <img width="208px" height="130px"  alt="浪漫爱情摄影桌面壁纸" src="https://desk-fd.zol-img.com.cn/t_s208x130c5/g5/M00/06/02/ChMkJ1hGHlqIK5-wAAQyacLPyTMAAYVPwHCDe8ABDKB659.jpg" title = "浪漫爱情摄影桌面壁纸"/>
        preg_match_all('|<img width="208px" height="130px"  alt=".*"\s+src="(.*)" title = ".*"/>|U',$content,$match_images);
        
        $count = 0;
        $cnum = "c".$i;
        $pnum = "p".$page;
        foreach($match_images[1] as $image_url){
            $image = file_get_contents($image_url);
            $format = substr($image_url,strlen($image_url) - 3,3);
            file_put_contents("assets/{$cnum}-{$pnum}-{$count}.$format",$image);
            $count++;
        }
    }
}