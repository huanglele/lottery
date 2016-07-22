<?php
/**
 * Author: huanglele
 * Date: 2016/7/22
 * Time: 下午 09:53
 * Description:
 */

namespace Home\Controller;

//球赛基础类
class BallBaseController extends CommonController
{



    public function getMatchList($ord)
    {
        $list = M('match')->order('times desc')->where(array('is_show'=>1,'result'=>array('in',array(1,2))))->limit(5)->select();

    }

    /**
     * 把获取到的比赛信息合并进球队信息
     * @param $list array 从match表里面拉取的数据
     * @return array
     */
    protected function mergeMatchInfo($list)
    {
        $data = array();
        if(count($list)){
            $teamIdArr = array();
            foreach($list as $k=>$v){
                $teamIdArr[] = $v['host_id'];
                $teamIdArr[] = $v['guess_id'];
            }
            $teamInfo = M('team')->where(array('id'=>array('in',$teamIdArr)))->getField('id,name,icon');
            $MatchResult = C('MatchResult');
            foreach($list as $v){
                $match = $v;
                if(isset($v['rate'])){
                    $match['rate'] = json_decode($v['rate'],true);
                }
                if(isset($v['result'])) {
                    $match['result'] = $MatchResult[$v['result']];
                }
                $match['host_name'] = $teamInfo[$v['host_id']]['name'];
                $match['host_icon'] = __ROOT__.'/upload/'.$teamInfo[$v['host_id']]['icon'];
                $match['guess_name'] = $teamInfo[$v['guess_id']]['name'];
                $match['guess_icon'] = __ROOT__.'/upload/'.$teamInfo[$v['guess_id']]['icon'];
                $data[] = $match;
            }
        }
        return $data;
    }

    /**
     *下注球赛
     * 1.判断球赛状态
     * 2.判断金豆是否够
     * 开启事务
     * 扣金币、添加记录、修改总参与人次
     * 返回结果
     */
    public function buyBall()
    {
        $res['status'] = 'error';
        $matchId = I('post.matchid',0,'number_int');
        $cost = I('post.cost');
        $option = I('post.option');
        $RecordOptionArr = C('MatchRecordOption');
        if(isset($RecordOptionArr[$option])){      //判断下注项是否合法
            //判断金豆是否够
            $userInfo = M('user')->field('coin')->find($this->uid);
            if($userInfo['coin']<$cost){
                $res['msg'] = '金豆不足';
            }else{
                $M = M('match');
                $matchInfo = $M->field('result,times,rate,is_show')->find($matchId);
                if( $matchInfo && $matchInfo['is_show']==1 && $matchInfo['result']==1 ){
                    $rate = json_decode($matchInfo['rate'],true);
                    $currentRate = $rate[$option];
                    if($currentRate>0){
                        $M->startTrans();
                        $r1 = M('user')->where(array('id'=>$this->uid))->setDec('coin',$cost);

                        $da2['mid'] = $matchId;
                        $da2['uid'] = $this->uid;
                        $da2['option'] = $RecordOptionArr[$option];
                        $da2['cost'] = $cost;
                        $da2['reward'] = intval($cost*$currentRate);
                        $da2['status'] = 1;
                        $r2 = M('match_record')->add($da2);

                        $r3 = $M->where(array('id'=>$matchId))->setInc('times',1);
                        if($r1 && $r2 && $r3){
                            $M->commit();
                            $res['status'] = 'success';
                            $res['coin'] = $userInfo['coin']-$cost;
                            session('coin',$res['coin']);
                            $res['msg'] = '下注成功';
                        }else{
                            $M->rollback();
                            $res['msg'] = '下注失败';
                        }
                    }else{
                        $res['msg'] = '下注错误';
                    }
                }else{
                    $res['msg'] = '比赛当前不能下注';
                }
            }
        }else {
            $res['msg'] = '下注项错误';
        }
        $this->ajaxReturn($res);
    }

}