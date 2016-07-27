<?php
/**
 * Author: huanglele
 * Date: 2016/7/21
 * Time: 下午 03:09
 * Description:
 */

namespace Home\Controller;


class FootballController extends BallBaseController
{

    public function index()
    {
        $this->assign('active','all');
        $map['is_show'] = 1;
        $map['type'] = 2;   //拉取篮球
        $map['result'] = array('in',array(1,2));
        $list = M('match')->order('b_time desc')->where($map)->limit(10)->select();
        $data = $this->mergeMatchInfo($list);
        $this->assign('data',$data);
        $this->display('index');
    }

    /**
     * 热门赛事
     * 按照 下注人次拉取前5条 可以下注的赛事
     */
    public function hot()
    {
        $this->assign('active','hot');
        //获取热门比赛
        $map['is_show'] = 1;
        $map['type'] = 2;   //拉取篮球
        $map['result'] = 1; //可以下注的赛事
        $list = M('match')->order('times desc')->where($map)->limit(5)->select();
        $data = $this->mergeMatchInfo($list);

        $this->assign('data',$data);
        $this->display('hot');
    }

    //拉取已经结束的比赛
    public function finish()
    {
        $this->assign('active','finish');
        $map['is_show'] = 1;
        $map['type'] = 2;   //拉取篮球
//        $map['result'] = array('gt',2);
        $list = M('match')->order('b_time desc')->where($map)->limit(10)->select();
        $data = $this->mergeMatchInfo($list);
        $this->assign('data',$data);
        $this->display('finish');
    }


    //一个比赛详情
    public function match()
    {
        $id = I('get.id');
        $info = M('match')->find($id);
        if($info){
            $teamInfo = M('team')->where(array('id'=>array('in',array($info['host_id'],$info['guess_id']))))->getField('id,name,icon,coach,area');
            $Country = C('Country');
            $teamInfo[$info['host_id']]['area'] = $Country[$teamInfo[$info['host_id']]['area']];
            $teamInfo[$info['guess_id']]['area'] = $Country[$teamInfo[$info['guess_id']]['area']];
            unset($Country);
            $info['host_info'] = $teamInfo[$info['host_id']];
            $info['guess_info'] = $teamInfo[$info['guess_id']];
            unset($teamInfo);
//            var_dump($info);
            $info['rate'] = json_decode($info['rate'],true);
            $this->assign('info',$info);
            $this->assign('MatchResult',C('MatchResult'));

            //查询投注统计
            $recordInfo = M('match_record')->where(array('mid'=>$id))->order('`option` asc')->group('`option`')->getField('option,count(`id`) as num');
            $recordInfo['3'] || $recordInfo['3'] = 0;
            $recordInfo['4'] || $recordInfo['4'] = 0;
            $recordInfo['5'] || $recordInfo['5'] = 0;
            $all = $recordInfo['3'] + $recordInfo['4'] + $recordInfo['5'];
            if($all!=$info['times']){
                $info['times'] = $all;
                M('match')->where('id='.$id)->setField('times',$all);
            }

            $record['win'] = $recordInfo['3']?round($recordInfo['3']*100/$all):0;
            $record['draw'] = $recordInfo['4']?round($recordInfo['4']*100/$all):0;
            $record['lose'] = $recordInfo['5']?round($recordInfo['5']*100/$all):0;

            $this->assign('record',$record);

            if($info['result']==1){
                $this->display('match_on');
            }else{
                $this->display('match_finish');
            }
        }else{
            $this->error('比赛不存在',U('index'));
        }
    }


    public function test(){
        $id = I('get.id');
        $info = M('match_record')->where(array('mid'=>$id))->group('`option`')->order('`option` asc')->getField('option,count(`id`) as num');
        echo M('match_record')->getLastSql();
        var_dump($info);
    }

}