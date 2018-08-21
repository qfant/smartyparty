<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 第三方用户
 */
class CustomerModel extends BaseModel{

    // 自动验证
    protected $_validate=array(
        array('username','require','登录名必填'),
        array('password','require','密码必填'),
        array('phone','require','手机号必填'),
        array('access_token','require','access_token必填'),
        );

    // 自动完成
    protected $_auto=array(
        array('password','md5',1,'function') , // 对password字段在新增的时候使md5函数处理
        array('register_time','time',1,'function'),
        array('last_login_time','time',3,'function'),
        );

    // 添加数据
    public function addData($add_data){
        if($data=$this->create($add_data)){
            $id=$this->add($data);
            return $id;
        }else{
            return false;
        }
    }


}