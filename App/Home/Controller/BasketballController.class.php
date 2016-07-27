<?php
/**
 * Author: huanglele
 * Date: 2016/7/27
 * Time: 下午 07:53
 * Description:
 */

namespace Home\Controller;


class BasketballController extends BallBaseController
{

    public function _initialize(){
        parent::_initialize();
        $this->type = 1;
    }

}