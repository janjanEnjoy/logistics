<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/4/1
 * Time: 13:33
 */


namespace JanjanEnjoy\Logistics\YouZheng;


use Illuminate\Support\Facades\Log;
use JanjanEnjoy\Logistics\Exceptions\LogisticsExcepition;

class YouzhengService
{
    /**
     * 下单取号：获取请求体xml
     * user: wangjunjie
     * @param array $params
     * @return string
     */
    public function getOrderLogisticsXml(Array $params, Array $config)
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
        $senderName = $config['sender_name'];

        $senderProvince = $config['sender_province'];
        $senderCity = $config['sender_city'];
        $senderDistrict = $config['sender_district'];
        $senderAddress = $config['sender_address'];
        //寄件人电话：
        $senderPhone = "";
        $senderMobile = "";
        if (strlen($config['sender_phone']) == 11) {
            $senderMobile = $config['sender_phone'];
        } else {
            $senderPhone = $config['sender_phone'];
        }

        //电商标识
        $ecCompanyId = $config['ec_company_id'];
        //大客户
        $sendNo = $config['send_no'];
        //电商客户标识
        $ecommerceUserId = $config['ecommerce_user_id'];
        //非成都本地和成都本地 快递类型区分
        $productAttr = $isChengdu ? $config['product_type']['in'] : $config['product_type']['out'];
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
     * 加密
     * user: wangjunjie
     * @param $data
     * @param $secret
     * @param $rawOutput //可选参数为true或false，机密后：true：16位，false：32位
     * @return string
     */
    public function getDigistData($data, $secret, $rawOutput)
    {
        return base64_encode(md5($data . $secret, $rawOutput));
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
    public function post($url, $querystring, $timeout)
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
     * 结果数据处理
     * user: wangjunjie
     * @param $result
     * @return mixed
     */
    public function handleOrderResult($result)
    {
        $xml = simplexml_load_string($result);
        $data = json_decode(json_encode($xml), TRUE);
        return $data;
    }

    /**
    生成签名的方法
    params    原始的输入参数数组 keyValuePairs
    api       调用服务API名
    version   服务版本
    ak        accessKey
    sk        secretKey

    返回 签名过的url请求数组
     */
    protected function sign($params = array(), $api, $version, $ak, $sk){

        $headers = array();
        $headers['_api_name'] = $api;
        $headers['_api_version'] = $version;
        $headers['_api_access_key'] = $ak;
        $headers['_api_timestamp'] = $this->get_millistime();

        $signParams = array();

        foreach ($params as $k => $v) {
            $signParams[$k] = $v;
        }

        foreach ($headers as $k => $v) {
            $signParams[$k] = $v;
        }

        ksort($signParams);

        //类似实现http_build_query生成url参数query
        $signstr = '';

        foreach ($signParams as $k => $v) {
            if ($k == '_api_signature')
                continue;
            if ($signstr != '')
                $signstr = $signstr."&";
            $signstr = $signstr.$k.'='.$v;
        }

        $signature = base64_encode(hash_hmac('sha1', $signstr, $sk, true));
        $headers['_api_signature'] =  $signature;

        //transfer header
        $theaders = array();
        foreach ($headers as $k => $v) {
            $theaders[] = $k.":".$v;
        }

        return $theaders;
    }

    /**使用POST方式调用HTTP服务的方法
    data      请求参数数组 (Key-Vale pairs)
    api       调用服务API名
    version   服务版本
    ak        accessKey
    sk        secretKey

    返回 从Http服务端返回的串
     */
    public function doPost($url, $data, $api, $version, $ak, $sk){
        $ch = curl_init();   // 初始化一个curl资源类型变量
        $headers = $this->sign($data,$api, $version, $ak, $sk);

        $pd = http_build_query($data,"","&");

        curl_setopt($ch, CURLOPT_POSTFIELDS, $pd);  // 设置POST传递的数据

        return $this->postInner($ch, $url, $headers);
    }

    /**
     * 供doPostXXX调用的内部方法，进行实际的POST调用，并返回调用结果
     */
    protected function postInner($ch, $url, $headers) {

        /*设置访问的选项*/
        curl_setopt($ch, CURLOPT_POST, true);  // 设置为POST传递形式
        $headers[] = 'Expect:';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //put signature-related headers

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // 启用时会将服务器返回的Location: 放在header中递归的返回给服务器，即允许跳转
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );  // 将获得的数据返回而不是直接在页面上输出
        //curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP );  // 设置访问地址用的协议类型为HTTP
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);  // 访问的超时时间限制为15s
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_USERAGENT, '');  // 将用户代理置空
        curl_setopt($ch, CURLOPT_HEADER, false);  // 设置不显示头信息
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在

        curl_setopt($ch, CURLOPT_URL, $url);  // 设置即将访问的URL
        $result = curl_exec($ch);  // 执行本次访问，返回一个结果
//        $info = curl_getinfo($ch);   // 获取本次访问资源的相关信息
//        print_r( $info);echo("\n");

        curl_close($ch);  // 关闭
        // ...                     // 针对结果的正确与否做一些操作
        return $result;
    }

    /** 获取毫秒级时间戳
     */
    protected function get_millistime()
    {
        $microtime = microtime();
        $comps = explode(' ', $microtime);
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }


}
