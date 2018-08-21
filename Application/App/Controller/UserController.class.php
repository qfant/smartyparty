<?php
namespace App\Controller;
use Common\Controller\AppBaseController;
/**
 * 认证控制器
 */
class UserController extends AppBaseController{
    private $caesar;
    public function _initialize(){
        parent::_initialize();
    }
   
    public function getSmscode(){
        $phone=I("get.phone",'');
        if($phone){
            $code='123456';
            $createtime=time();
            $result = D("Smscode")->add(array('phone'=>$phone,'code'=>$code,'createtime'=>$createtime));
            $data['status']=1;
            $data['message']='发送成功';
        }else {
            $data['status']=0;
            $data['message']='发送失败';
        }
        $this->ajaxReturn($data,'JSON');
    }

}
