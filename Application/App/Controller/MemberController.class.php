<?php
namespace App\Controller;

use Common\Controller\AppBaseController;

/**
 * 认证控制器
 */
class MemberController extends AppBaseController
{
    private $caesar;

    public function _initialize()
    {
        parent::_initialize();
        Vendor('Caesar.Caesar');
        $this->caesar = new \Caesar();
    }

    /**传参  phone  password
     * 登录接口
     */

    public function login()
    {
        $key = I("post.key");
        $b = I("post.b");
        if($key && $b){
            $b = $this->caesar->clientDecode($key, $b);
            $param = json_decode($b,true);
            $phone = $param['phone'];
            $pass = $param['password'];
            $token = uniqid();
            $user = D("Member")->where(array('phone' => $phone, 'password' => $pass))->find();
            $res['customer_Id'] = $user['id'];
            $res['loginIden'] = $token;
            $res['loginTime'] = date("Y-m-d H:i:s", time());
            if ($user) {
                $login = D("LoginLogger")->where(array('customer_Id' => $user['id']))->find();
                if ($login) {
                    D("LoginLogger")->where(array('customer_Id' => $user['id']))->save($res);
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_SUCCESS');
                    $data['bstatus']['des'] = '登录成功';
                    $data['data']['token'] = $token;
                    $data['data']['id'] = $user['id'];
                    $data['data']['phone'] = $user['phone'];
                    $data['data']['departmentid'] = $user['department_id'];
                    $data['data']['type'] = $user['type'];
                }else{
                    D("LoginLogger")->add($res);
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_SUCCESS');;
                    $data['bstatus']['des'] = '登录失败';
                    $data['data']['token'] = $token;
                    $data['data']['id'] = $user['id'];
                    $data['data']['phone'] = $user['phone'];
                    $data['data']['departmentid'] = $user['department_id'];
                    $data['data']['type'] = $user['type'];
                }
            }else {
                $data['bstatus']['code'] = -1;
                $data['bstatus']['des'] = '登录失败';
                $data['data'] = '';
            }

            echo $this->caesar->clientEncode($key, json_encode($data));
        }

    }

