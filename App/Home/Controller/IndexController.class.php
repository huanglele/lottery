<?php
namespace Home\Controller;

class IndexController extends CommonController
{

    public function index()
    {
        var_dump(session('loginJumpUrl'));
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