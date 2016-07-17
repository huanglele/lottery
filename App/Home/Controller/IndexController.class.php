<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $county = C('Country');
        foreach($county as $k=>$v){
            echo '\''.($k+1).'\' => \''.$v.'\',<br/>';
        }
    }
}