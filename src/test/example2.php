<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/30
 * Time: 14:44
 */

use JanjanEnjoy\Logistics\YouZheng\YouzhengLogistics;
include ("../Youzheng/YouzhengLogistics.php");
$obj = new YouzhengLogistics();
include("../Exceptions/LogisticsExcepition.php");
$params="1104846151425";

//  $requestData = [
//    "sendID" => "SCXTHK",
//    "proviceNo" => 99,
//    "msgKind" => "SCXTHK_JDPT_TRACE",
//    "serialNo" => "UUID",
//    "sendDate" => "YYYYMMDDHHMMSS",
//    "receiveID" => "JDPT",
//    "dataType" => 1,
//    "dataDigest" => "YTk4MjgyNTZkODRjZmRkN2UwOTk4OTY5YzI5N2E1NmU=",
////            "dataDigest" => $dataDigest,
//    "msgBody" => urlencode($msgBody)
//];
$res = $obj->queryLogistics($params);
