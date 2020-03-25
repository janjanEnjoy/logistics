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
//        $config=!empty($config)?:(config('logitics_common')include(dirname(__DIR__).'/config/logistics_common.php'));
        try {
//            if (empty($config)) {
                $this->config = config('logitics_common.youzheng');
//            }
        } catch (\Exception $e) {
            $this->config = include(dirname(__DIR__) . '/config/logistics_common.php');
//            throw new LogisticsExcepition(5010);
        }
    }

    /**
     * 下单获取运单号
     * user: wangjunjie
     * @param $params
     * @throws LogisticsExcepition
     */
    public function orderLogisticsNumber($params)
    {
// 首先检测是否支持curl
        if (!extension_loaded("curl")) {
            trigger_error("对不起，请开启curl功能模块！", E_USER_ERROR);
        }

// 构造xml数据
        $xmlData = $this->getOrderLogisticsXml($params);
        $digistData = $this->getDigistData($xmlData);

        $requestData = [
            'logistics_interface' => urlencode($xmlData),
            'data_digest' => urlencode($digistData),
            'msg_type' => 'ORDERCREATE',
            'ecCompanyId ' => $this->config['ec_company_id']
        ];

        $url = $this->config['order_logistics_url'];
//        $header = Array("Content-Type:application/x-www-form-urlencoded; charset=UTF-8");

        $result = $this->post($url, http_build_query($requestData), 2000);

        return $this->handleOrderResult($result);
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
        $dataDigest=$this->getDigistData(json_encode(["traceNo"=>$logisticsNum]));

        $requestData = [
            "Content-Type:application/x-www-form-urlencoded; charset=UTF-8",
            "sendID" =>$this->config['sendID'],
            "proviceNo" => 99,
            "msgKind" =>$this->config['msgKind'],
            "serialNo" =>$this->config['serialNo'],
            "sendDate"=>date('YYYYMMDDHHMMSS', time()),
            "receiveID" => $this->config['receiveID'],
            "dataType" => 1,
            "dataDigest" =>$dataDigest
        ];
        $result = $this->post($url, http_build_query($requestData), 2000);
        return $this->handleQueryResult($result);
    }

    private function handleOrderResult($result)
    {
        $xml = simplexml_load_string($result);
        printf($xml);
    }

    private function handleQueryResult($result){
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

        /****以下信息 均从config 配置信息中读取*****/
        //寄件人
        $senderPhone = $this->config['xintian_phone'];
        $senderProvince = $this->config['xintian_province'];
        $senderCity = $this->config['xintian_city'];
        $senderDistrict = $this->config['xintian_district'];
        $senderAddress = $this->config['xintian_address'];
        //发货人
        $pickupName = $this->config['pickup_name'];
        $pickupPhone = $this->config['pickup_phone'];
        $pickupProvince = $this->config['pickup_province'];
        $pickupCity = $this->config['pickup_city'];
        $pickupDistrict = $this->config['pickup_district'];
        $pickupAddress = $this->config['pickup_address'];


        return $xmlData = "
     <OrderNormal>
            <created_time>$now</created_time>
            <logistics_provider>A</logistics_provider>
            <ecommerce_no>Taobao</ecommerce_no>
            <ecommerce_user_id></ecommerce_user_id>
            <sender_type></sender_type>
            <sender_no></sender_no>
            <inner_channel>0</inner_channel>
            <logistics_order_no>$logisticsOrderNumber</logistics_order_no>
            <batch_no></batch_no>
            <waybill_no></waybill_no>
            <one_bill_flag>0</one_bill_flag>
            <submail_no>9</submail_no>
            <one_bill_fee_type></one_bill_fee_type>
            <contents_attribute>0</contents_attribute>
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
            <sender_safety_code></sender_safety_code>
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
                <countpi$pickupDistrict</county>
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
                <Cargo>
                    <cargo_name>风衣</cargo_name>
                    <cargo_category></cargo_category>
                    <cargo_quantity></cargo_quantity>
                    <cargo_value></cargo_value>
                    <cargo_weight></cargo_weight>
                </Cargo>
                <Cargo>
                    <cargo_name>帽子</cargo_name>
                    <cargo_category></cargo_category>
                    <cargo_quantity></cargo_quantity>
                    <cargo_value></cargo_value>
                    <cargo_weight></cargo_weight>
                </Cargo>
            </cargos>
        </OrderNormal>";
    }

    private function post($url, $querystring, $timeout)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:application/x-www-form-urlencoded; charset=UTF-8"));//设置HTTP头
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//设置HTTP头
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $querystring);
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
        $parentId = $this->config['system_id'];
        return base64_encode(md5($data . $parentId, TRUE));
    }



}
