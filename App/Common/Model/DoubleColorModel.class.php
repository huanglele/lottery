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
    public function getLastTimesInfo(){
        return $info = $this->order('times desc')->find();
    }

    //获取数据库里面当前进行的一起的数据
    public function getCurrentTimesInfo(){
        $CorrectTimes = $this->countCorrectTimes();
        return $info = $this->where(array('times'=>$CorrectTimes))->find();
    }

    /**
     * 计算当前实际应该是多少期
     */
    public function countCorrectTimes(){
        if(isset($this->CorrectTimes)){
            return $this->CorrectTimes;
        }else {
            $t = date('y,W,w,H,i'); //两位年数，第几周，星期中的第几天（0表示星期天）到 6（表示星期六），
            list($year, $week, $day, $hour, $min) = explode(',', $t);
            $i = ($day * 24 + $hour) * 60 + $min;
            $times = $year * 1000 + $week * 3 - 3;
            //17:25->1165  21:15->1275  24:00->1440
            if ($i < 2605) {
                //还在星期日的那一期
            } else if ($i > 2715 && $i < 5485) {
                //星期二 - 星期四
                $times++;
            } else if ($i > 5595 && $i < 9805) {
                $times += 2;
            }
            return $times - 1;
        }
    }

    /**
     * 获取当前系统中的最新一期双色球期数
     * @param  bool $cache 从缓存中读取数据
     * @return number $times 当前期数
     */
    public function getLastTimesInDatabse($cache = false){
        $times = S('doubleColorBallLastTimesInDatabase');
        if($cache || !$times){
            $info = $this->getLastTimesInfo();
            if($info){
                $times = $info['times'];
            }else{
                $times = 0;
            }
            S('doubleColorBallLastTimesInDatabase',$times);
        }
        return $times;
    }

    /**
     * 检测当前时间是否允许购买
     * @return bool
     */
    public function checkTimeForBuy(){
        $day = date('w');
        $arr = array(0,2,4);
        $min = intval( (NOW_TIME-strtotime(date('Y-m-d'))) /60);
        if( in_array($day,$arr) && ($min > 1165) && ($min < 1276)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 购买双色球
     * @param $options 购买的求号
     * @param $times 购买的期数
     * @return bool|string 购买成功返回true，失败返回失败原因
     */
    public function buy($options,$times){
        $options_num = count($options);
        if(!$options){
            return '没有投注';
        }
        if($this->countCorrectTimes()!=$times){
            return '当前期数已结束';
        }
        if(!$this->checkTimeForBuy()){
            return '当前已经停止购买';
        }
        $all_group_num = 0;
        $need_coin = 0;
        $data = array();
        $t['uid'] = session('uid');
        $t['buy_time'] = NOW_TIME;
        $t['status'] = 1;
        $t['times'] = $times;
        foreach ($options as $option){
            $red = $option['red'];
            $blue = $option['blue'];
            $num = $this->factorial(count($red)) / 720 / $this->factorial(count($red)-6) * count($blue);
            if($num) {
                $t['cost'] = $num * 200;
                $need_coin += $t['cost'];
                $all_group_num += $num;
                $t['option'] = json_encode($option);
                $data[] = $t;
            }
        }
        if(!count($data)){
            return '投注参数错误';
        }
        $left_coin = session('coin');
        if($left_coin<$need_coin){
            return '金豆不足';
        }
        $User = M('user');
        $User->startTrans();
        $r1 = $User->where(array('id'=>session('uid')))->setDec('coin',$need_coin);

        $coin['uid'] = session('uid');
        $coin['amount'] = $need_coin;
        $coin['time'] = date('Y-m-d H:i:s',NOW_TIME);
        $coin['type'] = -1;
        $coin['note'] = "购买 $all_group_num 注$times 期双色球";

        $r2 = M('coin')->add($coin);

        $r3 = M('double_color_record')->addAll($data);
        if($r1 && $r2 && $r3){
            $User->commit();
            session('coin',$left_coin-$need_coin);
            return true;
        }else{
            return '购买失败';
        }
    }

    // 阶乘函数
    private function factorial($n) {
        return ( $n <= 1 ) ? 1 : $n * $this->factorial($n - 1);
    }

    //
    public function getLotteryTime(){
        
    }

}