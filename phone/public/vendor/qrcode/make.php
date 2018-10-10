<?php

$url = isset($_GET['param']) ? $_GET['param'] : '';
require dirname(__FILE__) . '/phpqrcode.php';
$errorCorrectionLevel = "L";
$matrixPointSize = 8;
$matrixPointSize = empty($matrixPointSize) ? 8 : $matrixPointSize;
QRcode::png($url, false, $errorCorrectionLevel, $matrixPointSize);