<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class RouteController extends AdminBaseController{
    public function ajaxRouteList(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $result["total"]=D('Route')->count();
        $data=D('Route')->limit($offset.','.$rows)->select();
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');
    }
    /**
     * 添加
     */
    public function add(){
        if(IS_POST){
            $data['name']=I('post.name');
            $data['status']=I('post.status');
            unset($data['id']);
            $result=D('Route')->addData($data);
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
            $data['status']=I('post.status');
            $where['id']=$data['id'];
            $result=D('Route')->editData($where,$data);
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
        $result=D('Route')->deleteData($map);
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
     * 启用
     */
    public function usingRoute(){
        $data['id']=I('get.id');
        $data['status']="1";
        $where['id']=$data['id'];
        $result=D('Route')->editData($where,$data);
        if($result){
            $message['status']=1;
            $message['message']='启用成功';
        }else {
            $message['status']=0;
            $message['message']='启用失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    /**
     * 禁用
     */
    public function forbiddenRoute(){
        $data['id']=I('get.id');
        $data['status']="0";
        $where['id']=$data['id'];
        $result=D('Route')->editData($where,$data);
        if($result){
            $message['status']=1;
            $message['message']='禁用成功';
        }else {
            $message['status']=0;
            $message['message']='禁用失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    public function ajaxRoute(){
        $data=D('Route')->where(array('status'=>1))->select();
        $this->ajaxReturn($data,'JSON');
    }

}
