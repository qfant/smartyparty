<?php
namespace App\Controller;

use Common\Controller\AppBaseController;

/**
 * 认证控制器
 */
class IndexController extends AppBaseController
{
    private $caesar;

    public function _initialize()
    {
        parent::_initialize();
        Vendor('Caesar.Caesar');
        $this->caesar = new \Caesar();
    }

    /**
     * 个人中心首页
     */
    public function index()
    {

        // var_dump($caesar->clientEncode('F4C67DBE892A0351','{"cparam":{"imei":"864587029583557","versionCode":1,"versionName":"1.0.0"},"phone":"13212345678","verificationCode":"123456","room_id":1,"name":"测试"}'));
        //  var_dump( $this->caesar->clientDecode('F4C67DBE892A0351','0839FAD9B53AC1C925908F4FB7C0D9E99B9E660ACCE8EC021998BAE7CA01F524B1906688DA36CCF5A341085D91CCD3E49B9E7C88DA36CCF5A341085D90C6DAE49B9E6600D7E4B60F258A344F16C1DDED31397F84C5E5E904159CB2E4C504F2BB95391ECF1A52C8CC43478257BEC2E5E03C426606DAEFE90A1D9DBB4FD28E7CE03F483DCDB14AEA042B360D52BBCA4C219B23C2755E113C95AE'));
        // $this->display();
    }
	public function checkVersion()
    {
        // if(IS_POST){
        $key = I("post.key");
        $b = I("post.b");
        $b = $this->caesar->clientDecode($key, $b);
        $param = json_decode($b,true);
		$versionInfo=C("UPDATE_INFO");
		if($versionInfo){
			if($versionInfo['versionCode']>$param['cparam']['versionCode']){
				$info['upgradeInfo']['upgradeAddress']=$versionInfo['upgradeAddress'];
				$info['upgradeInfo']['upgradeNote']=$versionInfo['upgradeNote'];
				$info['upgradeInfo']['force']=$versionInfo['force'];
				$info['upgradeInfo']['versionCode']=$versionInfo['versionCode'];
				$info['upgradeInfo']['nversion']=$versionInfo['nversion'];
				$info['upgradeInfo']['upgradeFlag']=1;
			}else {
				$info['upgradeInfo']=null;
			}
		}else {
			$info['upgradeInfo']=null;
		}
        $data['bstatus']['code'] = 0;
        $data['bstatus']['message'] = '获取成功';
        $data['data'] = $info;		
        //print_r(json_encode($data));die;
        echo $this->caesar->clientEncode($key, json_encode($data));
        //  }
    }
    public function towns()
    {
        // if(IS_POST){
        $key = I("post.key");
        $b = I("post.b");
        $towns = D("Town")->select();
        foreach ($towns as $k => $val) {
            $villages = D("Village")->where(array('townid' => $val['id']))->select();
            foreach ($villages as $k2 => $val2) {
                $villages[$k2]['principal'] = $val['contact'];//负责人
                $villages[$k2]['mobile'] = $val['mobile'];//负责人电话
                $villages[$k2]['boundary'] = D("VillageBoundary")->where(array('villageid' => $val2['id']))->select();
            }
            $towns[$k]['children'] = $villages;
            $towns[$k]['boundary'] = D("TownBoundary")->where(array('townid' => $val['id']))->select();
        }
        $data['bstatus']['code'] = 0;
        $data['bstatus']['message'] = '获取成功';
        $data['data'] = $towns;
        //print_r(json_encode($data));die;
        echo $this->caesar->clientEncode($key, json_encode($data));
        //  }
    }

