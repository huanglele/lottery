<?php
/**
 * Created by PhpStorm.
 * User: huanglele
 * Date: 2016/9/4
 * Time: 下午 11:16
 */

namespace Common\Behavior;


use Think\Behavior;

class CheckDoubleColorBehavior extends Behavior
{

    private $model = null;

    public function run(&$params)
    {
        // TODO: Implement run() method.
        $this->model = new \Common\Model\DoubleColorModel();
        $this->checkTimes();
    }

    //检测期数是否正确
    public function checkTimes(){
        $real = $this->countCorrectTimes();
        $current = $this->getCurrentTimesInDatabse(true);
        if($current<$real){
            $this->startNewTimes();
        }
    }

    /**
     * 自动开启一期
     */
    private function startNewTimes(){
        $currentTimes = $this->getCurrentTimesInDatabse(true);
        $correctTimes = $this->countCorrectTimes();
        if($currentTimes<$correctTimes){
            $current_times = $currentTimes%1000;
            $correct_times = $correctTimes%1000;
            $max_imes  = readConf('doubleColorBallMaxTime') ? C('doubleColorBallMaxTime') : 154;  //当年最大期数
            if( $max_imes >= $current_times ){
                $data = array();
                $t['result'] = '';
                $t['time'] = time();
                if($current_times<$correct_times){
                    //在同一年
                    while ($currentTimes<=$correctTimes){
                        $currentTimes++;
                        $t['times'] = $currentTimes;
                        $data[] = $t;
                    }
                }else{
                    //跨年
                    while ($current_times<=$max_imes){
                        $currentTimes++;
                        $current_times++;
                        $t['times'] = $currentTimes;
                        $data[] = $t;
                    }
                    $num = date('y')*1000;
                    while ($num<=$correctTimes){
                        $num++;
                        $t['times'] = $num;
                        $data[] = $t;
                    }
                }
                $this->model->addAll($data);
            }
        }

    }

    /**
     * 获取当前系统中的最新一期双色球期数
     * @param  bool $cache 允许从缓存中读取数据
     * @return number $times 当前期数
     */
    public function getCurrentTimesInDatabse($cache = false){
        $times = S('doubleColorBallCurrentTimesInDatabase');
        if($cache || !$times){
            $info = $this->model ->getCurrentTimesInfo();
            if($info){
                $times = $info['times'];
            }else{
                $times = 0;
            }
            S('doubleColorBallCurrentTimesInDatabase',$times);
        }
        return $times;
    }

    /**
     * 计算当前实际应该是多少期
     */
    private function countCorrectTimes(){
        if(isset($this->CorrectTimes)){
            return $this->CorrectTimes;
        }else {
            $t = date('y,W,w,H,i'); //两位年数，第几周，星期中的第几天0（表示星期天）到 6（表示星期六），
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
}