    /**
     * 积分列表
     * 传参：pageNo  pageSize
     */
    public  function integral(){
     if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $pageNo = $param['pageNo'];
                $pageSize =$param['pageSize'];
                $offset = ($pageNo - 1) * $pageSize;

               $quarter =$param['quarter'];//季度
               $type = $param['type'];//类型
               $area = $param['area'];//区域
               $year = $param['year'];//年份
               $cat = $param['cat'];//年份
               $keyword = $param['keyword'];//年份
                $sql = "select m.id as id  , score,createtime, name,year,quarter  from qfant_integral m   where 1=1";
                $countsql = "select count(m.id) as totalcount  from qfant_integral m  where 1=1";
                $param=array();
                if($quarter!=0||$quarter!="0"){
                    if(!empty($quarter)){
                        $sql.=" and m.quarter=%d";
                        $countsql.=" and m.quarter=%d";
                        array_push($param,$quarter);
                    }
                }
                if($type!=0||$type!="0") {
                    if (!empty($type)) {
                        $sql .= " and m.type=%d";
                        $countsql .= " and m.type=%d";
                        array_push($param, $type);
                    }
                }
                if($area!=0||$area!="0") {
                    $sql .= "  AND m.department_id =%d";
                    $countsql .= "  AND m.department_id =%d";
                    array_push($param, $area);
                }
                if($year!=0||$year!="0") {
                     $sql.=" and m.year='%s'";
                     $countsql.=" and m.year='%s'";
                     array_push($param,$year);
                }
				if($cat!=0||$cat!="0") {
                     $sql.=" and m.type=%d";
                     $countsql.=" and m.type=%d";
                     array_push($param,$cat);
                }
				if($keyword!="") {
                     $sql.=" and m.name like '%s' ";
                     $countsql.=" and m.name like '%s' ";
                     array_push($param,'%'.$keyword."%");
                }
				$totalcount= D('Integral')->query($countsql, $param);
                $data['data']['totalNum'] =  $totalcount[0]['totalcount'];
				
                $sql .= " limit %d,%d";
                array_push($param, $offset);
                array_push($param, $pageSize);
                $integral = D('Integral')->query($sql, $param);
				foreach($integral as &$i){
                    if($i['quarter']==1){
						$i['quarter']="第一季度";
					}else if ($i['quarter']==2){
						$i['quarter']="第二季度";
					}else if ($i['quarter']==3){
						$i['quarter']="第三季度";
					}else if ($i['quarter']==4){
						$i['quarter']="第四季度";
					}else {
						$i['quarter']="第一季度";
					}
                }			
                $sqlyear="select DISTINCT year from  qfant_integral  ";

                $integralyear = D('Integral')->query($sqlyear, "");
                $memberList=array();
                foreach($integralyear as $k=>$value){
                    $record=array();
                    $record['id']=$k+1;
                    $record['name']=$value['year'];
                    $memberList[]=$record;
                }
                $data['bstatus']['code'] = 0;
                $data['bstatus']['des'] = '获取成功';
                  if($quarter==0||$quarter=="0"){
                      $quarter="";
                      $quarter[]=$quarter1=array("id"=>"1","name"=>"第一季度"); $quarter[]=$quarter2=array("id"=>"2","name"=>"第二季度"); $quarter[]=$quarter3=array("id"=>"3","name"=>"第三季度");$quarter[]=$quarter4=array("id"=>"4","name"=>"第四季度");
                  }else{
                      $quarter="";
                      $quarter[]=$quarter1=array("id"=>"1","name"=>"第一季度"); $quarter[]=$quarter2=array("id"=>"2","name"=>"第二季度"); $quarter[]=$quarter3=array("id"=>"3","name"=>"第三季度");$quarter[]=$quarter4=array("id"=>"4","name"=>"第四季度");
                  }

                $data['data']['quarter'] =$quarter;// array("1"=>"第一季度","2"=>"第二季度","3"=>"第三季度","4"=>"第四季度");

                if($type==0||$type=="0") {
                    $type="";
                    $type[]=$type1=array("id"=>"1","name"=>"药业企业 "); $type[]=$type2=array("id"=>"2","name"=>"白酒企业"); $type[]=$type3=array("id"=>"3","name"=>"食品企业");
                    $type[]=$type4=array("id"=>"4","name"=>"服务行业 "); $type[]=$type5=array("id"=>"5","name"=>"专业合作社"); $type[]=$type6=array("id"=>"6","name"=>"民办学校");
                    $type[]=$type7=array("id"=>"7","name"=>"民办医院 "); $type[]=$type8=array("id"=>"8","name"=>"其它行业");
                }else{
                    $type="";
                    $type[]=$type1=array("id"=>"1","name"=>"药业企业 "); $type[]=$type2=array("id"=>"2","name"=>"白酒企业"); $type[]=$type3=array("id"=>"3","name"=>"食品企业");
                    $type[]=$type4=array("id"=>"4","name"=>"服务行业 "); $type[]=$type5=array("id"=>"5","name"=>"专业合作社"); $type[]=$type6=array("id"=>"6","name"=>"民办学校");
                    $type[]=$type7=array("id"=>"7","name"=>"民办医院 "); $type[]=$type8=array("id"=>"8","name"=>"其它行业");
                }

                $data['data']['type'] = $type;//array("1"=>"药企","2"=>"酒厂","3"=>"农产品");
                $data['data']['cat'] = array(array('id'=>1,'name'=>'社会组织党组织'),array('id'=>2,'name'=>'非公企业党组织'));//array("1"=>"药企","2"=>"酒厂","3"=>"农产品");

				$area=D('Department')->where("pid>0")->field("id,name")->select();
                $data['data']['area'] =$area;// array("1"=>"亳州市","2"=>"涡阳县","3"=>"利辛县","4"=>"蒙城县","5"=>"谯城区","6"=>"亳州经开区","7"=>"亳芜园区");
                $data['data']['year'] = $memberList;
                $data['data']['integralList'] = $integral;
             echo $this->caesar->clientEncode($key, json_encode($data));
            }
        }
    }

    /**
     *申请注册账号
     */
    public  function  applyaccount(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $data['name']=  $param['name'];
                $data['phone']=  $param['phone'];
                $data['department']=  $param['department'];
                unset($data['id']);
                $result=D('Apply')->addData($data);
                if($result){
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '申请成功';
                }else{
                    $data['bstatus']['code'] = -1;
                    $data['bstatus']['des'] = '申请失败';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }
    }
    /**
     * 签到状态
     * 传参：pageNo  pageSize
     */
    public  function signstatus(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $member_id= $param['cparam']['userId'];
                $signtime=date("Y-m-d",strtotime(strtotime(time())));
                $signtime1=date("Y-m-d",strtotime("+1day",strtotime(time())));
                $sql="select * from  qfant_signrecord where member_id='$member_id'  BETWEEN  '".$signtime. " ' and '"  .$signtime1. "'";
                $record=D("Signrecord")->query($sql);
                if ($record) {
                    $today = strtotime(date("Y-m-d"), time());
                    $end = $today + 60 * 60 * 24;
                    $condition['signtime'] = array('gt', $today);
                    $condition['signtime'] = array('lt', $end);
                    $condition['type'] = array('eq', 1);
                    $signin = D("Signrecord")->where($condition)->find();
                    $condition['type'] = array('eq', 2);
                    $signout = D("Signrecord")->where($condition)->find();
                    if ($signin && empty($signout)) {
                        $data['data']['signin'] = 0;
                    } elseif (empty($signin) && empty($signout)) {
                        $data['data']['signin'] = 1;
                    } else {
                        $data['data']['signin'] = 1;
                    }
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                } else {
                    $data['data']['signin'] = 1;
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '可以签到';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));
            }
        }
    }

    /**
     * 工作日志列表
     * 暂时先查我的列表,以及数量
     */
    public function  worklogList(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $memberId=$param['cparam']['userId'];
                $type=$param['type'];
                $pageNo = $param['pageNo'];
                $pageSize = $param['pageSize'];
                $offset = ($pageNo - 1) * $pageSize;
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
					if ( $type==1){
						$data['data']['totalNum'] =  D('Worklog')->count();
						$sql = "select  id,title,address,content,createtime,pic1,pic2 from  qfant_worklog where 1=1  order by createtime desc ";
					}else {
						$data['data']['totalNum'] =  D('Worklog')->where(array('member_id' => $memberId))->count();
						$sql = "select  id,title,address,content,createtime,pic1,pic2 from  qfant_worklog where member_id='$memberId' order by createtime desc ";
					}
                    $param = array();
                    $sql .= " limit %d,%d";
                    array_push($param, $offset);
                    array_push($param, $pageSize);
                    $wrkLogs = D('Worklog')->query($sql, $param);
                    $data['bstatus']['code'] = 0;
               //     $data['data']['totalNum'] = D('Info')->where(array('isstardand' =>$isstardand,'type' =>$type))->count();
                    $data['bstatus']['des'] = '获取成功';
                    $data['data']['workLogResult'] = $wrkLogs;
                }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));
            }
        }
    }

    /**
     * 写工作日志
     */
    public function submitWorklog(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $data['pic1']=  $param['pic1'];
                    $data['pic2']=  $param['pic2'];
                    $content=  $param['content'];
                    $data['title']=  $param['title'];
                    $data['address']=  $param['address'];
                    $data['createtime']=  time();
                    $value = json_encode($content);
                    $value =preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $value);
                    $data['content'] = json_decode($value);
                    $data['member_id']=  $param['cparam']['userId'];
                    unset($data['id']);
                    $result=D('Worklog')->addData($data);
                    if($result){
                        $data['bstatus']['code'] = 0;
                        $data['bstatus']['des'] = '提交成功';
                    }else{
                        $data['bstatus']['code'] = -1;
                        $data['bstatus']['des'] = '提交失败';
                    }
                }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));
            }
        }
    }



    /**
     * 签到
     * 传参：pageNo  pageSize
     */
    public  function signin(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
				$login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
				if($login) {
					$record['longitude'] = $param['longitude'];
					$record['latitude'] = $param['latitude'];
					$record['address'] = $param['address'];
					$record['partybranch_id']  = $param['id'];
					$record['member_id']  = $param['cparam']['userId'];
					$record['type']  = 1;
					$record['signtime']  = time();
                    $member_id=$param['cparam']['userId'];
                    //判断签到只能一天一次签到
                    $signtime=date("Y-m-d",strtotime(strtotime(time())));
                    $signtime1=date("Y-m-d",strtotime("+1day",strtotime(time())));
                    $sql1="select * from  qfant_signrecord where member_id='$member_id'  BETWEEN  '".$signtime. " ' and '"  .$signtime1. "'";
                    $data1=D('Signrecord')->query($sql1);
                    if($data1[0]){
                        $data['bstatus']['code'] = -1;
                        $data['bstatus']['des'] = '你已签到';
                    }else{
                        $partybranch=D("Partybranch")->where(array('id'=>$record['partybranch_id']))->find();
                        if(empty($partybranch['latitude']) || empty($partybranch['longitude'])){
                            $result=D("Partybranch")->where(array('id'=>$record['partybranch_id']))->save(array('latitude'=>$record['latitude'],'longitude'=>$record['longitude'],'address'=>$record['address']));
							$result=D("Signrecord")->add($record);
							if($result){
								$data['bstatus']['code'] = 0;
								$data['bstatus']['des'] = '签到成功';
							}else {
								$data['bstatus']['code'] = -1;
								$data['bstatus']['des'] = '签到失败';
							}
                        }else {
							if($this->getDistance($partybranch['longitude'],$partybranch['latitude'],$record['longitude'],$record['latitude'])>2000){
								$data['bstatus']['code'] = -1;
								$data['bstatus']['des'] = '签到失败,距离太远';
							}else {
								$result=D("Signrecord")->add($record);
								$data['bstatus']['code'] = 0;
								$data['bstatus']['des'] = '签到成功';
							}
						
						}
                        
                        
                    }

				}else {
					$data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
				}

                echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }
    }
