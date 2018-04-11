<?php
define('ICON_SIZE', 60);

$static_thresholds = array();
$static_thresholds[5] = 25; // 配列indexは横にならぶアイコンの数を指す。代入されている値は、動かないアイコンの数を指す。
$static_thresholds[6] = 21;
$static_thresholds[7] = 19;
$static_thresholds[8] = 16;
$static_thresholds[9] = 15;
$static_thresholds[10] = 15;
$static_thresholds[11] = 13;
$static_thresholds[12] = 11;
$static_thresholds[13] = 10;

$file = fopen('./order.csv', 'r');
$index = 0;
$data = array();
while ($line = fgetcsv($file)) {
    // for ($l = 0; $l < count($line); $l++) {
    //     $data[$index][$l] = $line[$l];
    // }
    $data[$index][0] = $line[0];
    $data[$index][1] = $line[1];
    $data[$index][2] = $line[2];
    $index++;
}
fclose($file);

mkdir('./eye_positions/finding/static');
mkdir('./eye_positions/finding/dynamic');
mkdir('./eye_positions/pointing/static');
mkdir('./eye_positions/pointing/dynamic');

for ($i = 1; $i < count($data); $i += 2) {
    $x_num = $data[$i][1] / ICON_SIZE;
    if ($data[$i][0] < $static_thresholds[$x_num]) { // 動かないアイコンの場合
        rename('./eye_positions/finding/' . ($i - 1) . '.csv', './eye_positions/finding/static/' . ($i - 1) . '.csv');
        rename('./eye_positions/pointing/' . ($i - 1) . '.csv', './eye_positions/pointing/static/' . ($i - 1) . '.csv');
        rename('./eye_positions/finding/' . $i . '.csv', './eye_positions/finding/static/' . $i . '.csv');
        rename('./eye_positions/pointing/' . $i . '.csv', './eye_positions/pointing/static/' . $i . '.csv');
    } else {
        rename('./eye_positions/finding/' . ($i - 1) . '.csv', './eye_positions/finding/dynamic/' . ($i - 1) . '.csv');
        rename('./eye_positions/pointing/' . ($i - 1) . '.csv', './eye_positions/pointing/dynamic/' . ($i - 1) . '.csv');
        rename('./eye_positions/finding/' . $i . '.csv', './eye_positions/finding/dynamic/' . $i . '.csv');
        rename('./eye_positions/pointing/' . $i . '.csv', './eye_positions/pointing/dynamic/' . $i . '.csv');
    }
}

// 参考：http://d.hatena.ne.jp/omoon/20110527/1306475827
class ms_line_ending_filter extends php_user_filter
{
    function filter($in, $out, &$consumed, $closing) {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = preg_replace("/\n$/", "", $bucket->data);
            $bucket->data = preg_replace("/\r$/", "", $bucket->data);
            $bucket->data = $bucket->data . "\r\n";
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }
}