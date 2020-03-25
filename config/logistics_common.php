<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/24
 * Time: 9:31
 */
return [

    "youzheng" => [
        //电商标识
        "ec_company_id" => "",

        //下单地址
        "order_logistics_url" => "",

        //查询地址
        "query_logistics_url" => "",

        //发送方标识 ：如JDPT:寄递平台
        "sendId" => "",

        //消息类别：如接口编码（JDPT_XXX_TRACE）
        "msgKind" => "",

        //消息唯一序列号：相对于消息类别的唯一流水号，对于本类消息，是一个不重复的ID值，不同类别的消息，该值会重复
        "serialNo" => "",

        //代表接收方标识： XX：XX系统
        "receiveId" => "",

        //寄件人
        "xintian_phone"=> "",
        "xintian_provice" => "",
        "xintian_city" => "",
        "xintian_district" => "",
        "xintian_address" => "",

        //发货人
        "pickup_name" => "",
        "pickup_phone"=> "",
        "pickup_provice" => "",
        "pickup_city" => "",
        "pickup_district" => "",
        "pickup_address" => "",

        //
        "system_id" => "",
    ]
];
