<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class WorkLogController extends AdminBaseController{
    public function index(){
        $this->display();
    }

    public function ajaxWorkLogList(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $name=I("get.name");
        $offset = ($page-1)*$rows;
        $countsql="select count(w.id) AS total from qfant_worklog w ,qfant_member m WHERE w.member_id = m.id";
        $sql="select m.truename,w.* from qfant_worklog w,qfant_member m WHERE w.member_id = m.id";
        $param=array();
        if(!empty($name)){
            array_push($param,'%'.$name.'%');
            $countsql.=" and w.title like '%s' ";
            $sql.=" and w.title like '%s' ";
        }
        $sql.=" order by w.createtime desc  limit %d,%d";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('Worklog')->query($countsql,$param);
        $result['records']=$data[0]['total'];

        if($result['records'] >0 ) {
            $total_pages = ceil( $result['records']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $result['page']=$page;
        $result['total']=$total_pages;
        $data=D('Worklog')->query($sql,$param);
        foreach ($data as $key=>$basevalue) {
            $data[$key]['createtime'] = date('Y-m-d H:i:s',$basevalue['createtime']);
        }
        $result["rows"] = $data;
        $result["status"] = 1;
        $this->ajaxReturn($result,'JSON');
    }

    /*public  function ajaxAll(){
        $sql ="select  * from qfant_partymanager";
        $data=D('Partymanager')->query($sql,"");
        $this->ajaxReturn($data,'JSON');*/
      /*  $data=D('Partymanager')->select();
        $this->ajaxReturn($data,'JSON');*/
   // }
    /**
     * 添加
     */
    /*public function add(){
        if(IS_POST){
            $data['name']=I('post.name');
            $data['principal']=I('post.principal');
            $data['phone']=I('post.phone');
            $data['department_id']=I('post.department_id');
            unset($data['id']);
            $result=D('Partymanager')->addData($data);

            if($result){
                $this->success('保存成功',U('Admin/PartyManager/index'));
            }else {
                $this->success('保存失败',U('Admin/PartyManager/index'));
            }
        }else {
           $this->display();
        }

    }*/
    /**
     * 编辑
     */
    /*public function edit(){
        if(IS_POST){
            $data['id']=I('post.id');
            $data['name']=I('post.name');
            $data['principal']=I('post.principal');
            $data['phone']=I('post.phone');
            $data['department_id']=I('post.department_id');
            //print_r($data);die;
            $where['id']=$data['id'];
            $result=D('Partymanager')->editData($where,$data);
            if($result){
                $this->success('修改成功',U('Admin/PartyManager/index'));
            }else {
                $this->success('修改失败',U('Admin/PartyManager/index'));
            }
        }else {
            $manager=D('Partymanager')->where(array('id'=>I('get.id')))->find();
            $this->assign('manager',$manager);
            $this->display();
        }
    }*/
    /**
     * 删除站点
     */
   /* public function delete(){
        $id=I('get.id');
        $map=array(
            'id'=>$id
        );
        //删除站点，删除订单的站点以及到站时间
        //1.根据站点主键id，查找发车的id，cardriveid
        //2.根据cardriveid找到该发车的里面的运单，然后运单的站点、时间跟着删除
        $count=D('Partybranch')->where(array('managerid'=>$id))->count();
        if($count>0){
            $this->success('先删除下属企业',U('Admin/PartyManager/index'));
        }else {
            $result=D('Partymanager')->deleteData($map);
            if($result){
                $this->success('删除成功',U('Admin/PartyManager/index'));
            }else {
                $this->success('删除失败',U('Admin/PartyManager/index'));
            }
        }
    }*/

    /*public function ajaxCarDriv(){
        $orderid=I('get.orderid');
        $driver=I('post.driver');
        $number=I('post.number');
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $countsql="select  count(r.id )as total  FROM qfant_car AS r ,qfant_cardrive AS d WHERE r.id = d.carid ";
        $sql ="select r.id as carid,r.driver as driver ,r.carnumber as carnumber,d.id AS cardriveid,d.carid,d.startdate as startdate ,d.number as number FROM qfant_car AS r ,qfant_cardrive AS d WHERE r.id = d.carid ";
        $param=array();
        if(!empty($driver)){
            $countsql.=" and r.driver like '%s'";
            $sql.=" and r.driver like '%s'";
            array_push($param,'%'.$driver.'%');
        }
        if(!empty($number)){
            $countsql.=" and d.number like '%s'";
            $sql.=" and d.number like '%s'";
            array_push($param,'%'.$number.'%');
        }
        array_push($param,$offset);
        array_push($param,$rows);
        $sql.=" order by d.startdate desc limit %d,%d";
        $data=D('Cardrive')->query($countsql,$param);
        $result['total']=$data[0]['total'];
        $data=D('Cardrive')->query($sql,$param);
        foreach ($data as $key=>$basevalue){
            $data[$key]['orderid']= $orderid;
        }
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');

    }*/
}
