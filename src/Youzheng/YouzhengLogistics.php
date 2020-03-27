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

            $allConfig = config('logitics_common.youzheng') ?? include(dirname(__DIR__) . '/config/logistics_common.php');
            $this->config = $allConfig['youzheng'];
        } catch (\Exception $e) {
//            $this->config = include(dirname(__DIR__) . '/../config/logistics_common.php');
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
            $digistData = $this->getDigistData($xmlData);
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
            throw new LogisticsExcepition(5010);
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
        $dataDigest = $this->getDigistData(json_encode(["traceNo" => $logisticsNum]));

        $requestData = [
            "Content-Type:application/x-www-form-urlencoded; charset=UTF-8",
            "sendID" => $this->config['sendID'],
            "proviceNo" => 99,
            "msgKind" => $this->config['msgKind'],
            "serialNo" => $this->config['serialNo'],
            "sendDate" => date('YYYYMMDDHHMMSS', time()),
            "receiveID" => $this->config['receiveID'],
            "dataType" => 1,
            "dataDigest" => $dataDigest
        ];
        $result = $this->post($url, http_build_query($requestData), 2000);
        return $this->handleQueryResult($result);
    }

    private function handleQueryResult($result)
    {
        return null;
    }

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
        //寄件人
        $senderPhone = $this->config['xintian_phone'];
        $senderProvince = $this->config['xintian_province'];
        $senderCity = $this->config['xintian_city'];
        $senderDistrict = $this->config['xintian_district'];
        $senderAddress = $this->config['xintian_address'];
        //发货人
        $pickupName = $this->config['xintian_name'];
        $pickupPhone = $this->config['xintian_phone'];
        $pickupProvince = $this->config['xintian_province'];
        $pickupCity = $this->config['xintian_city'];
        $pickupDistrict = $this->config['xintian_district'];
        $pickupAddress = $this->config['xintian_address'];

        $ecCompanyId = $this->config['ec_company_id'];

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
    <ecommerce_user_id>2</ecommerce_user_id>
    <sender_type>1</sender_type>
    <sender_no></sender_no>
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
    <pickup_type></pickup_type>
    <pickup_pre_begin_time></pickup_pre_begin_time>
    <pickup_pre_end_time></pickup_pre_end_time>
    <payment_mode></payment_mode>
    <cod_flag></cod_flag>
    <cod_amount></cod_amount>
    <receipt_flag></receipt_flag>
    <receipt_waybill_no></receipt_waybill_no>
    <electronic_preferential_no></electronic_preferential_no>
    <electronic_preferential_amount></electronic_preferential_amount>
    <valuable_flag>0</valuable_flag>
    <sender_safety_code>0</sender_safety_code>
    <receiver_safety_code></receiver_safety_code>
    <note></note>
    <project_id></project_id>
    <sender>
        <name>心田花开</name>
        <post_code></post_code>
        <phone></phone>
        <mobile>$senderPhone</mobile>
        <prov>$senderProvince</prov>
        <city>$senderCity</city>
        <county>$senderDistrict</county>
        <address>$senderAddress</address>
    </sender>
    <pickup>
        <name>$pickupName</name>
        <post_code></post_code>
        <phone></phone>
        <mobile>$pickupPhone</mobile>
        <prov>$pickupProvince</prov>
        <city>$pickupCity</city>
        <county>$pickupDistrict</county>
        <address>$pickupAddress</address>
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

    private function getDigistData($data)
    {
        $parternID = $this->config['partner_secret'];
        return base64_encode(md5($data . $parternID, TRUE));
    }
}



