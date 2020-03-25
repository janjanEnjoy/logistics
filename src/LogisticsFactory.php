<?php
/**
 * Created by PhpStorm.
 * User: wangjunjie
 * Date: 2020/3/24
 * Time: 10:01
 */


namespace JanjanEnjoy\Logistics;


use JanjanEnjoy\Logistics\Exceptions\LogisticsExcepition;
use JanjanEnjoy\Logistics\YouZheng\YouzhengLogistics;

class LogisticsFactory
{


    const YouZheng='youzhengLogistics';

    public static function getInstance($logiticsClass){
        try {
            if($logiticsClass=='youzheng'){
                return new YouzhengLogistics();
            }
        }catch(\Exception $e){
            throw new LogisticsExcepition(5011);
        }
        throw new LogisticsExcepition(5013);
    }


}
