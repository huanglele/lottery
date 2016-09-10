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

    private $model;

    public function run(&$params)
    {
        // TODO: Implement run() method.
        $this->model = new \Common\Model\DoubleColorModel();
        $this->checkTimes();
    }

    //检测期数是否正确
    public function checkTimes(){
        $real = $this->model->countCorrectTimes();
        $current = $this->model->getLastTimesInDatabse(true);
        if($current<$real){
            $this->startNewTimes();
        }
    }

    /**
     * 自动开启一期
     */
    private function startNewTimes(){
        $currentTimes = $this->model->getLastTimesInDatabse(true);
        $correctTimes = $this->model->countCorrectTimes();
        if($currentTimes<$correctTimes){
            $current_times = $currentTimes%1000;
            $correct_times = $correctTimes%1000;
            $max_imes  = readConf('doubleColorBallMaxTime') ? C('doubleColorBallMaxTime') : 154;  //当年最大期数
            if( $max_imes >= $current_times ){
                $data = array();
                $t['result'] = '';
                $t['time'] = time();
                $t['status'] = 3;   //默认发放完成
                if($current_times<$correct_times){
                    //在同一年
                    while ($currentTimes<$correctTimes){
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
                    while ($num<$correctTimes){
                        $num++;
                        $t['times'] = $num;
                        $data[] = $t;
                    }
                }
                $this->model->addAll($data);
                //修改当前期数状态
                $before_map['times'] = array('lt',$correctTimes);
                $before_map['status'] = 0;
                $this->model->where(array('times'=>$correctTimes))->setField('status',0);
            }
        }

    }




}