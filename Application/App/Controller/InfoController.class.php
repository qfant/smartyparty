<?php
namespace App\Controller;

use Common\Controller\AppBaseController;

/**
 * 认证控制器
 */
class InfoController extends AppBaseController
{
    private $caesar;

    public function _initialize()
    {
        parent::_initialize();
        Vendor('Caesar.Caesar');
        $this->caesar = new \Caesar();
    }

    public  function infoList(){
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
                $sql = "select  id,name from qfant_info n where 1=1  and type=".$type.' and managerid='.$managerId;
                $countsql="select COUNT(id) as total from qfant_info n  where 1=1 and type=".$type.' and managerid='.$managerId;
                $param = array();
                if(!empty($name)){
                    $sql.=" and n.name  like '%s' ";
                    $countsql.=" and n.name  like '%s' ";
                    array_push($param,'%'.$name.'%');
                }
                $sql .= " order by id desc   limit %d,%d";
                array_push($param, $offset);
                array_push($param, $pageSize);
                $list = D('Info')->query($sql, $param);
                $countdata=D('Info')->query($countsql,$param);

                $data['bstatus']['code'] = 0;
                $data['bstatus']['des'] = '获取成功';
                $data['data']['infoList'] = $list;
                $data['data']['totalNum'] =  $countdata[0]['total'];
                echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }

    }

    /**
     * 信息平台列表
     */
    public function  InfoList2(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $pageNo = $param['pageNo'];
                $pageSize = $param['pageSize'];
                $offset = ($pageNo - 1) * $pageSize;
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $data['data']['totalNum'] =  D('Info')->count();
                    $sql = "select * from  qfant_info where 1=1";
                    $param = array();
                    $sql .= " limit %d,%d";
                    array_push($param, $offset);
                    array_push($param, $pageSize);
                    $infoList = D('Info')->query($sql, $param);
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';

                    $data['data']['infoListResult'] = $infoList;

                }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));
            }
        }
    }

    /**
     * 信息平台详情
     * 传参 id
     */
    public  function InfoDetail(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $infodetial = D("Info")->where(array('id' => $param['id']))->find();
                    $quarter="";
                    $quarter[]=$quarter1=array("key"=>"单位名称","name"=>$infodetial['name']);
                    $quarter[]=$quarter2=array("key"=>"单位地址","name"=>$infodetial['address']);
                    $quarter[]=$quarter3=array("key"=>"所在地区","name"=>$infodetial['area']);
                    $quarter[]=$quarter4=array("key"=>"出资人（负责人）姓名","name"=>$infodetial['promot1']);

                    $quarter[]=$quarter5=array("key"=>"从业人数","name"=>$infodetial['employnumber']);
                    $quarter[]=$quarter6=array("key"=>"是否规模以上企业","name"=>$infodetial['scale']);
                    $quarter[]=$quarter7=array("key"=>"党员人数","name"=>$infodetial['membernum']);
                    $quarter[]=$quarter8=array("key"=>"是否成立党组织","name"=>$infodetial['isparty']);

                    $quarter[]=$quarter9=array("key"=>"党组织成立时间","name"=>$infodetial['partytime']);
                    $quarter[]=$quarter10=array("key"=>"党组织设置形式","name"=>$infodetial['partyshape']);
                    $quarter[]=$quarter11=array("key"=>"党组织组建方式","name"=>$infodetial['orgmethod']);
                    $quarter[]=$quarter12=array("key"=>"党组织书记姓名","name"=>$infodetial['clerkname']);
                    $quarter[]=$quarter13=array("key"=>"标准化建设是否达标","name"=>$infodetial['phone']);
                    $quarter[]=$quarter14=array("key"=>"党组织书记号码","name"=>$infodetial['isstardand']);

                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                    $data['data']['infoDetialResult'] = $quarter;

               }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }
    }
    public  function  useHelp(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                    $useHelp="第一步，注册并登录百度站长平台
                              第二步，提交PC网站并验证站点与ID的归属关系，具体验证网站归属方法可见帮助文档
                              第三步，站点验证后，进入“工具”――“移动专区”――“移动适配工具”，选择具体需要进行移动适配的PC站，然后“添加适配关系”
                              第四步，根据自己提交的适配数据特点，选择适合您的提交方式：";
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                    $data['data']['useHelpResult'] = $useHelp;
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
                    if ($managerid==1){
                        $feigong= D("Info")->field('id,name')->where(array('type' =>1))->count();//非公企业数量
                        $zuzhi= D("Info")->field('id,name')->where(array('type' =>2))->count();//社会组织数量

                        $feigongguimo= D("Info")->field('id,name')->where(array('type' =>1,'orgmethod' =>0))->count();//单单独组建独组建
                        $zuzhiguimo= D("Info")->field('id,name')->where(array('type' =>2,'orgmethod' =>0))->count();//
                    }else {
                        $feigong= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid))->count();//非公企业数量
                        $zuzhi= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid))->count();//社会组织数量

                        $feigongguimo= D("Info")->field('id,name')->where(array('type' =>1,'orgmethod' =>0,'managerid'=>$managerid))->count();//单单独组建独组建
                        $zuzhiguimo= D("Info")->field('id,name')->where(array('type' =>2,'orgmethod' =>0,'managerid'=>$managerid))->count();//
                    }

                    $datasum[]=$feigong;
                    $datasum[]=$zuzhi;

                    $dataguimo[]=$feigongguimo;
                    $dataguimo[]=$zuzhiguimo;

                    $bar['sum']=$datasum;
                    $bar['sumguimo']=$dataguimo;

                    if ($managerid==1){
                        $feigong= D("Info")->field('id,name')->where(array('type' =>1,'isparty'=>1))->count();//单独党支部组建率
                        $feigongAll= D("Info")->field('id,name')->where(array('type' =>1))->count();//单独党支部组建率
                        $zuzhi= D("Info")->field('id,name')->where(array('type' =>2,'isparty'=>1))->count();//社会组织数量
                        $zuzhiAll= D("Info")->field('id,name')->where(array('type' =>2))->count();//社会组织数量
                    }else {
                        $feigong= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid,'isparty'=>1))->count();//单独党支部组建率
                        $feigongAll= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid))->count();//单独党支部组建率
                        $zuzhi= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid,'isparty'=>1))->count();//社会组织数量
                        $zuzhiAll= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid))->count();//社会组织数量
                    }

                    if($feigongAll>0){
                        $feigong=intval(($feigong/$feigongAll)*100);
                    }
                     if($zuzhiAll>0){
                         $zuzhi=intval(($zuzhi/$zuzhiAll)*100);
                     }
                    $dataisparty[]=$feigong;
                    $dataisparty[]=$zuzhi;
                    if ($managerid==1){
                        $feigongstd= D("Info")->field('id,name')->where(array('type' =>1,'isstardand'=>1))->count();//标准化达标率
                        $feigongstdAll= D("Info")->field('id,name')->where(array('type' =>1))->count();//
                        $zuzhistd= D("Info")->field('id,name')->where(array('type' =>2,'isstardand'=>1))->count();//
                        $zuzhistdAll= D("Info")->field('id,name')->where(array('type' =>2))->count();//
                    }else {
                        $feigongstd= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid,'isstardand'=>1))->count();//标准化达标率
                        $feigongstdAll= D("Info")->field('id,name')->where(array('type' =>1,'managerid'=>$managerid))->count();//
                        $zuzhistd= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid,'isstardand'=>1))->count();//
                        $zuzhistdAll= D("Info")->field('id,name')->where(array('type' =>2,'managerid'=>$managerid))->count();//
                    }

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

