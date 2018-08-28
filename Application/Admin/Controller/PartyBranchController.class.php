<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class PartyBranchController extends AdminBaseController{

    public function ajaxPartyBranchList(){
        $childs=D('Department')->getDepartmentList(session('user.data')['department_id']);
        $childs = substr($childs,0,strlen($childs)-1);
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $name=I("get.name");
        $offset = ($page-1)*$rows;
        //$countsql="select count(n.id) AS total from  qfant_partybranch n where 1=1 AND n.department_id in (".$childs.")";
        $countsql="select count(n.id) AS total from  qfant_partybranch n where 1=1 ";
        //$sql="select n.*,t.name AS departmentName, m.name as mname from qfant_partybranch n LEFT JOIN qfant_department t ON n.department_id=t.id LEFT JOIN qfant_partymanager m ON m.id=n.managerid where 1=1 AND n.department_id in (".$childs.")";
		$sql="select n.*,t.name AS departmentName, m.truename as mname from qfant_partybranch n LEFT JOIN qfant_department t ON n.department_id=t.id LEFT JOIN qfant_member m ON m.id=n.managerid where 1=1 ";
        $param=array();
        if(!empty($name)){
            array_push($param,'%'.$name.'%');
            $countsql.=" and n.name like '%s' ";
            $sql.=" and n.name like '%s'";
        }
        $sql.=" order by id desc  limit %d,%d";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('Partybranch')->query($countsql,$param);
        $result['records']=$data[0]['total'];

        if($result['records'] >0 ) {
            $total_pages = ceil( $result['records']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $result['page']=$page;
        $result['total']=$total_pages;
        $data=D('Partybranch')->query($sql,$param);
        foreach ($data as $key=>$basevalue) {
            $data[$key]['intro'] = htmlspecialchars_decode($basevalue['intro']);
            $data[$key]['companyface'] = htmlspecialchars_decode($basevalue['companyface']);
            $data[$key]['zhendi'] = htmlspecialchars_decode($basevalue['zhendi']);
            $data[$key]['activity'] = htmlspecialchars_decode($basevalue['activity']);
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
            $data['name']=I('post.name');
            $data['phone']=I('post.phone');
            $data['department_id']=I('post.department_id');
            $data['intro']=I('post.intro');
            $data['companyface']=I('post.companyface');
            $data['zhendi']=I('post.zhendi');
            $data['activity']=I('post.activity');
            $data['zhidao']=I('post.zhidao');
            $data['signinradius']=I('post.signinradius');
            $data['address']=I('post.address');
            $data['mamnagerid']=I('post.mamnagerid');
            $data['latitude']=I('post.latitude');
            $data['longitude']=I('post.longitude');
            $data['managerid']=I('post.managerid');
            $data['org']=I('post.org');
            $depData=D('Department')->where(array('name'=> $data['org']))->field("id,name")->find();
            $data['department_id']=$depData['id'];
            unset($data['id']);
            $result=D('Partybranch')->addData($data);
            if($result){
                $this->success('保存成功',U('Admin/PartyBranch/index'));
            }else {
                $this->success('保存失败',U('Admin/PartyBranch/index'));
            }
        }else {
            $departs=D('Department')->where("pid !=0")->field("id,name")->select();
            $this->assign('departs',$departs);
            $this->display();
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        if(IS_POST){
            $data=I('post.');
            $type = $_POST['id'];
            $where['id']=$type;
            $data['name']=I('post.name');
            $data['phone']=I('post.phone');
            $data['department_id']=I('post.department_id');
            $data['intro']=I('post.intro');
            $data['companyface']=I('post.companyface');
            $data['zhendi']=I('post.zhendi');
            $data['mamnagerid']=I('post.mamnagerid');
            $data['activity']=I('post.activity');
            $data['signinradius']=I('post.signinradius');
            $data['zhidao']=I('post.zhidao');

            $data['address']=I('post.address');
            $data['latitude']=I('post.latitude');
            $data['longitude']=I('post.longitude');
            $data['org']=I('post.org');
            $depData=D('Department')->where(array('name'=> $data['org']))->find();
            $data['department_id']=$depData['id'];
            $result=D('Partybranch')->editData($where,$data);
            if($result){
                $this->success('保存成功',U('Admin/PartyBranch/index'));
            }else {
                $this->success('保存失败',U('Admin/PartyBranch/index'));
            }
        }else {
            $partyBranch=D('Partybranch')->where(array('id'=>I('get.id')))->find();
//            $partybranch=D('Partymanager')->where(array('id'=>$partyBranch['managerid']))->find();
            $this->assign('partyBranch',$partyBranch);

//            $this->assign('partybranchname',$partybranch['name']);
//            $this->assign('partybranchid',$partybranch['id']);
            $departs=D('Department')->where("pid !=0")->field("id,name")->select();
            $this->assign('departs',$departs);
			$member=D('Member')->where(array('id'=>$partyBranch['managerid']))->field("id,truename")->find();
			$this->assign('member',$member);
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
        $map1=array('orderid'=>$id);
        $result1=D('Driverorder')->deleteData($map1);//删除装车与订单关联的表
        $result=D('Order')->deleteData($map);
        if($result){
            $message['status']=1;
            $message['message']='删除成功';
        }else {
            $message['status']=0;
            $message['message']='删除失败';
        }
        $this->ajaxReturn($message,'JSON');
    }
    public function ajaxPartyBranchAll(){
        $departmentId=I("get.departmentId");
        $branchs=D('Partybranch')->where(array('department_id'=>$departmentId))->field("id,name")->select();
        $result["result"] = $branchs;
        $result["status"] = 1;
        $this->ajaxReturn($result,'JSON');
    }
    public function searchPartyBranch(){
        $name=I("post.name");
        $cond['name']=array('like','%'.$name.'%');
        $branchs=D('Partybranch')->where($cond)->field("id,name")->select();
        $result["result"] = $branchs;
        $result["status"] = 1;
        $this->ajaxReturn($result,'JSON');
    }
 }
