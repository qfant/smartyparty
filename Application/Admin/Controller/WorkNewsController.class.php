<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class WorkNewsController extends AdminBaseController{
    public function ajaxNewsList(){
        $childs=D('Department')->getDepartmentList(session('user.data')['department_id']);
        $childs = substr($childs,0,strlen($childs)-1);
        //print_r($childs);die;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $title=I("get.title");
        $offset = ($page-1)*$rows;
        $countsql="select count(n.id) AS total from  qfant_Worknews n where 1=1 AND n.department_id in (".$childs.")";
        $sql="select n.*,t.name AS departmentName from qfant_Worknews n LEFT JOIN qfant_department t ON n.department_id=t.id where 1=1 AND n.department_id in (".$childs.")";
        $param=array();
        if(!empty($title)){
            array_push($param,'%'.$title.'%');
            $countsql.=" and n.title like '%s' order by n.createtime desc";
            $sql.=" and n.title like '%s'";
        }
        $sql.=" order by id desc  limit %d,%d";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('Worknews')->query($countsql,$param);
        $result['records']=$data[0]['total'];

        if($result['records'] >0 ) {
            $total_pages = ceil( $result['records']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $result['page']=$page;
        $result['total']=$total_pages;
        $data=D('Worknews')->query($sql,$param);
        foreach ($data as $key=>$basevalue) {
            $data[$key]['intro'] = htmlspecialchars_decode($basevalue['intro']);
        }
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
            $time=date("Y-m-d H:i:s");
            $data['createtime']=strtotime($time);
            unset($data['id']);
            $result=D('Worknews')->addData($data);
            if($result){
                $this->success('保存成功',U('Admin/WorkNews/index'));
            }else {
                $this->success('保存失败',U('Admin/WorkNews/index'));
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
            $data['title']=I('post.title');
            $data['type']=I('post.type');
            $data['intro']=I('post.intro');
            $result=D('Worknews')->editData($where,$data);
            if($result){
                $this->success('保存成功',U('Admin/WorkNews/index'));
            }else {
                $this->success('保存失败',U('Admin/WorkNews/index'));
            }
        }else {
            $news=D('Worknews')->where(array('id'=>I('get.id')))->find();
            $this->assign('news',$news);
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
        $result=D('Worknews')->deleteData($map);
        $this->success('删除成功',U('Admin/WorkNews/index'));
    }


}
