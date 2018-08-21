<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 商城首页Controller
 */
class IndexController extends HomeBaseController{
    /**
     * 首页
     */
    public function index(){
        $this->display();
    }

    /**
     * 发送邮件
     */
    public function send_email(){
        $email=I('post.email');
        $result=send_email($email,'邮件标题','邮件内容');
        if ($result['error']==1) {
            p($result);die;
        }
        $this->success('发送完成',U('Home/Index/index'));
    }

    /**
     * 生成二维码
     */
    public function qrcode(){
        $url=I('post.url');
        qrcode($url);
    }

    /**
     * 生成pdf
     */
    public function pdf(){
        $content=$_POST['content'];
        pdf($content);
    }

    /**
     * 支付宝
     */
    public function alipay(){
        $price=I('post.price');
        $data=array(
            'out_trade_no'=>time(),
            'price'=>$price,
            'subject'=>'测试'
            );
        alipay($data);
    }

    /**
     * 微信 公众号jssdk支付
     */
    public function weixinpay_js(){
        // 此处根据实际业务情况生成订单 然后拿着订单去支付

        // 用时间戳虚拟一个订单号  （请根据实际业务更改）
        $out_trade_no=time();
        // 组合url
        $url=U('Api/Weixinpay/pay',array('out_trade_no'=>$out_trade_no));
        // 前往支付
        redirect($url);
    }

    /**
     * 微信 扫描支付
     */
    public function weixinpay_qrcode(){
        // 此处根据实际业务情况生成订单 然后拿着订单去支付

        // 虚拟的订单 请根据实际业务更改
        $time=time();
        $order=array(
            'body'=>'test',
            'total_fee'=>1,
            'out_trade_no'=>strval($time),
            'product_id'=>1
            );
        weixinpay($order);
    }

    /**
     * 融云用户1
     */
    public function user1(){
        // 模拟id为89的用户的登陆过程
        $user_data=M('Users')->field('id,username,avatar')->find(88);
        $_SESSION['user']=array(
            'id'=>$user_data['id'],
            'username'=>$user_data['username'],
            'avatar'=>$user_data['avatar']
            );
        // 获取融云key
        $rong_key_secret=get_rong_key_secret();
        $assign=array(
            'uid'=>$user_data['id'], // 用户id
            'avatar'=>$user_data['avatar'],// 头像
            'username'=>$user_data['username'],// 用户名
            'rong_key'=>$rong_key_secret['key'],// 融云key
            'rong_token'=>get_rongcloud_token($user_data['id'])//获取融云token
            );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 融云用户2
     */
    public function user2(){
        // 模拟id为89的用户的登陆过程
        $user_data=M('Users')->field('id,username,avatar')->find(89);
        $_SESSION['user']=array(
            'id'=>$user_data['id'],
            'username'=>$user_data['username'],
            'avatar'=>$user_data['avatar']
            );
        // 获取融云key
        $rong_key_secret=get_rong_key_secret();
        $assign=array(
            'uid'=>$user_data['id'], // 用户id
            'avatar'=>$user_data['avatar'],// 头像
            'username'=>$user_data['username'],// 用户名
            'rong_key'=>$rong_key_secret['key'],// 融云key
            'rong_token'=>get_rongcloud_token($user_data['id'])//获取融云token
            );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 生成xls格式的表格
     */
    public function xls(){
        $data=I('post.data');
        create_xls($data);
    }

    /**
     * 生成csv格式的表格
     */
    public function csv(){
        $data=I('post.data');
        array_walk($data, function(&$v){
            $v=implode(',', $v);
        });
        create_csv($data);
    }

    /**
     * 导入xls格式的数据 
     * 也可以用来导入csv格式的数据
     * 但是csv建议使用 下面的import_csv 效率更高
     */
    public function import_xls(){
        $data=import_excel('./Upload/excel/simple.xls');
        p($data);
    }

    /**
     * 导入csv格式的数据
     */
    public function import_csv(){
        $data=file_get_contents('./Upload/excel/simple.csv');
        $data=explode("\r\n", $data);
        p($data);
    }

    /**
     * webuploader 上传文件
     */
    public function ajax_upload(){
        // 根据自己的业务调整上传路径、允许的格式、文件大小
        ajax_upload('/Upload/image/');
    }

    /**
     * webuploader 上传demo
     */
    public function webuploader(){
        // 如果是post提交则显示上传的文件 否则显示上传页面
        if(IS_POST){
            p($_POST);die;
        }else{
            $this->display();
        }
    }

    /**
     * 用来做测试用
     */
    public function test(){
        
    }


}

