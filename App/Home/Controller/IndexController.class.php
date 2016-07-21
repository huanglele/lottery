<?php
namespace Home\Controller;

class IndexController extends CommonController
{
    //显示首页
    public function index()
    {

    }


    public function test()
    {
        $jump = session('loginJumpUrl');
        if($jump){
            header("Location: $jump");
        }else{
            echo '没有跳转';
        }
    }


}