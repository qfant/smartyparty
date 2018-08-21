<?php
namespace Common\Controller;
use Common\Controller\BaseController;
/**
 * Home基类控制器
 */
class AppBaseController extends BaseController{
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		parent::_initialize();

	}

	public  function checkIsLonginUser($userId,$token){
		$loginExpireTime = C('APP_CONFIG.LOGIN_EXPIRE_TIME');
		$sql ="select log.id from  qfant_login_logger log where log.type=1 AND log.customer_Id='$userId' and log.loginIden='$token' AND TO_DAYS(NOW())-TO_DAYS(log.loginTime)<= '$loginExpireTime'";
		$data=D('LoginLogger')->query($sql,"");
		if($data){
		    return $data[0];
        }
	}



}

