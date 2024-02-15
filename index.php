<?php

ini_set( 'display_errors', 1 );
include("csv.php");

$base = './';
$dbbase = 'csv/';
$dbId = "id";//空の場合は最初の項目
$dbfile = 'name';
$charset = "utf-8";
$change = 'change.txt';
$datename = '';
$weekdayname = '曜日';
$expired = '';//期限切れ年月日
$relationdb = '';//連携ファイル
$relationid = 'id';//連携項目

$wdays = ["日", "月", "火", "水", "木", "金", "土"];

$items = 100;
$sort = $_GET["o"] ?? "";//逆順の場合は「-名前」
$sortbynumber = "";//;で区切る

readCSV();

$pos = $_GET["p"] ?? 0;

if (!empty($change)) {
    $fh_change = @fopen($change, "r");
    if (!empty($fh_change)) {
        ob_start();
    }
}
$data = empty($_GET["c"]) ? "" : $csv[$csvIx[$_GET["c"]]];
if (empty($data)) {
    include("list.php");
} else {
    include("details.php");
}
changeBody($fh_change);
