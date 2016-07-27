<?php
/**
 * Author: huanglele
 * Date: 2016/7/22
 * Time: 下午 09:53
 * Description:
 */

namespace Home\Controller;

//球赛基础类
use Think\Model;

class BallBaseController extends CommonController
{


    //前台ajax拉取比赛列表数据 默认从第二页开始拉取
    public function getMatchList()
    {
        $p = I('p',2,'number_int');
        $type = I('get.type');
        $map['is_show'] = 1;
        $map['type'] = $type;   //拉取篮球
        $ac = I('get.ac');
        if($ac=='finish'){  //已结束下注的
            $map['result'] = array('gt',2);
        }
        $list = $this->getData('match',$map,'b_time desc');
        $data = $this->mergeMatchInfo($list);
        $num = count($data);
        $ret['status'] = 'success';
        $ret['num'] = $num;
        $ret['list'] = $data;
        if($num==10)  $p++;
        $ret['page'] = $p;
        $this->ajaxReturn($ret);
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
                        //修改用户剩余金豆数量
                        $r1 = M('user')->where(array('id'=>$this->uid))->setDec('coin',$cost);

                        //添加竞猜记录
                        $da2['mid'] = $matchId;
                        $da2['uid'] = $this->uid;
                        $da2['option'] = $RecordOptionArr[$option];
                        $da2['cost'] = $cost;
                        $da2['reward'] = intval($cost*$currentRate);
                        $da2['status'] = 1;
                        $da2['time'] = date('Y-m-d H:i:s');
                        $r2 = M('match_record')->add($da2);

                        //修改竞猜次数
                        $r3 = $M->where(array('id'=>$matchId))->setInc('times',1);

                        //添加用户金豆消费记录
                        $da4['uid'] = $this->uid;
                        $da4['amount'] = $cost;
                        $da4['time'] = date('Y-m-d H:i:s');
                        $da4['type'] = '-1';
                        $da4['note'] = '竞猜记录ID'.$r2;
                        $r4 = M('coin')->add($da4);
                        if($r1 && $r2 && $r3 && $r4){
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

    //获取一场比赛竞猜记录
    public function getMatchRecordList(){
        $p = I('get.p');
        $mid = I('get.id');
        $map['mid'] = $mid;
        $list = $this->getData('match_record',$map,'id desc');
        $data = $this->mergeMatchRecordInfo($list);
        $num = count($data);
        $ret['status'] = 'success';
        $ret['num'] = $num;
        $ret['list'] = $data;
        if($num==10)  $p++;
        $ret['page'] = $p;
        $this->ajaxReturn($ret);
    }

    protected function mergeMatchRecordInfo($list){
        $data = array();
        if(count($list)){
            $uidArr = array();
            foreach($list as $v){
                $uidArr[] = $v['uid'];
            }
            $userInfo = M('user')->where(array('id'=>array('in',$uidArr)))->getField('id,nickname,headimgurl');
            $Result = C('MatchResult');
            foreach($list as $v){
                $i = $v;
                $i['nickname'] = $userInfo[$v['uid']]['nickname'];
                $i['headimgurl'] = $userInfo[$v['uid']]['headimgurl'];
                $i['option'] = $Result[$v['option']];
                $data[] = $i;
            }
        }
        return $data;
    }

    //我的竞猜记录列表
    public function myGuess(){
        if(IS_AJAX){
            $p = I('get.p');
            $sort = I('get.dataSort');
            $map['uid'] = $this->uid;
            if($sort=='win'){
                $map['status'] = 3;
            }elseif($sort == 'wait'){
                $map['status'] = 1;
            }

            $list = $this->getData('match_record',$map,'id desc','id,mid,option,cost,reward,time,status');
            $num = count($list);
            $data = array();
            if($num){
                //查询比赛信息
                $matchIdStr = '(';
                foreach($list as $v){
                    $matchIdStr .= $v['mid'].',';
                }
                $matchIdStr = rtrim($matchIdStr,',').')';
                $sql = 'select m.id,m.name as m_name,th.name as host_name,tg.name as guess_name FROM `match` m RIGHT JOIN `team` th ON th.id = m.host_id RIGHT JOIN `team` tg ON tg.id = m.guess_id WHERE m.id IN '.$matchIdStr.'ORDER BY `m`.`id` DESC';
                $Model = new Model();
                $matchInfoTemp = $Model->query($sql);
                $matchInfo = array();
                foreach($matchInfoTemp as $v){
                    $matchInfo[$v['id']] = $v;
                }
                $matchResult = C('MatchResult');
                $RecordStatus = C('RecordStatus');
                foreach($list as $v){
                    $i = $v;
                    $i['vs'] = $matchInfo[$v['mid']]['host_name'].' VS '.$v[$list['mid']]['guess_name'];
                    $i['name'] = $matchInfo[$v['mid']]['m_name'];
                    $i['option'] = $matchResult[$v['option']];
                    $i['status'] = $RecordStatus[$v['status']];
                    $data[] = $i;
                }
            }
            $ret['status'] = 'success';
            $ret['num'] = $num;
            $ret['list'] = $data;
            if($num==10)  $p++;
            $ret['page'] = $p;
            $this->ajaxReturn($ret);
        }else{
            $this->display('Ball/myGuess');
        }
    }

    //竞猜详情页面
    public function record()
    {
        $id = I('get.id');
        $info = M('user');
    }
}
