<?php
/**
 * Created by PhpStorm.
 * User: huanglele
 * Date: 2016/9/4
 * Time: 下午 10:59
 */

namespace Common\Model;


use Think\Model;

class DoubleColorModel extends Model
{


    //获取当前最新一期的基本信息
    public function getCurrentTimesInfo(){
        return $info = $this->order('times desc')->find();
    }

}