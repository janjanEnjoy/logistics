<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/24
 * Time: 9:31
 */

function env($a,$b=null){
    return $b;
}

return [

    "youzheng" => [

        /************** 下单接口 ********************/
        //电商标识
        "ec_company_id" => env("YZ_EC_COMPANY_ID","SCXTHK"),
        //大客户代码
        "send_no" =>env("YZ_SEND_NO","1100043911134"),
        //电商客户标识: 50位以下随机数
        "ecommerce_user_id" =>env("YZ_ECOMMERCE_USER_ID","XT5316266362553788"),

        //寄件人、发货人
        "sender_name" => env('XINTIAN_NAME',"心田花开"),
        "sender_phone" => env("XINTIAN_PHONE","18382239352"),
        "sender_province" => env("XINTIAN_province","四川"),
        "sender_city" => env("XINTIAN_CITY","成都"),
        "sender_district" => env("XINTIAN_DISTRICT","武侯区"),
        "sender_address" => env("XINTIAN_ADDRESS","武侯区航空路北国航世纪中心B座附近"),

        //基础产品代码
        /**
         * 成都以外 是 快递包裹 邮政：物流承包方A，
         * 成都以内 是 标准快递 EMS: 物流承包方B
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
        //下单取号地址
        "order_logistics_url" => env("YZ_ORDER_LOGISTICS_URL","https://211.156.195.199/iwaybillno-web/a/iwaybill/receive"),
        //环境密钥
        "partner_secret" => env('YZ_PARTNER_SECRET',"key123xydJDPT"),



        /************** 查询轨迹接口 ********************/
        //查询地址
        "query_logistics_url" => env("YZ_QUERY_LOGISTICS_URL","http://211.156.195.198/querypush-twswn/mailTrack/queryMailTrackWn/plus"),
        //发送方标识 ：如JDPT:寄递平台
        "sendId" => env("YZ_SEND_ID","SCXTHK"),
        //消息类别：如接口编码（JDPT_XXX_TRACE）
        "msgKind" => env("YZ_MSG_KIND","SCXTHK_JDPT_TRACE"),
        //消息唯一序列号：相对于消息类别的唯一流水号，对于本类消息，是一个不重复的ID值，不同类别的消息，该值会重复
        "serialNo" => env("YZ_SERIAL_NO","UUID"),
        //代表接收方标识： XX：XX系统
        "receiveId" => env("YZ_RECEIVE_ID","JDPT"),
        "query_secret" => env("YZ_QUERY_SECRET","C71CD504AE3205B0"),



        /************** 获取三段码接口 ********************/
        //请求地址
        "rout_info_query_url" =>env("YZ_ROUT_INFO_QUERY_URL","http://211.156.195.42:8086/csb_ceshinew_1"),
        "sk" => env("YZ_SK","TYKveK6sGpNFL0JOC94l7ff9YwM="),
        "ak" => env("YZ_AK","136dce232ab245298a05faf9e2056e1b"),
        "api_name" => env("YZ_API_NAME","routInfoQueryForPDD1990"),
        "api_version" => env("YZ_API_VERSION","1.0.0"),

    ]
];
