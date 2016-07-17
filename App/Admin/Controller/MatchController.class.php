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
        $name = I('get.name');
        if($name){
            $map['name'] = array('like','%'.$name.'%');
        }
        $this->assign('name',$name);
        $Tool = new CommonController();
        $list = $Tool->getList('match',$map,'id desc','id,name,host_id,guess_id,type,b_time,result,times');
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
        $info = M('team')->find($id);
        if($info){
            $this->assign('info',$info);
            $this->display('edit');
        }else{
            $this->error('球队不存在',U('index'));
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



}