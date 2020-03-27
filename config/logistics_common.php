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
        "ec_company_id" => env("YZ_EC_COMPANY_ID","SCXTHK"),

        //下单取号地址
        "order_logistics_url" => env("YZ_ORDER_LOGISTICS_URL","https://211.156.195.199/iwaybillno-web/a/iwaybill/receive"),

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

        //寄件人、发货人
        "xintian_name" => env('XINTIAN_NAME',"心田花开"),
        "xintian_phone" => env("XINTIAN_PHONE","123456789"),
        "xintian_province" => env("XINTIAN_province","四川"),
        "xintian_city" => env("XINTIAN_CITY","成都"),
        "xintian_district" => env("XINTIAN_DISTRICT","武侯区"),
        "xintian_address" => env("XINTIAN_ADDRESS","武侯区航空路北国航世纪中心B座附近"),

//        //发货人
//        "pickup_name" => "心田花开",
//        "pickup_phone" => "18382239352",
//        "pickup_province" => "四川",
//        "pickup_city" => "成都",
//        "pickup_district" => "武侯区",
//        "pickup_address" => "武侯区航空路北国航世纪中心B座附近",

        //基础产品代码
        /**
         * 成都以外 是 快递包裹 ：物流承包方A，
         * 成都以内 是 标准快递 : 物流承包方B
         */
        'product_type' => [
            /*****成都地区以外*****/
            'out' => [
                //基础产品代码
                'base_product_no' => env("YZ_BASE_PRODUCT_NO_OUT",'11312'), //-快递包裹
                //可售卖产品代码
                'biz_product_no' => env("YZ_BIZ_PRODUCT_NO_OUT",'113124300000691'), //-快递包裹
                //物流承运方
                'logistics_provider' => env("YZ_LOGISTICS_PROVIDER_OUT","A"), //A：邮务
            ],
            /*****成都地区以内*****/
            'in' => [
                //基础产品代码
                'base_product_no' => env('YZ_BASE_PRODUCT_NO_IN','21210'), //-标准快递
                //可售卖产品代码
                'biz_product_no' => env("YZ_BIZ_PRODUCT_NO_IN",'112104302300991'), //-标准快递
                //物流承运方
                'logistics_provider' => env("YZ_LOGISTICS_PROVIDER_IN","B"), //B：速递(快递)
            ]
        ],

        //环境密钥
        "partner_secret" => env('YZ_PARTNER_SECRET')
    ]
];
