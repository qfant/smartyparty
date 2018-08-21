<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class InfoController extends AdminBaseController{
    public function ajaxInfoList(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $areatype=I("get.areatype");
        $offset = ($page-1)*$rows;
        $countsql="select count(n.id) AS total from  qfant_info n where 1=1";
        $sql="select n.* from qfant_info n  where 1=1 ";
        $param=array();
        if(!empty($areatype)){
        //    array_push($param,$areatype);
            $countsql.=" and n.areatype =".$areatype;
            $sql.=" and n.areatype=".$areatype;
        }
        $sql.=" order by n.id desc  limit %d,%d";
        array_push($param,$offset);
        array_push($param,$rows);
        $data=D('Info')->query($countsql,$param);
        $result['records']=$data[0]['total'];

        if($result['records'] >0 ) {
            $total_pages = ceil( $result['records']/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $result['page']=$page;
        $result['total']=$total_pages;
        $data=D('Info')->query($sql,$param);
        $result["rows"] = $data;
        $result["status"] = 1;

        $this->ajaxReturn($result,'JSON');
    }

    public  function ajaxAll(){
        $sql ="select  * from qfant_partymanager";
        $data=D('Partymanager')->query($sql,"");
        $this->ajaxReturn($data,'JSON');
      /*  $data=D('Partymanager')->select();
        $this->ajaxReturn($data,'JSON');*/
    }
    /**
     * 添加
     */
    public function add(){
        if(IS_POST){
            $data=I('post.');
           /* $data['name']=I('post.name');
            $data['phone']=I('post.phone');
            $data['address']=I('post.address');*/
            unset($data['id']);
            $result=D('Info')->addData($data);
//var_dump($data);die;
            if($result){
                $this->success('保存成功',U('Admin/Info/index'));
            }else {
                $this->success('保存失败',U('Admin/Info/index'));
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
            $data['id']=I('post.id');
            $data=I('post.');
          /*  $data['name']=I('post.name');
            $data['phone']=I('post.phone');
            $data['address']=I('post.address');*/
            $where['id']=$data['id'];
            $result=D('Info')->editData($where,$data);
            if($result){
                $this->success('修改成功',U('Admin/Info/index'));
            }else {
                $this->success('修改失败',U('Admin/Info/index'));
            }
        }else {
            $info=D('Info')->where(array('id'=>I('get.id')))->find();
            $this->assign('info',$info);
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
            $result=D('Info')->deleteData($map);
            if($result){
                $this->success('删除成功',U('Admin/Info/index'));
            }else {
                $this->success('删除失败',U('Admin/Info/index'));

        }
    }

    /**
     * 数据可视化
     */
    public  function  numView(){
        $areatype=I('get.areatype');
    }


    function impInfo(){
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
           // var_dump($highestRow);die;
            for($i=2;$i<=$highestRow;$i++)
            {

                $data['name'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                $data['address'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                $data['area'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                $data['promot1'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();//为什么到不进去
                $data['employnumber'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();

                $data['scale'] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                if($data['scale']=="是"){
                    $data['scale'] =1;
                }else if($data['scale']=="否"){
                    $data['scale'] =0;
                }
                $data['isparty'] = $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                if($data['isparty']=="是"){
                    $data['isparty'] =1;
                }else if($data['isparty']=="否"){
                    $data['isparty'] =0;
                }/*else{
                    $message['status']=0;
                    $message['message']= '第'.$i.'行，错误，是否规模以上企业数据bucunzai ，保存失败!';
                    break;
                }*/
                $data['partytime'] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                $data['partyshape'] = $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();

                $data['orgmethod'] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                if($data['orgmethod']=="联合组建"){
                    $data['orgmethod'] =1;
                }else if($data['orgmethod']=="单独组建"){
                    $data['orgmethod'] =0;
                }

                $data['clerkname'] = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                $data['phone'] = $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                $data['membernum'] = $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();

                $data['isstardand'] = $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                if($data['isstardand']=="是"){
                    $data['isstardand'] =1;
                }else if($data['isstardand']=="否"){
                    $data['isstardand'] =0;
                }


                $data['areatype'] = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
               $manager= M('Partymanager')->where(array('name'=>$data['areatype']))->find();
                if($data['areatype']=="亳州市"){
                    $data['areatype'] =0;
                }else if($data['areatype']=="涡阳县"){
                    $data['areatype'] =1;
                }else if($data['areatype']=="蒙城县"){
                    $data['areatype'] =2;
                }else if($data['areatype']=="利辛县"){
                    $data['areatype'] =3;
                }else if($data['areatype']=="谯城区"){
                    $data['areatype'] =4;
                }else if($data['areatype']=="亳州经开区"){
                    $data['areatype'] =5;
                }
                else if($data['areatype']=="亳芜园区"){
                    $data['areatype'] =6;
                }
                $data['managerid'] =$manager['id'];

                $data['type'] = $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
               // print_r($data['type']);die;
                if(trim($data['type'])=="非公企业"){
                    $data['type'] =1;
                }else if(trim($data['type'])=="社会组织"){
                    $data['type'] =2;
                }

                M('Info')->add($data);

             /*   $message['status']=1;*/
                $message['message']='导入完成，保存成功!';

            }
        }else
        {

           // $message['status']=0;
            $message['message']='请选择上传文件！';
        }

        $this->success('导入完成，保存成功!',U('Admin/Info/index'));

    }

    public function exportExcel(){


        $m = D ('Info');
        $sql="SELECT * from qfant_info";
        $data = $m->query($sql);
        foreach ($data as $i=>$basevalue){
            if($data['scale']=="1"||$data['scale']==1){
                $data['scale'] ="是";
            }else if($data['scale']=="0"||$data['scale']==0){
                $data['scale'] ="否";
            }

            if($data['isparty']=="1"||$data['isparty']==1){
                $data['isparty'] ="是";
            }else if($data['isparty']=="0"||$data['isparty']==0){
                $data['isparty'] ="否";
            }

            if($data['orgmethod']=="1"||$data['orgmethod']==1){
                $data['orgmethod'] ="联合组建";
            }else if($data['orgmethod']=="0"||$data['orgmethod']==0){
                $data['orgmethod'] ="单独组建";
            }

            if($data['isstardand']=="1"||$data['isstardand']==1){
                $data['isstardand'] ="是";
            }else if($data['isstardand']=="0"||$data['isstardand']==0){
                $data['isstardand'] ="否";
            }
        }
        $filename="信息管理";
        $headArr=array("单位名称","地址","所在地区","出资人（负责人）姓名","从业人数","是否规模以上企业","是否成立党组织","成立党组织时间","党组织设置形式",
            "组建方式","党组织书记姓名","党组织书记手机号码","党员总数","标准化建设是否达标");
        $this->getExcel($filename,$headArr,$data);
    }
    public  function getExcel($fileName,$headArr,$data){

        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new \PHPExcel();

        //对数据进行检验
        if(empty($data) || !is_array($data)){
            die("data must be a array");
        }
        //检查文件名
        if(empty($fileName)){
            exit;
        }
        $date = date("Y_m_d", time());
        $fileName .= "_{$date}.xls";
        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I','J','K','L','M','N','O','P');
        for ($i = 0; $i < count($headArr); $i++)
        {
            $objPHPExcel->getActiveSheet()->setCellValue("$letter[$i]1", "$headArr[$i]");
        }
        for ($i = 2; $i <= count($data) + 1; $i++)
        {
            $j = 0;
            foreach ($data[$i - 2] as $key => $value)
            {
                $objPHPExcel->getActiveSheet()->setCellValue("$letter[$j]$i", "$value");
                if($j==14){

                }
                $j++;
            }
        }
        $write  = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');

    }

}
