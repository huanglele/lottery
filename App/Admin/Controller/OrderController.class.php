<?php
/**
 * Author: huanglele
 * Date: 2016/7/28
 * Time: 下午 02:33
 * Description:
 */

namespace Admin\Controller;


class OrderController extends CommonController
{

    public function index()
    {
        $map = array();
        $uid = I('get.uid',0,'number_int');
        if($uid){
            $map['uid'] = $uid;
        }
        $this->assign('uid',$uid);
        $gid = I('get.gid',0,'number_int');
        if($gid){
            $map['gid'] = $gid;
        }
        $this->assign('gid',$gid);
        $status = I('get.status',0,'number_int');
        if($status){
            $map['status'] = $status;
        }
        $this->assign('status',$status);
        $this->getList('orders',$map,'id desc','id,gid,uid,status,create_time');
        $this->assign('OrderStatus',C('OrderStatus'));
        $this->display('index');
    }

    public function view(){
        $id = I('get.id',0,'number_int');
        $info = M('orders')->find($id);
        $this->assign('OrderStatus',C('OrderStatus'));
        $this->assign('info',$info);
        $this->display('view');
    }

    public function update(){
        $id = I('post.id');
        $status = I('post.status');
        $data['id'] = $id;
        $data['status'] = $status;
        if(M('orders')->save($data)){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

}