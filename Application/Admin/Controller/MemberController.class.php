<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class MemberController extends AdminBaseController{
    public function ajaxMemberList(){
        //$childs=D('Department')->getDepartmentList(session('user.data')['department_id']);
        //$childs = substr($childs,0,strlen($childs)-1);
        //print_r($childs);die;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $username=I("get.username");
        $offset = ($page-1)*$rows;
//        $countsql="select count(m.id) as total from qfant_member m where 1=1 and m.department_id in (".$childs.")";
        $countsql="select count(m.id) as total from qfant_member m where 1=1 ";
        $sql="select m.*,t.name AS departmentName,b.name as partybranchName ,p.name as pname from qfant_member m LEFT JOIN qfant_department t ON m.department_id=t.id LEFT JOIN qfant_partybranch b ON m.partybranch_id=b.id left join qfant_post p  on p.id= m.type where 1=1  ";
        $param=array();
        if(!empty($username)){
            array_push($param,'%'.$username.'%');
            $countsql.=" and m.truename like '%s'";
            $sql.=" and m.truename like '%s'";
        }


        $sql.=" order by m.id desc  limit %d,%d";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('Member')->query($countsql,$param);
        $result['records']=$data[0]['total'];

        if($result['records'] >0 ) {
            $total_pages = ceil( $result['records']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $result['page']=$page;
        $result['total']=$total_pages;
        $data=D('Member')->query($sql,$param);
      /*  foreach ($data as $key=>$basevalue) {
            $data[$key]['intro'] = htmlspecialchars_decode($basevalue['intro']);
        }*/
        $result["rows"] = $data;
        $result["status"] = 1;
       $this->ajaxReturn($result,'JSON');

    }
    /**
     * 添加
     */
    public function add(){

        if(IS_POST){

            $phone=I('post.phone');
            if(D('Member')->where(array('phone'=>$phone))->find()){
                $result["status"] = 2;
                $result["message"] = '手机号已存在';
                $this->ajaxReturn($result,'JSON');
            }else {
                $data['truename']=I('post.truename');
                $data['org']=I('post.org');
                $data['type']=I('post.type');
                $data['phone']=$phone;
                $data['password']=I('post.password');
                $postData=D('Post')->where(array('name'=>$data['type'] ))->field("id,name")->find();
                $data['type']=$postData['id'];
                $data['headpic']=I('post.headpic');


//                $branchData=D('Partybranch')->where(array('name'=> I('post.partybranch')))->field("id,name")->find();
//                $data['partybranch_id']=$branchData['id'];
                $depData=D('Department')->where(array('name'=>$data['org']))->find();
                $data['department_id']=$depData['id'];
                unset($data['id']);
                $result=D('Member')->addData($data);
                if($result){
                    $r["status"] = 1;
                    $r["message"] = '添加成功';
                    $this->ajaxReturn($r,'JSON');
                }else {
                    $r["status"] = 0;
                    $r["message"] = '添加失败';
                    $this->ajaxReturn($r,'JSON');
                }
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
            $data['password']=I('post.password');
            $data['truename']=I('post.truename');
            $data['phone']=I('post.phone');
            $data['type']=I('post.type');
            $data['status']=I('post.status');
            $data['headpic']=I('post.headpic');
            $org=I('post.org');

            $type=I('post.type');
            $dataowner=D('Member')->where(array('id'=>$id))->find();
            if($dataowner['phone']!=$data['phone']){//该判断是修改手机号
                if(D('Member')->where("phone='".$data['phone']."' and id <> ".$id)->find()){
                    $r["status"] = 2;
                    $r["message"] = '手机号已存在';
                    $this->ajaxReturn($r,'JSON');
                }else {
                    $depData=D('Department')->where(array('name'=>$org))->find();
                    $data['department_id']=$depData['id'];

//                    //所属企业
//                    $branchData=D('Partybranch')->where(array('name'=> I('post.partybranch')))->field("id,name")->find();
//                    $data['partybranch_id']=$branchData['id'];
                    //部门
                    $depData=D('Department')->where(array('name'=>$org))->find();
                    $data['department_id']=$depData['id'];

                    //岗位类型
                    $postData=D('Post')->where(array('name'=>$type))->find();
                    $data['type']=$postData['id'];

                    $result=D('Member')->editData($where,$data);

                    if($result){
                        $r["status"] = 1;
                        $r["message"] = '保存成功';
                        $this->ajaxReturn($r,'JSON');
                    }else {
                        $r["status"] = 0;
                        $r["message"] = '保存失败';
                        $this->ajaxReturn($r,'JSON');
                    }
                }
            }else {
                $depData=D('Department')->where(array('name'=>$org))->find();
                $data['department_id']=$depData['id'];

//                //所属企业
//                $branchData=D('Partybranch')->where(array('name'=> I('post.partybranch')))->field("id,name")->find();
//                $data['partybranch_id']=$branchData['id'];
                //部门
                $depData=D('Department')->where(array('name'=>$org))->find();
                $data['department_id']=$depData['id'];

                //岗位类型
                $postData=D('Post')->where(array('name'=>$type))->find();
                $data['type']=$postData['id'];

                $result=D('Member')->editData($where,$data);

                if($result){
                    $r["status"] = 1;
                    $r["message"] = '保存成功';
                    $this->ajaxReturn($r,'JSON');
                }else {
                    $r["status"] = 0;
                    $r["message"] = '保存失败';
                    $this->ajaxReturn($r,'JSON');
                }
            }
        }else {
            $member=D('Member')->where(array('id'=>I('get.id')))->find();
            $department=D('Department')->where(array('id'=>$member['department_id']))->find();
            $partybranch=D('Partybranch')->where(array('id'=>$member['partybranch_id']))->find();
            $type=D('Post')->where(array('id'=>$member['type']))->find();
            $this->assign('member',$member);
            $this->assign('departmentname',$department['name']);
            $this->assign('partybranchname',$partybranch['name']);
            $this->assign('typename',$type['name']);
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
        $result=D('Member')->deleteData($map);
        $this->success('删除成功',U('Admin/Member/index'));
    }
    public function ajaxMemberAll(){
        $departmentId=I("get.departmentId");
        $members=D('Member')->where(array('department_id'=>$departmentId))->field("id,truename")->select();
        $result["result"] = $members;
        $result["status"] = 1;
        $this->ajaxReturn($result,'JSON');
    }
    public function searchMember(){
        $name=I("post.name");
        $cond['name']=array('like','%'.$name.'%');
        $members=D('Member')->where($cond)->field("id,truename")->select();
        $result["result"] = $members;
        $result["status"] = 1;
        $this->ajaxReturn($result,'JSON');
    }

}
