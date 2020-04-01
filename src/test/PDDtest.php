<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/4/1
 * Time: 15:07
 */
use JanjanEnjoy\Logistics\YouZheng\YouzhengLogistics;
include ("../Youzheng/YouzhengLogistics.php");
include("../Exceptions/LogisticsExcepition.php");
include("../Youzheng/YouzhengService.php");
$obj = new YouzhengLogistics();

$senderAddress = [
    "province"=>"浙江省",
    "city"=>"杭州市",
    "area"=>"余杭",
    "detail"=>"狮山路11号",
];
$receiverAddress = [
    "province"=>"江苏省",
    "city"=>"南京市",
    "area"=>"江宁",
    "detail"=>"东麒路33号A座",
];
$res=$obj->routInfoQueryForPDD("987654321",$senderAddress,$receiverAddress);
echo $res;

