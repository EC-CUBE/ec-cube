<?php
/**
 * シンプルなパフォーマンス簡易チェック用スクリプト
 *
 *  必要に応じてカテゴリIDを変動させるなどのカスタマイズを行う用の元として
 */
require_once '../install.php';
define ('LOOP_COUNT', 10);

$url_base = SITE_URL;
$url_add = 'products/list.php?category_id=1';
$max_loop = LOOP_COUNT;
$max_time = "";
$min_time = "";
$sum_time = 0;
$count = 0;

//初回はキャッシュにわざと乗せるため計算対象外
$dummy = file_get_contents($url_base . $url_add);

for($i = 0; $i < $max_loop; $i++) {
    $time = microtime(true);
    $dummy = file_get_contents($url_base . $url_add);
    $elapsed = sfPrintTime($time);
    if($max_time == "" or $max_time < $elapsed) $max_time = $elapsed;
    if($min_time == "" or $min_time > $elapsed) $min_time = $elapsed;
    $sum_time += $elapsed;
    $count++;
}

printf("Max: %f sec / Min: %f sec / Avg: %f sec / Count: %d\n", $max_time, $min_time, $sum_time / $count, $count);


function sfPrintTime($start,$end = '') {
    if($end == '') $end = microtime(true);
    $elapsed = $end - $start;
    printf("Time: %f sec\n",$elapsed);
    return $elapsed;
}
?>
