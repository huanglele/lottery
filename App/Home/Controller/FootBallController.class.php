<?php
/**
 * Author: huanglele
 * Date: 2016/7/21
 * Time: 下午 03:09
 * Description:
 */

namespace Home\Controller;


class FootballController extends BallBaseController
{

    public function _initialize(){
        parent::_initialize();
        $this->type = 2;
    }

}