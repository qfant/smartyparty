<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class MeetingController extends AdminBaseController{
    public function ajaxMeetingList(){
        $childs=D('Department')->getDepartmentList(session('user.data')['department_id']);
        $childs = substr($childs,0,strlen($childs)-1);
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $username=I("get.username");
        $offset = ($page-1)*$rows;
        $countsql="select count(m.id) as total from qfant_meeting m where 1=1  AND m.department_id in (".$childs.")";
        $sql="select m.*,t.name AS departmentName from qfant_meeting  m LEFT JOIN qfant_department t ON m.department_id=t.id where 1=1  AND m.department_id in (".$childs.")";
        $param=array();
        if(!empty($username)){
            array_push($param,'%'.$username.'%');
            $countsql.=" and m.username like '%s'";
            $sql.=" and m.username like '%s'";
        }
        $sql.=" order by m.id desc  limit %d,%d";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('Meeting')->query($countsql,$param);
        $result['records']=$data[0]['total'];

        if($result['records'] >0 ) {
            $total_pages = ceil( $result['records']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $result['page']=$page;
        $result['total']=$total_pages;
        $data=D('Meeting')->query($sql,$param);
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
            $data['address']=I('post.address');
            $startdate= strtotime(I('post.startdate'));
            $enddate= strtotime(I('post.enddate'));
            $data['startdate']=$startdate; $data['enddate']=$enddate;
            $attendUser=I('post.attendUser');
            $data['headpic']=I('post.headpic');
            $depData=D('Department')->where(array('name'=>$org))->find();
            $data['department_id']=$depData['id'];
            unset($data['id']);
            $result=D('Meeting')->addData($data);
            $arr1 = explode("@",$attendUser);
            $num = count($arr1);

            for($i=0;$i<$num;++$i){
                $memberId= $arr1[$i];
                $datam['memberid']= $memberId;
                $datam['meetingid']=$result;
                $resultm=D('MeetingJoin')->addData($datam);
            }
            if($result){
                $this->success('保存成功',U('Admin/Meeting/index'));
            }else {
                $this->success('保存失败',U('Admin/Meeting/index'));
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
            $org=I('post.org');
            $attendUser=I('post.attendUser');
            $depData=D('Department')->where(array('name'=>$org))->find();
            $data['department_id']=$depData['id'];
            $startdate= strtotime(I('post.startdate'));
            $enddate= strtotime(I('post.enddate'));
            $data['startdate']=$startdate; $data['enddate']=$enddate;

           /* $branchData=D('Partybranch')->where(array('name'=> I('post.partybranch')))->field("id,name")->find();
            $data['partybranch_id']=$branchData['id'];*/

            $depData=D('Department')->where(array('name'=>$org))->find();
            $data['department_id']=$depData['id'];

            $result=D('Meeting')->editData($where,$data);
            if($result){
                $this->success('保存成功',U('Admin/Meeting/index'));
            }else {
                $this->success('保存失败',U('Admin/Meeting/index'));
            }
        }else {
            $meeting=D('Meeting')->where(array('id'=>I('get.id')))->find();
            $startdate =date('Y-m-d H:i:s',$meeting['startdate']);
            $enddate =date('Y-m-d H:i:s',$meeting['enddate']);
            $department=D('Department')->where(array('id'=>$meeting['department_id']))->find();
            $partybranch=D('Partybranch')->where(array('id'=>$meeting['partybranch_id']))->find();
            $this->assign('meeting',$meeting);
            $this->assign('startdate',$startdate);
            $this->assign('enddate',$enddate);
            $this->assign('departmentname',$department['name']);
            $this->assign('partybranchname',$partybranch['name']);
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
        $result=D('Meeting')->deleteData($map);
        $this->success('删除成功',U('Admin/Meeting/index'));
    }


}
