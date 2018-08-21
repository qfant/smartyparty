<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class IntegralController extends AdminBaseController{
    public function ajaxIntegralList(){
        $childs=D('Department')->getDepartmentList(session('user.data')['department_id']);
        $childs = substr($childs,0,strlen($childs)-1);
       // var_dump($childs);die;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $title=I("get.title");
        $offset = ($page-1)*$rows;
        $countsql="select count(i.id) as  total from qfant_integral i  where i.department_id in (".$childs.")";
        $sql="select i.* ,b.name as partybranchname ,d.name as departmentname from qfant_integral i LEFT JOIN qfant_partybranch b on i.partybranch_id=b.id LEFT JOIN qfant_department d on i.department_id=d.id and i.department_id in (".$childs.")";
        $param=array();
        if(!empty($title)){
            array_push($param,'%'.$title.'%');
            $countsql.=" and i.title like '%s' order by i.id desc";
            $sql.=" and i.title like '%s'";
        }
        $sql.=" order by i.id desc  limit %d,%d";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('Integral')->query($countsql,$param);
        $result['records']=$data[0]['total'];

        if($result['records'] >0 ) {
            $total_pages = ceil( $result['records']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $result['page']=$page;
        $result['total']=$total_pages;
        $data=D('Integral')->query($sql,$param);

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
            $partybranch=I('post.partybranch');
            $depData=D('Partybranch')->where(array('name'=>$partybranch))->find();
            $data['partybranch_id']=$depData['id'];
            $time=date("Y-m-d H:i:s");
            $data['createtime']= $time;
            unset($data['id']);
            $result=D('Integral')->addData($data);
            if($result){
                $this->success('保存成功',U('Admin/Integral/index'));
            }else {
                $this->success('保存失败',U('Admin/Integral/index'));
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
            $data['score']=I('post.score');
            $partybranch=I('post.partybranch');
            $depData=D('Partybranch')->where(array('name'=>$partybranch))->find();
            $data['partybranch_id']=$depData['id'];
            $data['year']=I('post.year');
            $data['quarter']=I('post.quarter');
            $data['type']=I('post.type');
            $result=D('Integral')->editData($where,$data);
            if($result){
                $this->success('保存成功',U('Admin/Integral/index'));
            }else {
                $this->success('保存失败',U('Admin/Integral/index'));
            }
        }else {
            $integral=D('Integral')->where(array('id'=>I('get.id')))->find();
            $partybranch=D('Partybranch')->where(array('id'=>$integral['partybranch_id']))->find();
            $this->assign('integral',$integral);
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
        $result=D('Integral')->deleteData($map);
        $this->success('删除成功',U('Admin/Integral/index'));
    }

    function impIntegral(){
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();// 实例化上传类
            $filepath='./Upload/excel/';
            $upload->exts = array('xlsx','xls');// 设置附件上传类型
            $upload->rootPath  =  $filepath; // 设置附件上传根目录
            $upload->saveName  =     'time';
            $upload->autoSub   =     false;
            if (!$info=$upload->upload()) {
                $this->error($upload->getError());
            }
            foreach ($info as $key => $value) {
                unset($info);
                $info[0]=$value;
                $info[0]['savepath']=$filepath;
            }
            vendor("PHPExcel.PHPExcel");
            $file_name=$info[0]['savepath'].$info[0]['savename'];
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
			//print_r($highestRow);die;
            for($i=4;$i<=$highestRow;$i++)
            {
				
                $time=date("Y-m-d H:i:s");
                $data['createtime']= $time;
                $name= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
               // $partybranch_id = M('Partybranch')->where(array('name'=>$name))->field('id')->find();//查询党组织id 若有 分条插入数据
                $departmentN = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                $department_id = M('Department')->where(array('name'=>$departmentN))->field('id')->find();//查询所属县区id 若有 分条插入数据
				//print_r("------------".$department_id);die;
                $typeN = $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();//单位类型，要分条插入
                if($typeN=="药业企业"){
                    $data['type'] =1;
                }else if($typeN=="白酒企业"){
                    $data['type'] =2;
                }else if($typeN=="食品企业"){
                    $data['type'] =3;
                }else if($typeN=="服务行业"){
                    $data['type'] =4;
                }else if($typeN=="专业合作社"){
                    $data['type'] =5;
                }else if($typeN=="民办学校"){
                    $data['type'] =6;
                }else if($typeN=="民办医院"){
                    $data['type'] =7;
                }else if($typeN=="其它行业"){
                    $data['type'] =8;
                }
                $partytypeN = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();//党组织类型
                if($partytypeN=="社会组织党组织"){
                    $data['partytype'] =1;
                }else if($partytypeN=="非公企业党组织"){
                    $data['partytype'] =2;
                }
				$year=date("Y");
               // if(empty($partybranch_id)){
                   // $message['message']= '第'.$i.'行，错误，该党组织名称不存在！';
                   // break;
               // }else{
                    if(empty($department_id)){
                        $message['message']= '第'.$i.'行，错误，该党组织所属县区不存在！';
                        break;
                    }else{
                        $score1 = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();//第一季度
                        if($score1!=0&&$score1!="0"||$score1!=null){
                            $data1['score']= $score1;
                            $data1['quarter']=1;
                            $data1['createtime']=  $data['createtime'];
                            //$data1['partybranch_id']=intval($partybranch_id);
                            $data1['name']=$name;
                            $data1['department_id']=$department_id['id'];
                            $data1['partytype']=$data['partytype'];
                            $data1['type']=$data['type'];
                            $data1['year']=$year;
							
                            M('Integral')->add($data1);
                        }
                        $score2 = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();//第二季度
                        if($score2!=0&&$score2!="0"||$score2!=null){
                            $data2['createtime']=  $data['createtime'];
                            $data2['score']= $score2;
                            $data2['quarter']=2;
                            //$data2['partybranch_id']=intval($partybranch_id);
							$data2['name']=$name;
                            $data2['department_id']=$department_id['id'];
                            $data2['partytype']=$data['partytype'];
                            $data2['type']=$data['type'];
							 $data2['year']=$year;
                            M('Integral')->add($data2);
                        }
                        $score3 = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();//第三季度
                        if($score3!=0&&$score3!="0"||$score3!=null){
                            $data3['createtime']=  $data['createtime'];
                            $data3['score']= $score3;
                            $data3['quarter']=3;
                           // $data3['partybranch_id']=intval($partybranch_id);
							$data3['name']=$name;
                            $data3['department_id']=$department_id['id'];
                            $data3['partytype']=$data['partytype'];
                            $data3['type']=$data['type'];
							$data2['year']=$year;
                            M('Integral')->add($data3);
                        }
                        $score4 = $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();//第四季度
                        if($score4!=0&&$score4!="0"||$score4!=null){
                            $data4['createtime']=  $data['createtime'];
                            $data4['score']= $score4;
                            $data4['quarter']=4;
                            //$data4['partybranch_id']=intval($partybranch_id);
							$data4['name']=$name;
                            $data4['department_id']=$department_id['id'];
                            $data4['partytype']=$data['partytype'];
                            $data4['type']=$data['type'];
							$data4['year']=$year;
                            M('Integral')->add($data4);
                        }
                       /* $score5 = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();//年度积分
                        if($score5!=0&&$score5!="0"||$score5!=null){
                            $data5['createtime']=  $data['createtime'];
                            $data5['sumscore']= $score5;
                           // $data5['partybranch_id']=intval($partybranch_id);
							$data1['name']=$name;
                            $data5['department_id']=$department_id;
                            $data5['partytype']=$data['partytype'];
                            $data5['type']=$data['type'];
							$data2['year']=$year;
                            M('Integral')->add($data5);
                        }*/
                        $message['message']='导入完成，保存成功!';
                    }
               // }
            }
        }else {
            $message['message']='请选择上传文件！';
        }

        //$this->success($message['message'],U('Admin/Integral/index'));

    }

}