/**
 * 计算两点地理坐标之间的距离
 * @param  Decimal $longitude1 起点经度
 * @param  Decimal $latitude1  起点纬度
 * @param  Decimal $longitude2 终点经度 
 * @param  Decimal $latitude2  终点纬度
 * @param  Int     $unit       单位 1:米 2:公里
 * @param  Int     $decimal    精度 保留小数位数
 * @return Decimal
 */
function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit=2, $decimal=2){

    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI = 3.1415926;

    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;

    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI /180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
    $distance = $distance * $EARTH_RADIUS * 1000;

    if($unit==2){
        $distance = $distance / 1000;
    }

    return round($distance, $decimal);

}
	
    /**
     * 签到
     * 传参：pageNo  pageSize
     */
    public  function signout(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
				$login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
				if($login) {
					$record['longitude'] = $param['longitude'];
					$record['latitude'] = $param['latitude'];
					$record['partybranch_id']  = $param['id'];
					$record['member_id']  = $param['cparam']['userId'];
					$record['type']  = 2;
					$record['signtime']  = time();
					$partybranch=D("Partybranch")->where(array('id'=>$record['partybranch_id']))->find();
							if($this->getDistance($partybranch['longitude'],$partybranch['latitude'],$record['longitude'],$record['latitude'])>2000){
								$data['bstatus']['code'] = -1;
								$data['bstatus']['des'] = '签退失败,距离太远';
							}else {
								$result=D("Signrecord")->add($record);
								if($result){
									$data['bstatus']['code'] = 0;
									$data['bstatus']['des'] = '签退成功';
								}else {
									$data['bstatus']['code'] = -1;
									$data['bstatus']['des'] = '签退失败';
								}	
							}
								
				}else {					
					$data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
				}
            }
				echo $this->caesar->clientEncode($key, json_encode($data));			
        }
    }
    /**
     * 签到
     * 传参：pageNo  pageSize
     */
    public  function signList(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
				$login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
				if($login) {
					$year = $param['year'];
					$month= $param['month'];
					$member_id= $param['cparam']['userId'];
					if(empty($year) || empty($month)){
						$year = date('Y');
						$month = date('m');
					}
					$start=strtotime($year.'-'.$month.'-01');
					$end=strtotime("+1 month",$start);
					$signlist=D("Signrecord")->query("select r.*,b.name as partybranchname from qfant_signrecord r  LEFT  JOIN qfant_partybranch b on r.partybranch_id=b.id where r.member_id=".$member_id."  and r.signtime>=".$start."  and r.signtime<".$end);
					$signdata=array();
					if($signlist){
						foreach ($signlist as $k=>$val){
							$day=date('d',$val['signtime'])."日";
							$record=array();
							$record['signdate']=date("Y-m-d H:i:s" ,$val['signtime']);
							$record['partybranchname']=$val['partybranchname'];
							$record['type']=$val['type'];
							$record['day']=$day;
							$signdata[]=$record;
						}
						$data['data']['signList']=$signdata;
					}else {
						$data['data']['signListResult']=array();
					}
					$data['bstatus']['code'] = 0;
					$data['bstatus']['des'] = '获取成功';
				}else {
					$data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';					
				}              
               echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }
    }

    /**
     * 修改密码
     * 传参：电话  原始密码    新密码
     */
    public   function editPassword(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $phone = $param['phone'];
                    $oldPass = $param['oldPassword'];
                    $user = D("Member")->where(array('phone' => $phone, 'password' => $oldPass))->find();
                    if($user){
                        $data['password'] = $param['newPassword'];
                        $where['id']=$param['cparam']['userId'];
                         $result=D('Member')->editData($where,$data);
                        if($result){
                            $data['bstatus']['code'] = 0;
                            $data['bstatus']['des'] = '修改成功';
                        }else{
                            $data['bstatus']['code'] = -1;
                            $data['bstatus']['des'] = '修改失败';
                        }

                  }else{
                        $data['bstatus']['code'] = -1;
                        $data['bstatus']['des'] = '电话号码或者原始密码错误';
                    }

          }else{
            $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
            $data['bstatus']['des'] = '登录失效，请重新登录';
        }
        echo $this->caesar->clientEncode($key, json_encode($data));
            }
        }
    }
    public function updateMyPortrait()
    {
		$key = I("get.key");
        $b = I("get.b");
		if($key && $b){
            $b = $this->caesar->clientDecode($key, $b);
            $param = json_decode($b,true);
			$userId=$param['cparam']['userId'];
			$token=$param['cparam']['token'];
			$path='./Upload/avatar/'.date('Ymd').'/';
			if (! file_exists ( $path )) {
				mkdir ( "$path", 0777, true );
			}
			$filename=$token.$userId.'.'.$param['ext'];
			$this->receiveStreamFile($path.$filename);
			$result=D("Member")->where(array('id'=>$userId))->save(array('headpic'=>'/Upload/avatar/'.date('Ymd').'/'.$filename));
			if($result){
				$data['data']['portrait']=C("GLOBAL_PIC_URL").'/Upload/avatar/'.date('Ymd').'/'.$filename;
				$data['bstatus']['code']=0;
				$data['bstatus']['des']='获取成功';						
			}else {
				$data['bstatus']['code']=-1;
				$data['bstatus']['des']='上传头像失败';		
			}
	
		}else {
			$data['bstatus']['code']=-1;
			$data['bstatus']['des']='参数错误';		
		}	

		echo  $this->caesar->clientEncode($key,json_encode($data));
    }
	/** php 接收流文件 
	* @param  String  $file 接收后保存的文件名 
	* @return boolean 
	*/  
	function receiveStreamFile($receiveFile){  
	  
		$streamData = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';  
	  
		if(empty($streamData)){  
			$streamData = file_get_contents('php://input');  
		}  
	  
		if($streamData!=''){  
			$ret = file_put_contents($receiveFile, $streamData, true);  
		}else{  
			$ret = false;  
		}  
	  
		return $ret;  
	  
	}
    /**
     * 获取个人信息
     */
    public   function myInfo(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $memberId = $param['cparam']['userId'];
                    $member=D("Member")->where(array('id'=>$memberId))->find();
                    $partybranch=D("Partybranch")->where(array('id'=>$member['partybranch_id']))->field("name")->find();
                    $department=D("Department")->where(array('id'=>$member['department_id']))->field("name")->find();
                    $data['data']['userId']=$member['id'];
                    $data['data']['headpic']=C("GLOBAL_PIC_URL").$member['headpic'];
                    $data['data']['truename']=$member['truename'];
                    $data['data']['phone']=$partybranch['name'];
                    $data['data']['dname']=$department['name'];
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));
            }
        }
    }
    /**
     * 柱状图 详情 根据type 显示数量
     */
    public  function tongji()
    {
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $managerid= $param['managerId'];
                    $feigong= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid))->count();//非公企业数量
                    $zuzhi= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid))->count();//社会组织数量

                    $datasum[]=$feigong;
                    $datasum[]=$zuzhi;

                    $feigongguimo= D("Info")->field('id,name')->where(array('type' =>1,'orgmethod' =>0,'managerid'=>$managerid))->count();//单单独组建独组建
                    $zuzhiguimo= D("Info")->field('id,name')->where(array('type' =>2,'orgmethod' =>0,'managerid'=>$managerid))->count();//

                    $dataguimo[]=$feigongguimo;
                    $dataguimo[]=$zuzhiguimo;

                    $bar['sum']=$datasum;
                    $bar['sumguimo']=$dataguimo;

                    $feigong= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid,'isparty'=>1))->count();//单独党支部组建率
                    $feigongAll= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid))->count();//单独党支部组建率
                    $zuzhi= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid,'isparty'=>1))->count();//社会组织数量
                    $zuzhiAll= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid))->count();//社会组织数量
                    if($feigongAll>0){
                        $feigong=intval(($feigong/$feigongAll)*100);
                    }
                    if($zuzhiAll>0){
                        $zuzhi=intval(($zuzhi/$zuzhiAll)*100);
                    }
                    $dataisparty[]=$feigong;
                    $dataisparty[]=$zuzhi;

                    $feigongstd= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid,'isstardand'=>1))->count();//标准化达标率
                    $feigongstdAll= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid))->count();//
                    $zuzhistd= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid,'isstardand'=>1))->count();//
                    $zuzhistdAll= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid))->count();//
                    if($feigongstdAll>0){
                        $feigongstd=intval(($feigongstd/$feigongstdAll)*100);
                    }
                    if($zuzhistdAll>0){

                    }  $zuzhistd=intval(($zuzhistd/$zuzhistdAll)*100);


                    $dataisstd[]=$feigongstd;
                    $dataisstd[]=$zuzhistd;

                    $bar['isparty']=$dataisparty;
                    $bar['isstandard']=$dataisstd;
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                    $data['data']= $bar;

                }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }
    }

}
