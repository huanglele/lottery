<?php
/**
 * Author: huanglele
 * Date: 2016/7/21
 * Time: ���� 10:43
 * Description:
 */

namespace Home\Controller;

use Think\Controller;

class CommonController extends Controller
{
    private $uid = null;

    //�жϵ�¼
    public function _initialize()
    {
        $this->uid =  session('uid');
        if (!$this->uid){    //�ж�û�е�¼
           //�ж��Ƿ��ǵ�¼����
            $action = strtolower(ACTION_NAME);
            if ($action == 'login'){    //��¼����

            } else {
                //��¼url
                session('loginJumpUrl',$_SERVER['REQUEST_URI']);
            }
        }
    }

    //΢�ŵ�¼
    public function login()
    {
        $tools = new \Org\Wxpay\UserApi();
        $openId = $tools->GetOpenid();
        $wxInfo = $tools->getInfo();
        if(!$wxInfo || isset($wxInfo['errcode'])){
            $this->error('΢����Ȩ����',U('index/index'));
        }
        $info = getWxUserInfo($openId);
        if(!$info || isset($info['errcode'])){
            var_dump($info);die;
            $this->error('��¼���˵�״��',U('index/index'));
        }

        //�ж�֮ǰ�Ƿ�洢���û�����
        $M = M('user');
        $data = array_merge($info,$wxInfo);

        session('openid',$openId);

        if(isset($data['headimgurl'])){
            $data['headimgurl'] = trim($data['headimgurl'],'0').'64';
        }
        $uInfo = $M->where(array('openid'=>$openId))->field('uid')->find();
        $uid = $uInfo['uid'];
        $data['last_time'] = time();    //д������¼ʱ��
        $jump = session('loginJumpUrl');
        if(!$jump){
            $jump = U('index/index');
        }
        session('loginJumpUrl',null);
        if($uid){
            $data['uid'] = $uid;
            M('user')->save($data);
            session('uid',$uid);
            header("Location:$jump");
        }else{
            //��һ�ε�¼ ��ӵ��û�������
            $data['coin'] =  0;
            $r = $M->add($data);
            if($r){
                session('uid',$r);
                session('agent',0);
                header("Location:$jump");
            }
        }
    }

    public function _empty(){
        $this->index();
    }

}