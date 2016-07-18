<?php
/**
 * Author: huanglele
 * Date: 2016/7/18
 * Time: 下午 04:24
 * Description:
 */

namespace Admin\Controller;


class UserController extends CommonController
{

    public function _initialize(){
        parent::_initialize();
        $this->assign('UserSub',C('UserSub'));
        $this->assign('UserSex',C('UserSex'));
    }

    //显示用户列表
    public function index(){
        $map = array();
        $id = I('get.id');
        if($id){
            $map['id'] = $id;
        }
        $this->assign('id',$id);

        $name = I('get.name');
        if($name){
            $map['nickname'] = array('like','%'.$name.'%');
        }
        $this->assign('name',$name);
        $this->getList('user',$map,'id desc');
        $this->display('index');
    }




}