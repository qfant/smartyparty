<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class RuleController extends AdminBaseController{

//******************权限***********************
    /**
     * 权限列表
     */
    public function index(){
        $this->display();
    }
    public function ajaxRules(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $result["total"]=D('AuthRule')->where(array('pid'=>0))->count();

        $data=D('AuthRule')->where(array('pid'=>0))->limit($offset.','.$rows)->select();
        foreach ($data as $key => $value){
            $children=D('AuthRule')->where(array('pid'=>$value['id']))->select();
            if($children){
                foreach ($children as $key2 => $value2){
                    $children2=D('AuthRule')->where(array('pid'=>$value2['id']))->select();
                    $children[$key2]['children']=$children2;
                    $children[$key2]['addlevel']=1;
                    $children[$key2]['state']='closed';
                }
            }

            if(count($children)>0){
                $data[$key]['children']=$children;
            }
            $data[$key]['addlevel']=1;
        }
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');
    }
    public function ajaxAuthTree(){
        $id=I('get.id');
        $data=D('AuthRule')->field('id,title')->where(array('pid'=>0))->select();
        $group_data=M('Auth_group')->where(array('id'=>$id))->find();
        $rules=explode(",",$group_data['rules']);
        foreach ($data as $key => $value){//一级权限
            $children=D('AuthRule')->where(array('pid'=>$value['id']))->select();
            if($children){
                foreach ($children as $key2 => $value2){//二级权限
                    $children2=D('AuthRule')->field('id,title')->where(array('pid'=>$value2['id']))->select();
                    if($children2) {
                        foreach ($children2 as $key3 => $value3) {//三级权限
                            $children2[$key3]['text'] = $value3['title'];
                            if (in_array($value3['id'], $rules)) {
                                $children2[$key3]['checked'] = true;
                            } else {
                                $children2[$key3]['checked'] = false;
                            }
                            $children2[$key3]['children'] = null;
                        }
                    }else {
                        if (in_array($value2['id'], $rules)) {
                            $children[$key2]['checked'] = true;
                        } else {
                            $children[$key2]['checked'] = false;
                        }
                    }
                    $children[$key2]['children']=$children2;
                    $children[$key2]['text']=$value2['title'];
                }
            }else {
                if (in_array($data['id'], $rules)) {
                    $data[$key]['checked'] = true;
                } else {
                    $data[$key]['checked'] = false;
                }
            }
            $data[$key]['children']=$children;
            $data[$key]['text']=$value['title'];
        }
        $this->ajaxReturn($data,'JSON');
    }
    /**
     * 添加权限
     */
    public function add(){
        $data=I('post.');
        unset($data['id']);
        $result=D('AuthRule')->addData($data);
        if($result){
            $message['status']=1;
            $message['message']='添加成功';
        }else {
            $message['status']=0;
            $message['message']='添加失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    /**
     * 修改权限
     */
    public function edit(){
        $data=I('post.');
        $map=array(
            'id'=>$data['id']
            );
        $result=D('AuthRule')->editData($map,$data);
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
     * 获取权限
     */
    public function get(){
        $id=I('get.id');
        $rule=D('AuthRule')->where(array('id'=>$id))->find();
        $this->ajaxReturn($rule,'JSON');
    }
    /**
     * 删除权限
     */
    public function delete(){
        $id=I('get.id');
        $map=array(
            'id'=>$id
            );
        $result=D('AuthRule')->deleteData($map);
        if($result){
            $message['status']=1;
            $message['message']='删除成功';
        }else {
            $message['status']=0;
            $message['message']='删除失败';
        }
        $this->ajaxReturn($message,'JSON');

    }
//*******************用户组**********************
    /**
     * 用户组列表
     */
    public function group(){
        $this->display();
    }
    public function ajaxGroup(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $result["total"]=D('AuthGroup')->count();

        $data=D('AuthGroup')->limit($offset.','.$rows)->select();
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');
    }
    /**
     * 添加用户组
     */
    public function add_group(){
        $data=I('post.');
        unset($data['id']);
        $result= D('AuthGroup')->addData($data);
        if($result){
            $message['status']=1;
            $message['message']='添加成功';
        }else {
            $message['status']=0;
            $message['message']='添加失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    /**
     * 修改用户组
     */
    public function edit_group(){
        $data=I('post.');
        $map=array(
            'id'=>$data['id']
            );
        $result=D('AuthGroup')->editData($map,$data);
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
     * 删除用户组
     */
    public function delete_group(){
        $id=I('get.id');
        $result=D('AuthGroup')->where(array('id'=>$id))->delete();
        if($result){
            $message['status']=1;
            $message['message']='删除成功';
        }else {
            $message['status']=0;
            $message['message']='删除失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

//*****************权限-用户组*****************
    /**
     * 分配权限
     */
    public function rule_group(){
        $data=I('post.');
        $map=array(
            'id'=>$data['id']
        );
        $result=D('AuthGroup')->editData($map,$data);
        if($result){
            $message['status']=1;
            $message['message']='修改成功';
        }else {
            $message['status']=0;
            $message['message']='修改失败';
        }
        $this->ajaxReturn($message,'JSON');

    }
//******************用户-用户组*******************
    /**
     * 添加成员
     */
    public function check_user(){
        $username=I('get.username','');
        $group_id=I('get.group_id');
        $group_name=M('Auth_group')->getFieldById($group_id,'title');
        $uids=D('AuthGroupAccess')->getUidsByGroupId($group_id);
        // 判断用户名是否为空
        if(empty($username)){
            $user_data='';
        }else{
            $user_data=M('Users')->where(array('username'=>$username))->select();
        }
        $assign=array(
            'group_name'=>$group_name,
            'uids'=>$uids,
            'user_data'=>$user_data,
            );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 添加用户到用户组
     */
    public function add_user_to_group(){
        $data=I('get.');
        $map=array(
            'uid'=>$data['uid'],
            'group_id'=>$data['group_id']
            );
        $count=M('AuthGroupAccess')->where($map)->count();
        if($count==0){
            D('AuthGroupAccess')->addData($data);
        }
        $this->success('操作成功',U('Admin/Rule/check_user',array('group_id'=>$data['group_id'],'username'=>$data['username'])));
    }

    /**
     * 将用户移除用户组
     */
    public function delete_users(){
        $id=I('get.id');
        D('AuthGroupAccess')->where(array('uid'=>$id))->delete();
        $result=D('users')->where(array('id'=>$id))->delete();
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
     * 管理员列表
     */
    public function admin_user_list(){
        $this->display();
    }
    public function ajaxUserList(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $result["total"]=D('users')->count();

        $data=D('users')->limit($offset.','.$rows)->select();
        foreach ($data as $key => $value) {//三级权限
            $ag=D('AuthGroup')->query("select g.title from qfant_auth_group g,qfant_auth_group_access ga where g.id=ga.group_id and ga.uid=".$value['id']);
            $deptName=D('Department')->field('name')->where(array('id'=>$value['department_id']))->find();
         //   var_dump($deptName[name]);die;
            $data[$key]['deptName']= $deptName['name'];
            if($ag){
                $data[$key]['groupname']=implode(',',$ag[0]);
            }
        }
        $result["rows"] = $data;
        $this->ajaxReturn($result,'JSON');
    }
    public function ajaxGroupAll(){
        $id=I('get.id');
        if($id>0){
            $user_groups=D("AuthGroupAccess")->field('group_id')->where(array('uid'=>$id))->select();
            if($user_groups){
                $data=D('AuthGroup')->field('id,title')->select();
                foreach ($data as $key => $value) {
                    if (in_array($value['id'], $user_groups[0])) {
                        $data[$key]['selected'] = true;
                    }
                }
            }else {
                $data=D('AuthGroup')->field('id,title')->select();
            }
        }else {
            $data=D('AuthGroup')->field('id,title')->select();
        }
        $this->ajaxReturn($data,'JSON');
    }
    /**
     * 添加管理员
     */
    public function add_admin(){
        $message=array();
        if(IS_POST){
            $data=I('post.');
            $Model = M(); // 实例化一个空对象
            $Model->startTrans(); // 开启事务
            $user['username']=$data['username'];
            $user['password']=md5($data['password']);
            $user['status']=$data['status'];
            $user['department_id']=$data['departmentId'];
            $user['register_time']=time();
            $result=$Model-> table('qfant_users')->add($user);
            if($result){
                if (!empty($data['group_ids'])) {
                    $groups=explode(',',$data['group_ids']);
                    foreach ($groups as $k => $v) {
                        $group=array(
                            'uid'=>$result,
                            'group_id'=>$v
                            );
                        $r=$Model-> table('qfant_auth_group_access')->add($group);

                    }                   
                }
                if($result){
                    $Model->commit();
                    $message['status']=1;
                    $message['message']='保存成功';
                }else {
                    $Model->rollback();
                    $message['status']=0;
                    $message['message']='保存失败';
                }
            }else{
                $message['status']=0;
                $message['message']='保存失败';
            }
        }else{
            $message['status']=0;
            $message['message']='保存失败';
        }
        $this->ajaxReturn($message,'JSON');
    }

    /**
     * 修改管理员
     */
    public function edit_admin(){
        $message=array();
        if(IS_POST){
            $data=I('post.');
            if($this->checkusername($data)){
                $message['status']=0;
                $message['message']='此账号已存在';
            }else {
                $Model = M(); // 实例化一个空对象
                $Model->startTrans(); // 开启事务
                // 修改权限
                $Model-> table('qfant_auth_group_access')->where(array('uid'=>$data['id']))->delete();
                $groups=explode(',',$data['group_ids']);
                foreach ($groups as $k => $v) {
                    $group=array(
                        'uid'=>$data['id'],
                        'group_id'=>$v
                    );
                    $Model-> table('qfant_auth_group_access')->add($group);
                }
                $data=array_filter($data);
                // 如果修改密码则md5
                if (!empty($data['password'])) {
                    $user['password']=md5($data['password']);
                }
                $user['username']=$data['username'];
                $user['department_id']=$data['departmentId'];
                $user['status']=$data['status'];
                $result= $Model-> table('qfant_users')->where(array('id'=>$data['id']))->save($user);
                $Model->commit();
                    $message['status']=1;
                    $message['message']='保存成功';
            }

        }else{
            $message['status']=0;
            $message['message']='编辑失败';
        }
        $this->ajaxReturn($message,'JSON');
    }
	function checkusername($data){
        if($data['id']>0){
            $sum=D("Users")->where(array('id'=>$data['id'],'username'=>$data['username']))->count();

            if($sum>1){
                return true;
            }else {
                return false;
            }
        }else {
            $user=D("Users")->field('id')->where(array('username'=>$data['username']))->find();
            if($user){
                return flase;
            }else {
                return true;
            }
        }
    }
}
