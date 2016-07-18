<?php
/**
 * Author: huanglele
 * Date: 2016/7/17
 * Time: 下午 10:27
 * Description:
 */

namespace Admin\Controller;


class MatchController extends CommonController
{
    public function _initialize(){
        parent::_initialize();
        $this->assign('MatchType',C('MatchType'));
        $this->assign('MatchResult',C('MatchResult'));
    }

    //所有比赛
    public function index(){
        $type = I('get.type',0,'number_int');
        $map = array();
        if($type) {
            $map['type'] = $type;
        }
        $this->assign('type',$type);

        $is_show = I('get.is_show',-1,'number_int');
        if($is_show>-1){
            $map['is_show'] = $is_show;
        }
        $this->assign('is_show',$is_show);

        $name = I('get.name');
        if($name){
            $map['name'] = array('like','%'.$name.'%');
        }
        $this->assign('name',$name);

        $Tool = new CommonController();
        $list = $Tool->getList('match',$map,'id desc','id,name,host_id,guess_id,type,b_time,result,is_show');
        $teamIdArr[] = 0;
        foreach($list as $v){
            if(!in_array($v['host_id'],$teamIdArr)){
                $teamIdArr[] = $v['host_id'];
            }
            if(!in_array($v['guess_id'],$teamIdArr)){
                $teamIdArr[] = $v['guess_id'];
            }
        }
        $teamMap['id'] = array('in',$teamIdArr);
        $Teams = M('team')->where($teamMap)->getField('id,name,icon');
        $this->assign('Teams',$Teams);
        $this->display('index');
    }

    //添加球队
    public function add(){
        $this->display('add');
    }

    //编辑球队
    public function edit(){
        $id = I('get.id');
        $info = M('match')->find($id);
        if($info){
            $info['rate'] = json_decode($info['rate'],true);
            $this->assign('info',$info);
            $this->display('edit');
        }else{
            $this->error('比赛不存在',U('index'));
        }
    }

    //处理添加|更新提交的数据
    public function update(){
        if(isset($_POST['submit'])){
            $ac = I('post.submit');
            $data = $_POST;
            $data['rate'] = json_encode(I('post.rate'));
            $is_show = I('post.is_show',0,'number_int');
            $data['is_show'] = $is_show;
            $M = M('match');
            if($ac == 'add'){
                $data['result'] = 1;
                if($M->add($data)){
                    $this->success('添加成功',U('index'));
                }else{
                    $this->error('添加失败请重试');
                }
            }elseif($ac == 'update'){
                $id = I('post.id');
                if(!$id)   $this->error('参数错误',U('index'));
                if($M->save($data)){
                    $this->success('更新成功',U('index'));
                }else{
                    $this->error('更新失败请重试');
                }
            }
        }else{
            $this->index();die;
        }
    }

    //查看一个比赛的情况
    public function view(){
        $id = I('get.id');
        $info = M('match')->find($id);
        if($info){
            $info['rate'] = json_decode($info['rate'],true);
            $this->assign('info',$info);
            $this->display('view');
        }else{
            $this->error('比赛不存在',U('index'));
        }
    }

    //查看竞猜的记录
    public function record(){
        $map = array();
        $mid = I('get.mid');
        if($mid){
            $map['mid'] = $mid;
        }
        $this->assign('mid',$mid);

        $uid = I('get.uid');
        if($uid){
            $map['uid'] = $uid;
        }
        $map['uid'] = $uid;

        $status = I('get.status');
        if($status){
            $map['status'] = $status;
        }
        $this->assign('status',$status);
        $this->getList('match_record',$map,'id desc');
        $this->assign('RecordStatus',C('RecordStatus'));
        $this->display('record');
    }

    //更新一个比赛的状态
    public function setResult(){
        $id = I('post.id');
        $s = I('post.result');
        $result = M('match')->where(array('id'=>$id))->getField('result');
        if($result){
            if($s==$result){$this->error('请修改为不同的状态');die;}
            if(in_array($result,array(1,2))){   //简单更新
                if(M('match')->where(array('id'=>$id))->setField('result',$s)){
                    $this->success('更新成功');
                }else{
                    $this->error('更新失败');
                }
            }else if(in_array($result,array(3,4,5))){   //开奖
                if(M('match')->where(array('id'=>$id))->setField('result',$s)){
                    ignore_user_abort(true);
                    $this->lottery($id);
                    $this->success('更新成功');
                }else{
                    $this->error('更新失败');
                }
            }
        }else{
            $this->error('当前结果不能修改');
        }
    }

    /**
     * @param $id match表id
     * @param bool|false $result 开奖结果 对应的是match表result字段的值
     */
    private function lottery($id,$result=false){
        if(!$result){
            $result = M('match')->where(array('id'=>$id))->getField('result');
        }

    }

}