    public function villages()
    {
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            $b = $this->caesar->clientDecode($key, $b);
            $param = json_decode($b);
            $town = D("Town")->where(array('id' => $param['id']))->find();
            $villages = D("Village")->where(array('townid' => $param['id']))->select();
            foreach ($villages as $k => $val) {
                $villages[$k]['principal'] = $town['contact'];//负责人
                $villages[$k]['mobile'] = $town['mobile'];//负责人电话
                $villages[$k]['boundary'] = D("VillageBoundary")->where(array('villageid' => $val['id']))->select();
            }
            $data['bstatus']['code'] = 0;
            $data['bstatus']['message'] = '获取成功';
            $data['data'] = $villages;
            echo $this->caesar->clientEncode($key, json_encode($data));
        }
    }

    public function consult()
    {
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            $b = $this->caesar->clientDecode($key, $b);
            $param = json_decode($b);
            $consult['villageid'] = $param['villageid'];
            $consult['content'] = $param['content'];
            $result = D("VillageConsult")->add($consult);
            if ($result) {
                $data['bstatus']['code'] = 0;
                $data['bstatus']['message'] = '添加成功';
            } else {
                $data['bstatus']['code'] = -1;
                $data['bstatus']['message'] = '添加失败';
            }
            echo $this->caesar->clientEncode($key, json_encode($data));
        }
    }

    public function weather()
    {
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            $b = $this->caesar->clientDecode($key, $b);
            $param = json_decode($b);
            $w = $this->http("https://api.seniverse.com/v3/weather/now.json?key=fguvd3ahk6eneabr&location=Bozhou&language=zh-Hans&unit=c");
            $w = json_decode($w);
            $data['bstatus']['code'] = 0;
            $data['bstatus']['message'] = '获取成功';
            $data['data'] = $w->results;
            //print_r( json_encode($data));die;
            echo $this->caesar->clientEncode($key, json_encode($data));
        }
    }

    /**
     * 发送HTTP请求方法
     * @param  string $url 请求URL
     * @param  array $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @return array  $data   响应数据
     */
    function http($url, $params, $method = 'GET', $header = array(), $multi = false)
    {
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $header
        );
        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) throw new Exception('请求发生错误：' . $error);
        return $data;
    }

    public function newslist()
    {
        $type = I("get.type", 1);
        if ($type > 0) {
			if ($type==1){
				$catname = '政策宣传';				
			}else if ($type==2){
				$catname = '党建业务';
			}else {
				$catname = '工作动态';
			}
            $news = D('News')->where(array('type' => $type))->select();
            //print_r($cats);die;
            $this->assign("catname", $catname);
            $this->assign("news", $news);
			$this->display();
        } 
    }

    public function baseinfo()
    {
        $id = I("get.id", 0);
        if ($id > 0) {
            $town = D('Town')->where(array('id' => $id))->find();
            $town['baseinfo'] = htmlspecialchars_decode($town['baseinfo']);
            $villages = D('Village')->where(array('townid' => $id))->select();
            $this->assign("villages", $villages);
            $this->assign("town", $town);
            $this->display();
        }
    }

    public function village()
    {
        $id = I("get.id", 0);
        if ($id > 0) {
            $village = D('Village')->where(array('id' => $id))->find();
            $village['baseinfo'] = htmlspecialchars_decode($village['baseinfo']);
            $this->assign("village", $village);
//            print_r($village);die;
            $this->display();
        }
    }

    public function newsdetail()
    {
        $id = I("get.id", 0);
        if ($id > 0) {
            $news = D('News')->where(array('id' => $id))->find();
            $news['intro'] = htmlspecialchars_decode($news['intro']);
            $this->assign("news", $news);
            // print_r($news);die;
            $this->display();
        }
    }

	 public function equipments(){
        if(IS_POST){
            $key=I("post.key");
            $b=I("post.b");
            $b=$this->caesar->clientDecode($key,$b);
            $param=json_decode($b,true);
            $equipments=D("Equipment")->where(array('areaid'=>$param['type']))->select();
            $data['bstatus']['code']=0;
            $data['bstatus']['des']='获取成功';
            $data['data']['equimpents']=$equipments;
            echo  $this->caesar->clientEncode($key,json_encode($data));
        }
    }
	public function equipment(){
        if(IS_POST){
            $key=I("post.key");
            $b=I("post.b");
            $b=$this->caesar->clientDecode($key,$b);
            $param=json_decode($b,true);
            $equipment=D("Equipment")->where(array('id'=> $param['id']))->find();
						
			$param['method']='liveplay';
			$param['deviceid']=$equipment['deviceid'];
			$param['shareid']=$equipment['shareid'];
			$param['uk']=$equipment['uk'];
			$param['type']='rtmp';
			if($param['deviceid'] && $param['shareid'] && $param['uk']){
				$data=http("https://api.iermu.com/v2/pcs/device",$param);
				$data=json_decode($data,true);
				$equipment['rtmp']=$data['url'];
				$equipment['status']=$data['status'];
				if($equipment['status']==0){
					$data['bstatus']['code']=-2;
					$data['bstatus']['des']='设备已离线或取消分享';
				}else {
					$data['bstatus']['code']=0;
					$data['bstatus']['des']='获取成功';
					$data['data']=$equipment;	
				}				
			}else {
				$data['bstatus']['code']=-1;
				$data['bstatus']['des']='设备号为空,暂时无法播放';
			}
			//print_r($data);
            echo  $this->caesar->clientEncode($key,json_encode($data));
        }
    }

    /**
     * openinfo
     * 党支部管理的公开信息
     */
    /*  public function openinfo1(){
          $vilage_id=I("get.id",0);
          if($vilage_id>0){
              $openinfo=D('Openinfo')->where(array('villageid'=>$vilage_id))->select();
              $this->assign("openinfo",$openinfo);
              $this->display();
          }
      }*/
    public function openinfo()
    {
        $id = I("get.id", 0);
        if ($id > 0) {
            $param = array();
            array_push($param, $id);
            $sql = " select v.name as vname,o.* from qfant_village v  LEFT JOIN qfant_openinfo o ON o.villageid=v.id where  o.villageid=%d";
            $openinfo = D('Openinfo')->query($sql, $param);
            $this->assign("openinfo", $openinfo);
            $this->display();
        }

    }

    /**
     * lookOpeninfo
     * 查看党支部个人的公开信息
     */
    public function lookOpeninfo()
    {
        $id = I("get.id", 0);
        if ($id > 0) {
            $lookOpenInfo = D('Openinfo')->where(array('id' => $id))->find();
            $this->assign("lookOpenInfo", $lookOpenInfo);
            $this->display();
        }
    }

    public function groupindex()
    {
        $this->display();
    }

    public function standard()
    {
        $this->display();
    }

    public function grouplife()
    {
        $this->display();
    }

    public function photo()
    {
        $photo = D('Photo')->select();
        $this->assign("photo", $photo);
        $this->display();
    }
    public function uploadPic()
    {
		$key = I("get.key");
        $b = I("get.b");
		if($key && $b){
            $b = $this->caesar->clientDecode($key, $b);
            $param = json_decode($b,true);
			$userId=$param['cparam']['userId'];
			$memberId=$param['cparam']['token'];
			$path='./Upload/images/'.date('Ymd').'/';
			if (! file_exists ( $path )) {
				mkdir ( "$path", 0777, true );
			}
			$filename=$memberId.$userId.'.'.$param['ext'];
			$this->receiveStreamFile($path.$filename);
			
			$data['data']['url']=C("GLOBAL_PIC_URL").'/Upload/images/'.date('Ymd').'/'.$filename;
			$data['bstatus']['code']=0;
			$data['bstatus']['des']='获取成功';			
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
}
