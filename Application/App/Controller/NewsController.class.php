<?php
namespace App\Controller;

use Common\Controller\AppBaseController;

/**
 * 认证控制器
 */
class NewsController extends AppBaseController
{
    private $caesar;

    public function _initialize()
    {
        parent::_initialize();
        Vendor('Caesar.Caesar');
        $this->caesar = new \Caesar();
    }

    /**
     * 新闻列表（政策宣传、党建业务）
     * 传参：pageNo  pageSize  type  keyword
     */
    public  function newsList(){
       if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $pageNo =$param['pageNo'];
                $pageSize = $param['pageSize'];
                $type = $param['type'];
               $keyword = $param['keyword'];
                $offset = ($pageNo - 1) * $pageSize;
                $sql = "select  id,title,createtime,headpic from qfant_news n where 1=1  ";
                $countsql="select COUNT(id) as total from qfant_news n  where 1=1";
                $param = array();
                if($type>0){
                    $sql.=" and n.type=%d";
                    $countsql.=" and n.type=%d";
                    array_push($param,$type);
                }
                if(!empty($keyword)){
                    $sql.=" and n.title  like '%s' ";
                    $countsql.=" and n.title  like '%s' ";
                    array_push($param,'%'.$keyword.'%');
                }
                $sql .= " order by id desc   limit %d,%d";
                array_push($param, $offset);
                array_push($param, $pageSize);
                $news = D('News')->query($sql, $param);
                $countdata=D('News')->query($countsql,$param);
                foreach ($news as $i=>$basevalue){
                    $news[$i]['intro']=htmlspecialchars_decode($basevalue['intro']);
                    $news[$i]['headpic']=C("GLOBAL_PIC_URL").$basevalue['headpic'];
                    $news[$i]['createtime'] =date('Y-m-d H:i',$basevalue['createtime']);
                    $news[$i]['detailurl'] ='http://sm.qfant.com/App/Index/newsdetail/id/'.$basevalue['id'];
                }
                $data['bstatus']['code'] = 0;
                $data['bstatus']['des'] = '获取成功';
                $data['data']['newsList'] = $news;
                $condition['type']=array('eq',$type);
                $data['data']['totalNum'] =  $countdata[0]['total'];
               echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }

    }

    /**
     * 新闻详情
     * 传参 id
     */
    public  function newsDetail(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $newsdetial = D("News")->field('title,intro,createtime,headpic')->where(array('id' => $param['id']))->find();
				$newsdetial['createtime'] =date('Y-m-d H:i',$newsdetial['createtime']);
				$newsdetial['intro'] =htmlspecialchars_decode($newsdetial['intro']);
                $data['bstatus']['code'] = 0;
                $data['bstatus']['des'] = '获取成功';
                $data['data']['newsdetialResult'] = $newsdetial;
                echo $this->caesar->clientEncode($key, json_encode($data));

           }
        }
    }

    /**
     * 工作动态
     */
    public  function worknewsList(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $keyword =$param['keyword'];
                    $countsql="select COUNT(id) as total from qfant_worknews n  where 1=1";
                    $sql = "select id ,title ,createtime,intro from qfant_worknews n   where 1=1";
                    $param = array();
                    if(!empty($keyword)){
                        $sql.=" and n.title  like '%s' ";
                        $countsql.=" and n.title  like '%s' ";
                        array_push($param,'%'.$keyword.'%');
                    }
                    $sql.=" order by id desc  ";
                    $countdata=D('Worknews')->query($countsql,$param);
                    $worknews = D('Worknews')->query($sql,$param);
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                    foreach ($worknews as $i=>$basevalue){
                        $worknews[$i]['intro']=htmlspecialchars_decode($basevalue['intro']);
                        $worknews[$i]['createtime'] =date('Y-m-d H:i',$basevalue['createtime']);
						$worknews[$i]['detailurl'] ='http://sm.qfant.com/App/Index/newsdetail/id/'.$basevalue['id'];
                    }
                    $data['data']['workNewsList'] = $worknews;
                    $data['data']['totalNum'] =  $countdata[0]['total'];
               }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));
           }
        }

    }

    /**
     * 工作动态详情
     * 传参 id
     */
    public  function worknewsDetail(){
        if (IS_POST) {
            $key = I("post.key");
            $b = I("post.b");
            if ($key && $b) {
                $b = $this->caesar->clientDecode($key, $b);
                $param = json_decode($b, true);
                $login = $this->checkIsLonginUser($param['cparam']['userId'], $param['cparam']['token']);
                if($login) {
                    $worknewsdetial = D("Worknews")->field('id,title,intro,createtime')->where(array('id' => $param['id']))->find();
                    foreach ($worknewsdetial as $i=>$basevalue){
                        $worknewsdetial[$i]['intro']=htmlspecialchars_decode($basevalue['intro']);
                        $worknewsdetial[$i]['createtime'] =date('Y-m-d H:i',$basevalue['createtime']);
                    }
                    $data['bstatus']['code'] = 0;
                    $data['bstatus']['des'] = '获取成功';
                    $data['data']['worknewsdetialResult'] = $worknewsdetial;
                }else{
                    $data['bstatus']['code'] = C('APP_STATUS.STATUS_CODE_NOT_LOGIN');
                    $data['bstatus']['des'] = '登录失效，请重新登录';
                }
                echo $this->caesar->clientEncode($key, json_encode($data));

            }
        }
    }
}
