<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台门店管理控制器
 */
class AppWeixinController extends AdminBaseController{
	public function ajaxAppList(){
		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$offset = ($page-1)*$rows;
		$result["total"]=D('AppWeixin')->count();
		$data=D('AppWeixin')->limit($offset.','.$rows)->select();
		$result["rows"] = $data;
		$this->ajaxReturn($result,'JSON');
	}
	public function ajaxTownAll(){
		$data=D('AppWeixin')->select();
		$this->ajaxReturn($data,'JSON');
	}
	/**
	 * 添加
	 */
	public function addAppNav(){
		if(IS_POST){
			$data['appid']=I('post.appid');
			$data['appsecert']=I('post.appsecert');
			unset($data['id']);
			$result=D('AppWeixin')->addData($data);
			if($result){
				$message['status']=1;
				$message['message']='保存成功';
			}else {
				$message['status']=0;
				$message['message']='保存失败';
			}
		}else {
			$message['status']=0;
			$message['message']='保存失败';
		}
		$this->ajaxReturn($message,'JSON');
	}
	/**
	 * 删除
	 */
	public function deleteAppNav(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
		);
		$result=D('AppWeixin')->deleteData($map);
		if($result){
			$message['status']=1;
			$message['message']='删除成功';
		}else {
			$message['status']=0;
			$message['message']='删除失败';
		}
		$this->ajaxReturn($message,'JSON');
	}

	/**
	 * 添加
	 */
	public function editAppNav(){
		if(IS_POST){
			$data['id']=I('post.id');
			$data['appid']=I('post.appid');
			$data['appsecert']=I('post.appsecert');
			$where['id']=$data['id'];
			$result=D('AppWeixin')->editData($where,$data);
			if($result){
				$message['status']=1;
				$message['message']='保存成功';
			}else {
				$message['status']=0;
				$message['message']='保存失败';
			}
		}else {
			$message['status']=0;
			$message['message']='保存失败';
		}
		$this->ajaxReturn($message,'JSON');
	}

	/**
	 * get_access_token
	 * 通过APPID 、appsecret 获取accessToken
	 */
	function get_access_token(){
		$appid = "";
		$appsecret = "";
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$jsoninfo = json_decode($output, true);
		$access_token = $jsoninfo["access_token"];
		return  $access_token;
	}
}
