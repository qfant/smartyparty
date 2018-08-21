<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台门店管理控制器
 */
class HouseController extends AdminBaseController{
	public function ajaxHouseList(){
		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$offset = ($page-1)*$rows;
		$result["total"]=D('House')->count();

		$data=D('House')->order('createtime desc')->limit($offset.','.$rows)->select();
		$result["rows"] = $data;
		$this->ajaxReturn($result,'JSON');
	}
	/**
	 * 添加
	 */
	public function add(){
		if(IS_POST){
			$data['title']=I('post.title');
			$data['phone']=I('post.phone');
			$data['pic']=I('post.pic');
			$data['address']=I('post.address');
			$data['content']=I('post.content','',false);
			$data['isshow']=I('post.isshow');
			unset($data['id']);
			$data['createtime']=date("Y-m-d H:i:s" ,time());
			$result=D('House')->addData($data);
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
	public function delete(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
		);
		$result=D('House')->deleteData($map);
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
	public function edit(){
		if(IS_POST){
			$data['id']=I('post.id');
			$data['title']=I('post.title');
			$data['phone']=I('post.phone');
			$data['pic']=I('post.pic');
			$data['address']=I('post.address');
			$data['content']=I('post.content','',false);
			$data['isshow']=I('post.isshow');
			$where['id']=$data['id'];
			$result=D('House')->editData($where,$data);
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
}
