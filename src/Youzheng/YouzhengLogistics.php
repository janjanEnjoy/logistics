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

    private $service;

    public function __construct()
    {
        try {
            $this->service = new YouzhengService();
            $allConfig = config('logistics_common') ?? include(dirname(__DIR__) . '/../config/logistics_common.php');
            //测试
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
            $xmlData = $this->service->getOrderLogisticsXml($params, $this->config);

            //签名
            $digistData = $this->service->getDigistData($xmlData, $this->config['partner_secret'], true);

            $requestData = [
                'logistics_interface' => $xmlData,
                'data_digest' => $digistData,
                'msg_type' => 'ORDERCREATE',
                'ecCompanyId' => $this->config['ec_company_id']
            ];
            $url = $this->config['order_logistics_url'];
            $result = $this->service->post($url, http_build_query($requestData), 2000);
            return $this->service->handleOrderResult($result);
        } catch (\Exception $e) {
            Log::error("邮政物流下单取号发生错误", [$e]);
            throw new LogisticsExcepition(5014);
        }
    }


    /**
     * 查询邮政物流接口
     * user: wangjunjie
     * @param $logisticsNum //自定义订单号
     * @return mixed
     * @throws LogisticsExcepition
     */
    public function queryLogistics($logisticsNum)
    {
        $url = $this->config['query_logistics_url'];
        $msgBody = json_encode(["traceNo" => $logisticsNum]);
        $dataDigest = $this->service->getDigistData($msgBody, $this->config['query_secret'], false);

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
        $result = $this->service->post($url, http_build_query($requestData), 2000);
        return json_decode($result, true);
    }

    /**
     * 获取邮政三段码
     * user: wangjunjie
     * @param $wayBillNo
     * @return bool|string
     * @throws LogisticsExcepition
     */
    public function routInfoQueryForPDD($wayBillNo, $senderAddress, $receiverAddress, $inCity)
    {
        $url = $this->config['rout_info_query_url'];
        $api = $this->config['api_name'];
        $ak = $this->config['ak'];
        $sk = $this->config['sk'];
        $version = $this->config['api_version'];

        //城市内为EMS,外为邮政包裹
        $wpCodeFlag = $inCity?"EMS":"YZXB";

        $data = [
            "wpCode" => $this->config['ec_company_id'].'='.$wpCodeFlag,
            "logisticsInterface" => json_encode(
                [[
                    "objectId" => $wayBillNo,
                    "senderAddress" => $senderAddress,
                    "receiverAddress" => $receiverAddress
                ]]),
            "dataDigest" => ""
        ];

        try {
            //POST调用
            $result = $this->service->doPost($url, $data, $api, $version, $ak, $sk);
            return $result;
            // 进行后续的结果处理
            // ...
        } catch (\Exception $e) {
            throw new LogisticsExcepition(5015);
        }
    }

}



