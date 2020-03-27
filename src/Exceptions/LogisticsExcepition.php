<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/24
 * Time: 9:42
 */

namespace JanjanEnjoy\Logistics\Exceptions;


class LogisticsExcepition extends \Exception
{
    protected static $errorMsg=[
        5010 => '未检测到配置文件',
        5011 => '暂未对接该快递类型',
        5012 => '下单取号请求错误',
        5013 => '请选择快递类型',
        5014 => "邮政下单取号失败"
    ];

    public function __construct($code)
    {
        parent::__construct(self::$errorMsg[$code], $code);
    }
}
