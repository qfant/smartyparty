<?php
namespace App\Controller;

use Common\Controller\AppBaseController;

/**
 * 认证控制器
 */
class CompanyController extends AppBaseController
{
    private $caesar;

    public function _initialize()
    {
        parent::_initialize();
        Vendor('Caesar.Caesar');
        $this->caesar = new \Caesar();
    }

    public  function companyList(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $pageNo =$param['pageNo'];
                $pageSize = $param['pageSize'];
                $type = $param['type'];
                $managerId = $param['managerId'];
                $name = $param['name'];
                $offset = ($pageNo - 1) * $pageSize;
                $sql = "select  id,name from qfant_partybranch n where 1=1  and type=".$type.' and managerid='.$managerId;
                $countsql="select COUNT(id) as total from qfant_partybranch n  where 1=1 and type=".$type.' and managerid='.$managerId;
                $param = array();
                if(!empty($name)){
                    $sql.=" and n.name  like '%s' ";
                    $countsql.=" and n.name  like '%s' ";
                    array_push($param,'%'.$name.'%');
                }
                $sql .= " order by id desc   limit %d,%d";
                array_push($param, $offset);
                array_push($param, $pageSize);
                $news = D('Partybranch')->query($sql, $param);
                $countdata=D('Partybranch')->query($countsql,$param);

                $data['bstatus']['code'] = 0;
                $data['bstatus']['des'] = '获取成功';
                $data['data']['newsList'] = $news;
                $data['data']['totalNum'] =  $countdata[0]['total'];
                echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }

    }
    /**
     * 周边企业
     * 传参：longitude   经度
     *       latitude   维度
     */
  public  function nearby(){
           if (IS_POST) {
             $key = I("post.key");
             $b = I("post.b");
           if ($key && $b) {
                 $b = $this->caesar->clientDecode($key, $b);
                 $param = json_decode($b, true);
                // $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
               //if($login) {
                     $longitude = $param['longitude'];//115.790492;
                     $latitude = $param['latitude'];// 33.861641;
                     $sql="select  pb.id as id,pb.managerid as managerid,  longitude,latitude,pb.name as name,pb.intro as intro, pb.id,pb.address,pm.NAME AS manager, pm.phone AS phone  , ROUND(6378.138*2*ASIN(SQRT(POW(SIN((" . $latitude . "*PI()/180-latitude*PI()/180)/2),2)+COS(" . $latitude . "*PI()/180)*COS(latitude*PI()/180)*POW(SIN((" . $longitude . "*PI()/180-longitude*PI()/180)/2),2)))*1000) AS juli
                        FROM qfant_partybranch pb left join qfant_partymanager pm on pb.managerid = pm.id where ROUND(6378.138*2*ASIN(SQRT(POW(SIN((" . $latitude . "*PI()/180-latitude*PI()/180)/2),2)+COS(" . $latitude . "*PI()/180)*COS(latitude*PI()/180)*POW(SIN((" . $longitude . "*PI()/180-longitude*PI()/180)/2),2)))*1000)<=3000  ORDER BY juli ASC ";
                     $param = array();
                     $partybranch = D('Partybranch')->query($sql, $param);
                     $data['bstatus']['code'] = 0;
                     $data['bstatus']['des'] = '获取成功';
                   foreach ($partybranch as $i=>$basevalue){
                       $partybranch[$i]['intro']=htmlspecialchars_decode($basevalue['intro']);
                       if($basevalue['manager']==null){
                           $partybranch[$i]['manager']="";
                       }
                       if($basevalue['phone']==null){
                           $partybranch[$i]['phone']="";
                       }

                   }
                     $data['data']['partyBranchList'] = $partybranch;

             //}else{
                     //$data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
					$data['bstatus']['code'] = 0;
                     $data['bstatus']['des'] = '获取成功';
                // }

                echo $this->caesar->clientEncode($key, json_encode($data));

             }
      }

    }
    /**
     * 周主管单位
     * 传参：longitude   经度
     *       latitude   维度
     */
    public  function managermentList(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $data['data'] = D("Partymanager")->select();
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
     * 企业范围
     * 传参：企业主键id,type 1 2  3 4
     */
    public  function partyBranchDetial(){	
		if (IS_POST) {
             $key = I("post.key");
             $b = I("post.b");
           if ($key && $b) {
                 $b = $this->caesar->clientDecode($key, $b);
                 $param = json_decode($b, true);
                 $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
               if($login) {
                       $id=$param['id'];
                       $sql="SELECT  pb.* ,pm.NAME AS manager, pm.phone AS phone  FROM qfant_partybranch pb LEFT JOIN qfant_partymanager pm on pb.managerid=pm.id where pb.id='$id' ";
                       $param = array();
                       $partyBranchdetial = D("Partybranch")->query($sql, $param);
                       $partyBranchdetial=$partyBranchdetial[0];
                       $partyBranchdetial['intro']=htmlspecialchars_decode($partyBranchdetial['intro']);
                       $partyBranchdetial['companyface']=htmlspecialchars_decode($partyBranchdetial['companyface']);
                       $partyBranchdetial['zhendi']=htmlspecialchars_decode($partyBranchdetial['zhendi']);
                       $partyBranchdetial['activity']=htmlspecialchars_decode($partyBranchdetial['activity']);
                       $partyBranchdetial['name']=$partyBranchdetial['name'];
                       if($partyBranchdetial['manager']==null){
                           $partyBranchdetial['manager']="";
                       }
                       if($partyBranchdetial['phone']==null){
                           $partyBranchdetial['phone']="";
                       }
                        $data['data']['partyBranchDetial']=$partyBranchdetial;
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
     * 企业范围
     * 传参：企业主键id,type 1 2  3 4
     */
    public  function partyBranchIntro(){	
		if (IS_POST) {
             $key = I("post.key");
             $b = I("post.b");
           if ($key && $b) {
                 $b = $this->caesar->clientDecode($key, $b);
                 $param = json_decode($b, true);
                 $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
               if($login) {

					$partyBranchdetial = D("Partybranch")->field('id,name,intro,companyface,zhendi,activity,signinradius,address')->where(array('id' => $param['id']))->find();
					if($param['id']==2){
						$data['data']['content'] =htmlspecialchars_decode( $partyBranchdetial['companyface']);
					}else if($param['id']==3){
						$data['data']['content'] = htmlspecialchars_decode($partyBranchdetial['zhendi']);
					}else if($param['id']==4){
						$data['data']['content'] = htmlspecialchars_decode($partyBranchdetial['activity']);
					}else {
						$data['data']['content'] = htmlspecialchars_decode($partyBranchdetial['intro']);
					}
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
     * 企业联系人详情
     * 传参：企业联系人  主键 id
     */
    public function zixun(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
				$login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
				if($login){
					$managerId=$param['managerId'];
					$content=$param['content'];
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
     * 周边企业关键字搜索
     * 传参：企业名称  传空 返回不能查询
     */
    public  function  nearbySearch(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $name = $param['name'];
                if(empty($name)){
                    $data['bstatus']['code'] = -1;
                    $data['bstatus']['des'] = '查询条件不能为空';
                }else{
					$condition['name']=array('like',"%$name%"); 
                    $partyBranchList = D("Partybranch")->field('id,name,longitude,latitude,managerid,intro,signinradius,address')->where($condition)->select();

                    $data['data']['partyBranchList'] = $partyBranchList;
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';                   
                }
				echo $this->caesar->clientEncode($key, json_encode($data));
            }
        }
    }

    /**
     * 页面展示指导员不能为空
     */
    public  function queryByZhidao(){
       // if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login){
                    $managerid= $param['cparam']['userId'];
                    $partyBranchList = D("Partybranch")->field('id,name,longitude,latitude,signinradius,address')->where(array('managerid'=>$managerid))->select();

                    $today = strtotime(date("Y-m-d"), time());
                    $end = $today + 60 * 60 * 24;
                    foreach ($partyBranchList as $k=>$val){
                        $signin = D("Signrecord")->where('type =1 and partybranch_id ='.$val['id'].' and member_id='.$managerid.' and signtime > '.$today.' and signtime <='.$end)->find();
                        $signout = D("Signrecord")->where('type =2 and partybranch_id ='.$val['id'].' and member_id='.$managerid.' and signtime > '.$today.' and signtime <='.$end)->find();
                        if ($signin && empty($signout)) {
                            $partyBranchList[$k]['signin'] = 0;
                            $partyBranchList[$k]['signout'] = 1;
                        } elseif (empty($signin) && empty($signout)) {
                            $partyBranchList[$k]['signin'] = 1;
                            $partyBranchList[$k]['signout'] = 0;
                        } else if ($signin && $signout) {
                            $partyBranchList[$k]['signin'] = 0;
                            $partyBranchList[$k]['signout'] = 0;
                        }else {
                            $partyBranchList[$k]['signin'] = 1;
                            $partyBranchList[$k]['signout'] = 0;
                        }
                    }
                    $signins=D("Signrecord")->query("select count(r.id) as shuliang,m.name from qfant_signrecord r,qfant_partybranch b,qfant_department m where r.partybranch_id=b.id and b.managerid=m.id and r.type=1 and m.pid=1 group by m.name  order by m.order_num desc ");//签到
                    $signouts=D("Signrecord")->query("select count(r.id)  as shuliang,m.name from qfant_signrecord r,qfant_partybranch b,qfant_department m where r.partybranch_id=b.id and b.managerid=m.id and r.type=1 and m.pid=1 group by m.name  order by m.order_num desc ");//签到
                    $worklogs=D("Worklog")->query("select count(w.id)  as shuliang,d.name from qfant_worklog w,qfant_member m,qfant_department d where w.member_id=m.id and m.department_id=d.id and d.pid=1  group by d.name  order by d.order_num desc ");//签到

                    $data['data']['partyBranchList'] = $partyBranchList;
                    ///$data['data']['signin'] = D('Signrecord')->where(array('type' =>1))->count();
                   // $data['data']['signout'] = D('Signrecord')->where(array('type' =>2))->count();
                   // $data['data']['worklog'] = D('Worklog')->count();
                    $data['data']['signins'] = $signins;
                    $data['data']['signouts'] = $signouts;
                    $data['data']['worklogs'] = $worklogs;
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                    echo $this->caesar->clientEncode($key, json_encode($data));
                }else {
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                    echo $this->caesar->clientEncode($key, json_encode($data));
                }

            }
       // }
    }
}