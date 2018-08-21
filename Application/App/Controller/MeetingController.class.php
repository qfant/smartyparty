<?php
namespace App\Controller;

use Common\Controller\AppBaseController;

/**
 * 认证控制器
 */
class MeetingController extends AppBaseController
{
    private $caesar;

    public function _initialize()
    {
        parent::_initialize();
        Vendor('Caesar.Caesar');
        $this->caesar = new \Caesar();
    }


    /**
     * 党员管理会议列表
     * 传参：pageNo  pageSize
     */
    public  function meetingList(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $pageNo = $param['pageNo'];
                    $pageSize = $param['pageSize'];
                    $offset = ($pageNo - 1) * $pageSize;
                    $sql = "select id,name,startdate,enddate,headpic,address from qfant_meeting";
                    $param = array();
                    $sql .= " limit %d,%d";
                    array_push($param, $offset);
                    array_push($param, $pageSize);
                    $meeting = D('Meeting')->query($sql, $param);
                    foreach ($meeting as $k=>$basevalue) {
                        $meeting[$k]['startdate'] =date('Y-m-d H:i',$basevalue['startdate']);
                        $meeting[$k]['enddate'] =date('Y-m-d H:i',$basevalue['enddate']);
                    }
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                    $data['data']['meetingList'] = $meeting;
                    $data['data']['totalNum'] = D('Meeting')->count();
                }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }
    }

    /**
     * 接口参数： （进入会议时查询是否在会议名单内，如果没有就提示没有权限进入会议室）
     * 传参：id
     */
    public  function  meetingDetail(){
           if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $id=$param['id'];//会议id
                    $userid=$param['cparam']['userId'];//人员id

                    $isAttendMeeting= D("MeetingJoin")->where(array('meetingid' =>$id,'memberid' =>$userid))->find();//没有权限进入会议室详情
                   if($isAttendMeeting){
                       $meetingdetial = D("Meeting")->where(array('id' => $param['id']))->find();//会议详情
                       $attendMeetingUser= D("MeetingJoin")->where(array('meetingid' => $param['id']))->select();//参加会议的所有人
                       $data['data']['meetingdetial'] = $meetingdetial;
                       $data['data']['statementcount'] = D('MeetingRecord')->where(array('meetingid' =>$param['id']))->count();
					   $memberList=array();
                       foreach($attendMeetingUser as $k=>$value){
                           $detail=D('Member')->where(array('id'=>$value['memberid']))->find();
                           $memberList[]=$detail;
					   }   
                        $data['data']['memberList'] = $memberList;
                        $data['bstatus']['code'] = 0;
                        $data['bstatus']['des'] = '获取成功';					   
                   }else{
                       $data['bstatus']['code'] = 600;
                       $data['bstatus']['des'] = '没有权限进入会议室';
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
     * 会议发言列表
     */
    public function meetingStatementList(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $meetingid= $param['meetingid'];
                    $meetingRecord= D("MeetingRecord")->where(array('meetingid' =>$meetingid))->select();//
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                    $data['data']['meetingRecordList'] = $meetingRecord;
                    $data['data']['totalNum'] = D('MeetingRecord')->where(array('meetingid' =>$meetingid))->count();

                }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
               echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }
    }

    /**
     *
     */
    public function submitStatement(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $content= $param['content'];
                    $data['meetingid']=  $param['meetingid'];
                    $value = json_encode($content);
                    $value =preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $value);
                    $data['content'] = json_decode($value);
                    $data['memberid']=  $param['cparam']['userId'];
                    $data['createtime']=date("Y-m-d H:i:s");
                    if($param['id']){
                        $where['id']=$param['id'];
                        $result=D('MeetingRecord')->editData($where,$data);
                    }else{//不传id添加
                        unset($data['id']);
                        $result=D('MeetingRecord')->addData($data);
                    }
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

}
