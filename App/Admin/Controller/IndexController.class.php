<?php
namespace Admin\Controller;

class IndexController extends CommonController {

    public function index(){
        $this->display();
    }

    /**
     * 修改自己的密码
     */
    public function pwd(){
        if(isset($_POST['submit'])){
            $pwd = I('post.pwd');
            $newpwd = I('post.newpwd');
            $repwd = I('post.repwd');
            if((!$pwd || !$newpwd || !$repwd)) $this->error('请把表单填写完整');
            if($pwd == $newpwd) $this->error('新旧密码不能相同');
            if($newpwd != $repwd)   $this->error('两次新密码不同');
            $M = M('Admin');
            $map['aid'] = $this->aid;
            $map['password'] = md5($pwd);
            $id = $M->where($map)->getField('aid');
            if(!$id) $this->error('原密码错误');
            $data['aid'] = $this->aid;
            $data['password'] = md5($newpwd);
            if($M->save($data)){
                $this->success('修改成功',U('index'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $this->display('pwd');
        }
    }

    //显示微信信息
    public function wechat(){
        $info = C('Wx');
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'/index.php/Notify/wechatNotify';
        $this->assign('url',$url);
        $this->assign('info',$info);
        $this->display('wechat');
    }

    //管理员列表
    public function listAdmin(){
        $this->getList('admin',array(),'aid desc');
        $this->display('listAdmin');
    }

    //添加管理员
    public function addAdmin(){
        if(isset($_POST['submit'])){
            $name = I('post.name');
            $pwd = I('post.pwd');
            if((!$name || !$pwd)) $this->error('请把表单填写完整');
            $M = M('Admin');
            if($M->where(array('name'=>$name))->find()) $this->error('用户名已存在');
            $data['name'] = $name;
            $data['password'] = md5($pwd);
            if($M->add($data)){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->display('addAdmin');
        }
    }

    /**
     * 删除一个用户，不能删除自己
     */
    public function delAdmin(){
        $id = I('get.id');
        if($id == $this->aid) $this->error('不能删除自己');
        $M = M('Admin');
        if($M->delete($id)){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

}