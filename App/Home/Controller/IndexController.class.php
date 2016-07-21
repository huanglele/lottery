<?php
namespace Home\Controller;

class IndexController extends CommonController
{

    public function index()
    {
        echo $this->uid;
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