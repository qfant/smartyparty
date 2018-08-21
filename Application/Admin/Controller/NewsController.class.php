<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class NewsController extends AdminBaseController{
    public function ajaxNewsList(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $title=I("get.title");
        $offset = ($page-1)*$rows;
        $countsql="select count(n.id) AS total from  qfant_news n where 1=1 ";
        $sql="select n.* from qfant_news n  where 1=1";
        $param=array();
        if(!empty($title)){
            array_push($param,'%'.$title.'%');
            $countsql.=" and n.title like '%s' order by n.createtime desc";
            $sql.=" and n.title like '%s'";
        }
        $sql.=" order by id desc  limit %d,%d";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('News')->query($countsql,$param);
        $result['records']=$data[0]['total'];

        if($result['records'] >0 ) {
            $total_pages = ceil( $result['records']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $result['page']=$page;
        $result['total']=$total_pages;
        $data=D('News')->query($sql,$param);
        foreach ($data as $key=>$basevalue) {
            $data[$key]['intro'] = htmlspecialchars_decode($basevalue['intro']);
        }
        $result["rows"] = $data;
       $this->ajaxReturn($result,'JSON');


    }
    /**
     * 添加
     */
    public function add(){
        if(IS_POST){
            $data['headpic']=I('post.headpic');

            $data['title']=I('post.title');
            $data['type']=I('post.type');
            $data['intro']=I('post.intro');
            $time=date("Y-m-d H:i:s");
            $data['createtime']=strtotime($time);
            unset($data['id']);
            $result=D('News')->addData($data);
            if($result){
                $this->success('保存成功',U('Admin/News/index'));
            }else {
                $this->success('保存失败',U('Admin/News/index'));
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
            $result=D('News')->editData($where,$data);
            if($result){
                $this->success('保存成功',U('Admin/News/index'));
            }else {
                $this->success('保存失败',U('Admin/News/index'));
            }
        }else {
            $news=D('News')->where(array('id'=>I('get.id')))->find();
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
        $result=D('News')->deleteData($map);
        $this->success('删除成功',U('Admin/News/index'));
    }


}
