<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/27
 * Time: 14:59
 */

use JanjanEnjoy\Logistics\YouZheng\YouzhengLogistics;

$params = [
    "order_sn" => "1234567",
    "receive_name" => "古古怪怪",
    "receive_phone" => "18382239352",
    "receive_province" => "四川",
    "receive_city" => "成都",
    "receive_district" => "成华区",
    "receive_address" => "成都市成华区",
    "is_chengdu" => true,
    "materials" => [
        0 => [
            "material_name" => "1年级语文",
            "material_num" => 2
        ],
        1 => [
            "material_name" => "2年级语文",
            "material_num" => 3
        ]
    ]
];
$obj = new YouzhengLogistics();
include("../Exceptions/LogisticsExcepition.php");
$res = $obj->orderLogisticsNumber($params);

print_r($res);
