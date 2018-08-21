<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台门店管理控制器
 */
class DepartmentController extends AdminBaseController{
    public function index(){
        $this->display();
    }
    public function ajaxDepartments(){
        $data=D('Department')->getTreeDatajax('tree','','text');
        $this->ajaxReturn($data,'JSON');
    } 
    /**
     * 添加
     */
    public function add(){
        if(IS_POST){
            $data['id']=I('post.id');
            $data['name']=I('post.name');
            $data['pid']=I('post.pid');

            $data['order_num']=I('post.order_num');
            unset($data['id']);
            $result=D('Department')->addData($data);

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
     * 编辑
     */
    public function edit(){
        if(IS_POST){
            $data['id']=I('post.id');
            $data['name']=I('post.name');
            $data['pid']=I('post.pid');
            $data['order_num']=I('post.order_num');
            //print_r($data);die;
            $where['id']=$data['id'];
            $result=D('Department')->editData($where,$data);
            if($result){
                $message['status']=1;
                $message['message']='保存成功';
            }else {
                $message['status']=0;
                $message['message']='保存失败';
            }
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
        $result=D('Department')->deleteData($map);
        if($result){
            $message['status']=1;
            $message['message']='删除成功';
        }else {
            $message['status']=0;
            $message['message']='删除失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    public function ajaxDepartmentAll(){
        $data=D('Department')->getTreeData('tree','','text');
        $result['result']=$data;
        $result['status']=1;
        $this->ajaxReturn($result,'JSON');
    }
    /**
     * 获取权限
     */
    public function getDepartment(){
        $id=I('get.id');
        $department=D('Department')->where(array('id'=>$id))->find();
        if($department['pid']){
            $pcat=D('Department')->where(array('id'=>$department['pid']))->find();
            $department['pname']=$pcat['name'];
        }
        $this->ajaxReturn($department,'JSON');
    }
}
