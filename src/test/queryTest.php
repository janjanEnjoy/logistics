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

$res = $obj->queryLogistics($params);
