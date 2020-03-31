<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/24
 * Time: 14:41
 */


namespace JanjanEnjoy\Logistics\YouZheng;


use Illuminate\Support\Facades\Log;
use JanjanEnjoy\Logistics\Exceptions\LogisticsExcepition;

class YouzhengLogistics
{

    private $config;

    public function __construct()
    {
        try {
            $allConfig = config('logistics_common') ?? include(dirname(__DIR__) . '/../config/logistics_common.php');
//            $allConfig = include(dirname(__DIR__) . '/../config/logistics_common.php');
            $this->config = $allConfig['youzheng'];
        } catch (\Exception $e) {
            $allConfig = include(dirname(__DIR__) . '/../config/logistics_common.php');
            $this->config = $allConfig['youzheng'];
            Log::error("邮政物流下单取号请求错误", [$e]);
            throw new LogisticsExcepition(5010);
        }
    }

    /**
     * 下单获取运单号
     * user: wangjunjie
     * @param $params
     * @return mixed
     * @throws LogisticsExcepition
     */
    public function orderLogisticsNumber($params)
    {
        try {
            // 首先检测是否支持curl
            if (!extension_loaded("curl")) {
                trigger_error("对不起，请开启curl功能模块！", E_USER_ERROR);
            }

            // 构造xml数据
            $xmlData = $this->getOrderLogisticsXml($params);

            //签名
            $digistData = $this->getDigistData($xmlData, $this->config['partner_secret'], true);
            $requestData = [
                'logistics_interface' => $xmlData,
                'data_digest' => $digistData,
                'msg_type' => 'ORDERCREATE',
                'ecCompanyId' => $this->config['ec_company_id']
            ];
            $url = $this->config['order_logistics_url'];
            $result = $this->post($url, http_build_query($requestData), 2000);
            return $this->handleOrderResult($result);
        } catch (\Exception $e) {
            Log::error("邮政物流下单取号发生错误", [$e]);
            throw new LogisticsExcepition(5014);
        }
    }

    /**
     * 结果数据处理
     * user: wangjunjie
     * @param $result
     * @return mixed
     */
    private function handleOrderResult($result)
    {
        $xml = simplexml_load_string($result);
        $data = json_decode(json_encode($xml), TRUE);
        return $data;
    }

    /**
     * 查询邮政物流接口
     * user: wangjunjie
     * @param $logisticsNum
     * @return mixed
     * @throws LogisticsExcepition
     */
    public function queryLogistics($logisticsNum)
    {
        $url = $this->config['query_logistics_url'];
        $msgBody = json_encode(["traceNo" => $logisticsNum]);
        $dataDigest = $this->getDigistData($msgBody, $this->config['query_secret'], false);

        $requestData = [
            "sendID" => $this->config['sendId'],
            "proviceNo" => 99,
            "msgKind" => $this->config['msgKind'],
            "serialNo" => $this->config['serialNo'],
            "sendDate" => date('Ymdhis', time()),
            "receiveID" => $this->config['receiveId'],
            "dataType" => 1,
            "dataDigest" => $dataDigest,
            "msgBody" => urlencode($msgBody)
        ];
        $result = $this->post($url, http_build_query($requestData), 2000);
        return json_decode($result, true);
    }

