<?php
namespace Home\Controller;

class IndexController extends CommonController
{
    //显示首页
    public function index()
    {
        $this->display();
    }

    public function upload(){

    }

    //礼物兑换
    public function gift()
    {
        $map['status'] = 1;
        $this->getData('gift',$map,'id desc');
        $this->display('gift');
    }

    //申请兑换礼物
    public function exchangeGift(){
        $gid = I('post.gid');
        $uid = session('uid');
        $name = I('post.name');
        $phone = I('post.phone');
        $address = I('post.address');
        if($name=='' || $phone =='' || $address==''){
            $this->error('请填写完整的表单');die;
        }
        $M = M('gift');
        $gInfo = $M->find($gid);
        if($gInfo['left_num']<1){
            $this->error('改商品已兑换完');die;
        }
        $leftCoin = session('coin');
        //查看是否有足够的金币
        if($leftCoin<$gInfo['price']){
            $this->error('金币不足');die;
        }

        //开启事务，gift表 orders，user ，coin_record
        $M->startTrans();
        $da1['left_num'] = $gInfo['left_num']-1;
        $da1['sold_num'] = $gInfo['sold_num']+1;
        $da1['id'] = $gid;
        $r1 = $M->save($da1);

        $data['gid'] = $gid;
        $data['user_name'] = $name;
        $data['user_phone'] = $phone;
        $data['user_address'] = $address;
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['status'] = 1;
        $data['uid'] = $uid;
        $r2 = M('orders')->add($data);

        $r3 = M('user')->where(array('id'=>$uid))->setDec('coin',$gInfo['price']);

        $da4['uid'] = $uid;
        $da4['amount'] = $gInfo['price'];
        $da4['type'] = -2;
        $da4['time'] = date('Y-m-d H:i:s');
        $da4['note'] = '兑换'.$gInfo['name'];
        $r4 = M('coin')->add($da4);
        if($r1 && $r2 && $r3 && $r4){
            $M->commit();
            session('coin',$leftCoin-$gInfo['price']);
            $this->success('兑换成功');
        }else{
            $this->error('兑换失败');
        }
    }

}