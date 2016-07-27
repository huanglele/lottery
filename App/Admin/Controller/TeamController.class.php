<?php
/**
 * Author: huanglele
 * Date: 2016/7/17
 * Time: 下午 06:07
 * Description:
 */

namespace Admin\Controller;

class TeamController extends CommonController
{

    public function _initialize(){
        parent::_initialize();
        $this->assign('TeamType',C('TeamType'));
    }

    //列举球队
    public function index(){
        $type = I('get.type',0,'number_int');
        if($type) {
            $map['type'] = $type;
        }
        $this->assign('type',$type);
        $Tool = new CommonController();
        $Tool->getList('team',$map,'id desc','id,area,name,icon,type,coach');
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
            //判断是否有文件上传
            if($_FILES['img']['error']==0){
                //处理图片
                $upload = new \Think\Upload(C('UploadConfig'));
                $info   =   $upload->upload();
                if($info) {
                    $data['icon'] = $info['img']['savepath'].$info['img']['savename'];
                    //对图片进行裁剪
                    $image = new \Think\Image();
                    $image->open('./upload/'.$data['icon']);
                    $image->thumb(60,60,6)->save('./upload/'.$data['icon']);
                }else{
                    $this->error($upload->getError());
                }
            }
            $M = M('team');
            if($ac == 'add'){
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

    //获取单只球队的信息
    public function getOneTeamInfo(){
        $id = I('get.id');
        $info = M('team')->find($id);
        if($info){
            $info['status'] = true;
            $ret['name'] = $info['name'];
        }else{
            $info['status'] = false;
        }
        $this->ajaxReturn($info);
    }

    //获取比赛名称
    public function ajax_get_team(){
        $type = I('get.type');
        $name = I('get.name');
        $map = array();
        if($type){
            $map['type'] = $type;
        }
        if($name){
            $map['name'] = array('like','%'.$name.'%');
        }
        $list = M('team')->where($map)->field('id,name,area,type')->select();
    }

}