    /**
     * 下单取号：获取请求体xml
     * user: wangjunjie
     * @param array $params
     * @return string
     */
    private function getOrderLogisticsXml(Array $params)
    {
        //创建时间
        $now = date('Y-m-d H:i:s', time());

        //心田订单号
        $logisticsOrderNumber = $params['order_sn'];

        //收货人信息
        $receiveName = $params['receive_name'];
        $receivePhone = $params['receive_phone'];
        $receiveProvince = $params['receive_province'];
        $receiveCity = $params['receive_city'];
        $receiveDistrict = $params['receive_district'];
        $receiveAddress = $params['receive_address'];

        $isChengdu = $params['is_chengdu'];

        /****以下信息 均从config 配置信息中读取*****/
        //***寄件人
        $senderName = $this->config['sender_name'];

        $senderProvince = $this->config['sender_province'];
        $senderCity = $this->config['sender_city'];
        $senderDistrict = $this->config['sender_district'];
        $senderAddress = $this->config['sender_address'];
        //寄件人电话：
        $senderPhone = "";
        $senderMobile = "";
        if (strlen($this->config['sender_phone']) == 11) {
            $senderMobile = $this->config['sender_phone'];
        } else {
            $senderPhone = $this->config['sender_phone'];
        }

        //电商标识
        $ecCompanyId = $this->config['ec_company_id'];
        //大客户
        $sendNo = $this->config['send_no'];
        //电商客户标识
        $ecommerceUserId = $this->config['ecommerce_user_id'];
        //非成都本地和成都本地 快递类型区分
        $productAttr = $isChengdu ? $this->config['product_type']['in'] : $this->config['product_type']['out'];
        $baseProductNo = $productAttr['base_product_no'];
        $bizProductNo = $productAttr['biz_product_no'];
        $logisticsProvider = $productAttr['logistics_provider'];

        //物流产品发送
        $materials = $params['materials'];
        $cargos = "";

        foreach ($materials as $material) {
            $materialName = $material['material_name'];
            $materialNum = $material['material_num'];
            $cargos .= <<< CARGO
        <Cargo>
            <cargo_name>$materialName</cargo_name>
            <cargo_category></cargo_category>
            <cargo_quantity>$materialNum</cargo_quantity>
            <cargo_value></cargo_value>
            <cargo_weight></cargo_weight>
        </Cargo>
CARGO;
        }


        $xmlData = <<< XML
<OrderNormal>
    <created_time>$now</created_time>
    <logistics_provider>$logisticsProvider</logistics_provider>
    <ecommerce_no>$ecCompanyId</ecommerce_no>
    <ecommerce_user_id>$ecommerceUserId</ecommerce_user_id>
    <sender_type>1</sender_type>
    <sender_no>$sendNo</sender_no>
    <inner_channel>0</inner_channel>
    <logistics_order_no>$logisticsOrderNumber</logistics_order_no>
    <batch_no></batch_no>
    <waybill_no></waybill_no>
    <one_bill_flag></one_bill_flag>
    <submail_no></submail_no>
    <one_bill_fee_type></one_bill_fee_type>
    <contents_attribute></contents_attribute>
    <base_product_no>$baseProductNo</base_product_no>
    <biz_product_no>$bizProductNo</biz_product_no>
    <product_type></product_type>
    <weight></weight>
    <volume></volume>
    <length></length>
    <width></width>
    <height></height>
    <postage_total></postage_total>
    <pickup_notes></pickup_notes>
    <insurance_flag>1</insurance_flag>
    <insurance_amount></insurance_amount>
    <deliver_type></deliver_type>
    <deliver_pre_date></deliver_pre_date>
    <pickup_type>1</pickup_type>
    <pickup_pre_begin_time></pickup_pre_begin_time>
    <pickup_pre_end_time></pickup_pre_end_time>
    <payment_mode>1</payment_mode>
    <cod_flag>9</cod_flag>
    <cod_amount></cod_amount>
    <receipt_flag>1</receipt_flag>
    <receipt_waybill_no></receipt_waybill_no>
    <electronic_preferential_no></electronic_preferential_no>
    <electronic_preferential_amount></electronic_preferential_amount>
    <valuable_flag>0</valuable_flag>
    <sender_safety_code>0</sender_safety_code>
    <receiver_safety_code></receiver_safety_code>
    <note></note>
    <project_id></project_id>
    <sender>
        <name>$senderName</name>
        <post_code></post_code>
        <phone>$senderPhone</phone>
        <mobile>$senderMobile</mobile>
        <prov>$senderProvince</prov>
        <city>$senderCity</city>
        <county>$senderDistrict</county>
        <address>$senderAddress</address>
    </sender>
    <pickup>
        <name>$senderName</name>
        <post_code></post_code>
        <phone>$senderPhone</phone>
        <mobile>$senderMobile</mobile>
        <prov>$senderProvince</prov>
        <city>$senderCity</city>
        <county>$senderDistrict</county>
        <address>$senderAddress</address>
    </pickup>
    <receiver>
        <name>$receiveName</name>
        <post_code></post_code>
        <phone></phone>
        <mobile>$receivePhone</mobile>
        <prov>$receiveProvince</prov>
        <city>$receiveCity</city>
        <county>$receiveDistrict</county>
        <address>$receiveAddress</address>
    </receiver>
    <cargos>
        $cargos
    </cargos>
</OrderNormal>
XML;
        return $xmlData;
    }

    /**
     * 发送请求
     * user: wangjunjie
     * @param $url
     * @param $querystring
     * @param $timeout
     * @return bool|string
     * @throws LogisticsExcepition
     */
    private function post($url, $querystring, $timeout)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:application/x-www-form-urlencoded; charset=UTF-8"));//设置HTTP头
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $querystring);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);

        // 是否报错
        if (curl_errno($ch)) {
            Log::error("邮政物流下单取号请求错误", [curl_error($ch)]);
            throw new LogisticsExcepition(5012);
        }
        curl_close($ch);    // //关闭cURL资源，并且释放系统资源
        return $response;
    }

    /**
     * 加密
     * user: wangjunjie
     * @param $data
     * @param $secret
     * @param $rawOutput //可选参数为true或false，机密后：true：16位，false：32位
     * @return string
     */
    private function getDigistData($data, $secret, $rawOutput)
    {
        return base64_encode(md5($data . $secret, $rawOutput));
    }


}



