<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台菜单管理
 */
class NavController extends AdminBaseController{
	/**
	 * 菜单列表
	 */
	public function index(){
		$data=D('AdminNav')->getTreeData('tree','order_number,id');
		$assign=array(
			'data'=>$data
			);
		$this->assign($assign);
		$this->display();
	}
	public function Menus(){
		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$offset = ($page-1)*$rows;
		$result["total"]=D('AdminNav')->where(array('pid'=>0))->count();

		$data=D('AdminNav')->where(array('pid'=>0))->limit($offset.','.$rows)->select();
		foreach ($data as $key => $value){
			$children=D('AdminNav')->where(array('pid'=>$value['id']))->select();
			$data[$key]['children']=$children;
		}
		$result["rows"] = $data;
		$this->ajaxReturn($result,'JSON');
	}
	public function menuLevel(){
		$pid=I('get.pid');
		$data=D('AdminNav')->field('id,name')->where(array('pid'=>$pid))->select();
		$this->ajaxReturn($data,'JSON');
	}
	/**
	 * 添加菜单
	 */
	public function add(){
		$data=I('post.');
		unset($data['id']);
		$result=D('AdminNav')->addData($data);
		if($result){
			$message['status']=1;
			$message['message']='添加菜单成功';
		}else {
			$message['status']=0;
			$message['message']='添加菜单失败';
		}
		$this->ajaxReturn($message,'JSON');
	}

	/**
	 * 修改菜单
	 */
	public function edit(){
		$data=I('post.');
		$map=array(
			'id'=>$data['id']
			);
		$result=D('AdminNav')->editData($map,$data);
		if($result){
			$message['status']=1;
			$message['message']='修改成功';
		}else {
			$message['status']=0;
			$message['message']='修改失败';
		}
		$this->ajaxReturn($message,'JSON');
	}

	/**
	 * 删除菜单
	 */
	public function delete(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
			);
		$result=D('AdminNav')->deleteData($map);
		if($result){
			$message['status']=1;
			$message['message']='删除菜单成功';
		}else {
			$message['status']=0;
			$message['message']='删除菜单失败';
		}
		$this->ajaxReturn($message,'JSON');
	}

	/**
	 * 菜单排序
	 */
	public function order(){
		$data=I('post.');
		D('AdminNav')->orderData($data);
		$this->success('排序成功',U('Admin/Nav/index'));
	}


}
