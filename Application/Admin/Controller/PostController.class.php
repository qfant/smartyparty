<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class PostController extends AdminBaseController{
    public function ajaxPostList(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $username=I("get.name");
        $offset = ($page-1)*$rows;
        $countsql="select count(m.id) as total from qfant_post m where 1=1 ";
        $sql="select m.*,t.name AS departmentname from qfant_post m  LEFT JOIN qfant_department t ON m.department_id=t.id where 1=1 ";
        $param=array();
        if(!empty($username)){
            array_push($param,'%'.$username.'%');
            $countsql.=" and m.name like '%s'";
            $sql.=" and m.name like '%s'";
        }
        $sql.=" order by m.id desc  limit %d,%d";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('Post')->query($countsql,$param);
        $result['records']=$data[0]['total'];

        if($result['records'] >0 ) {
            $total_pages = ceil( $result['records']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $result['page']=$page;
        $result['total']=$total_pages;
        $data=D('Post')->query($sql,$param);

        $result["rows"] = $data;
        $result["status"] = 1;
       $this->ajaxReturn($result,'JSON');

    }
    /**
     * 添加
     */
    public function add(){
        if(IS_POST){
            $data=I('post.');
            $org=I('post.org');
            $depData=D('Department')->where(array('name'=>$org))->find();
            $data['department_id']=$depData['id'];
            unset($data['id']);
            $result=D('Post')->addData($data);
            if($result){
                $this->success('保存成功',U('Admin/Post/index'));
            }else {
                $this->success('保存失败',U('Admin/Post/index'));
            }
        }else {
            $this->display();
        }

    }

    /**
     * 编辑
     */
    public function edit(){
        if(IS_POST){
            $id=I('post.id');
            $where['id']=$id;
            $data['name']=I('post.name');
            $org=I('post.org');
            $depData=D('Department')->where(array('name'=>$org))->find();
            $data['department_id']=$depData['id'];
            $result=D('Post')->editData($where,$data);
            if($result){
                $this->success('保存成功',U('Admin/Post/index'));
            }else {
                $this->success('保存失败',U('Admin/Post/index'));
            }
        }else {
            $post=D('Post')->where(array('id'=>I('get.id')))->find();
            $department=D('Department')->where(array('id'=>$post['department_id']))->find();
            $this->assign('post',$post);
            $this->assign('departmentname',$department['name']);
            $this->display();
        }
    }


    /**
     * 删除
     */
    public function delete(){
        $id=I('get.id');
        $map=array(
            'id'=>$id
        );
        $result=D('Post')->deleteData($map);
        $this->success('删除成功',U('Admin/Post/index'));
    }
    public function ajaxPostAll(){
        $departmentId=I("get.departmentId");
        $branchs=D('Post')->where(array('department_id'=>$departmentId))->field("id,name")->select();
        $result["result"] = $branchs;
        $result["status"] = 1;
        $this->ajaxReturn($result,'JSON');
    }
    public function searchPost(){
        $name=I("post.name");
        $cond['name']=array('like','%'.$name.'%');
        $branchs=D('Post')->where($cond)->field("id,name")->select();
        $result["result"] = $branchs;
        $result["status"] = 1;
        $this->ajaxReturn($result,'JSON');
    }

}
