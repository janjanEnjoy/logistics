<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/24
 * Time: 9:31
 */

/**
 * 临时本地test调试专用，正式laravel使用，将return拷贝入config目录即可
 * @param $a
 * @param null $b
 * @return null
 */
//function env($a,$b=null){
//    return $b;
//}

return [

    "youzheng" => [

        /************** 下单接口 ********************/
        //电商标识
        "ec_company_id" => env("YZ_EC_COMPANY_ID",""),
        //大客户代码
        "send_no" =>env("YZ_SEND_NO",""),
        //电商客户标识: 50位以下随机数
        "ecommerce_user_id" =>env("YZ_ECOMMERCE_USER_ID",""),

        //寄件人、发货人
        "sender_name" => env('XINTIAN_NAME',""),
        "sender_phone" => env("XINTIAN_PHONE",""),
        "sender_province" => env("XINTIAN_province",""),
        "sender_city" => env("XINTIAN_CITY",""),
        "sender_district" => env("XINTIAN_DISTRICT",""),
        "sender_address" => env("XINTIAN_ADDRESS",""),

        //基础产品代码
        /**
         * 成都以外 是 快递包裹 邮政：物流承包方A，
         * 成都以内 是 标准快递 EMS: 物流承包方B
         */
        'product_type' => [
            /*****成都地区以外*****/
            'out' => [
                //基础产品代码
                'base_product_no' => env("YZ_BASE_PRODUCT_NO_OUT",''), //-快递包裹
                //可售卖产品代码
                'biz_product_no' => env("YZ_BIZ_PRODUCT_NO_OUT",''), //-快递包裹
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
        "order_logistics_url" => env("YZ_ORDER_LOGISTICS_URL",""),
        //环境密钥
        "partner_secret" => env('YZ_PARTNER_SECRET',""),



        /************** 查询轨迹接口 ********************/
        //查询地址
        "query_logistics_url" => env("YZ_QUERY_LOGISTICS_URL",""),
        //发送方标识 ：如JDPT:寄递平台
        "sendId" => env("YZ_SEND_ID",""),
        //消息类别：如接口编码（JDPT_XXX_TRACE）
        "msgKind" => env("YZ_MSG_KIND",""),
        //消息唯一序列号：相对于消息类别的唯一流水号，对于本类消息，是一个不重复的ID值，不同类别的消息，该值会重复
        "serialNo" => env("YZ_SERIAL_NO",""),
        //代表接收方标识： XX：XX系统
        "receiveId" => env("YZ_RECEIVE_ID",""),
        "query_secret" => env("YZ_QUERY_SECRET",""),



        /************** 获取三段码接口 ********************/
        //请求地址
        "rout_info_query_url" =>env("YZ_ROUT_INFO_QUERY_URL",""),
        "sk" => env("YZ_SK",""),
        "ak" => env("YZ_AK",""),
        "api_name" => env("YZ_API_NAME",""),
        "api_version" => env("YZ_API_VERSION","1.0.0"),

    ]
];
