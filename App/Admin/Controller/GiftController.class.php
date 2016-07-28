<?php
/**
 * Author: huanglele
 * Date: 2016/7/27
 * Time: 下午 10:24
 * Description:
 */

namespace Admin\Controller;

class GiftController extends CommonController
{

    //礼物列表
    public function index()
    {
        $map = array();
        $name = I('get.name');
        if($name){
            $map['name'] = array('like','%'.$name.'%');
        }
        $this->assign('name',$name);
        $status = I('get.status');
        if($status){
            $map['status'] = $status;
        }
        $this->assign('status',$status);

        $this->getList('gift',$map,'id desc');
        $this->assign('GiftStatus',C('GiftStatus'));
        $this->display('index');
    }

    //添加礼物
    public function add()
    {
        $this->assign('GiftStatus',C('GiftStatus'));
        $this->display('add');
    }

    public function view()
    {
        $id = I('get.id');
        $info = M('gift')->find($id);
        if($info){
            $this->assign('info',$info);
            $this->assign('GiftStatus',C('GiftStatus'));
            $this->display('view');
        }else{
            $this->error('页面不存在');
        }
    }

    //处理添加礼物
    public function update()
    {
        $ac = I('post.submit');
        $id = I('post.id');
        $data = $_POST;
        //判断是否有文件上传
        if($_FILES['img']['error']==0){
            //处理图片
            $upload = new \Think\Upload(C('UploadConfig'));
            $info   =   $upload->upload();
            if($info) {
                $data['img'] = $info['img']['savepath'].$info['img']['savename'];
            }else{
                $this->error($upload->getError());
            }
        }
        $M = M('gift');
        if($id && $ac=='update'){
            if($M->save($data)){
                $this->success('更新成功',U('index'));
            }else{
                $this->error('更新失败请重试');
            }
        }else{
            $data['sold_num'] = 0;
            if($M->add($data)){
                $this->success('添加成功',U('index'));
            }else{
                $this->error('添加失败请重试');
            }
        }
    }

}