<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 第三方用户
 */
class LoginLoggerModel extends BaseModel{

    protected $_auto=array(
        array('createtime','time',1,'function'), // 对date字段在新增的时候写入当前时间戳
    );


}