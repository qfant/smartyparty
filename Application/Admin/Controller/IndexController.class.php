<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
use \Think\Upload;
/**
 * 后台首页控制器
 */
class IndexController extends AdminBaseController{
	/**
	 * 首页
	 */
	public function index(){
		// 分配菜单数据
		$nav_data=D('AdminNav')->getTreeData('level','order_number desc');
		$assign=array(
			'data'=>$nav_data
		);
		$this->assign($assign);
		$this->display();
	}
	/**
	 * elements
	 */
	public function elements(){

		$this->display();
	}
	
	/**
	 * welcome
	 */
	public function welcome(){
	    $this->display();
	}
    public function map(){
        $this->display();
    }

    public function upLoadFile(){
        $error = "";
        $msg = "";
        $fileElementName = 'upimage';
        $result["status"] = 1;
        if(!empty($_FILES[$fileElementName]['error'])){
            $result["status"] = 0;
            switch($_FILES[$fileElementName]['error']){
                case '1':
                    $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'The uploaded file was only partially uploaded';
                    break;
                case '4':
                    $error = 'No file was uploaded.';
                    break;

                case '6':
                    $error = 'Missing a temporary folder';
                    break;
                case '7':
                    $error = 'Failed to write file to disk';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = 'No error code avaiable';
            }
        }elseif(empty($_FILES['upimage']['tmp_name']) || $_FILES['upimage']['tmp_name'] == 'none'){
            $error = 'No file was uploaded.';
            $result["status"] = 0;
        }else{
            $re = $this->up();
            if($re['status']==-1){
                $result["error"] =$re['error'];
                $result["status"] = 0;
            }else{
                $result["error"] = $error;
                $result["result"] = '/Upload/image/'.date("Ymd").'/'.$re['savename'];
                $result["size"] = $re['size'];
                $result["savename"] = $re['savename'];    //文件名
                $result["status"] = 1;
            }
        }
        echo json_encode($result);exit;
    }

    private function up(){
        //import('@.Org.UploadFile');//将上传类UploadFile.class.php拷到Lib/Org文件夹下
        $upload = new Upload();

        $upload->maxSize   = 3145728 ;// 设置附件上传大小
        $upload->rootPath  = './Upload/image/';//保存路径
//        $upload->savePath= '/image/';//保存路径
        $upload->saveRule=uniqid;//上传文件的文件名保存规则
        $upload->uploadReplace=true;//如果存在同名文件是否进行覆盖
        $upload->autoSub=true;//自动使用子目录保存上传文件 默认为true
        $upload->subName=array('date','Ymd');//自动使用子目录保存上传文件 默认为true
        $upload->allowExts=array('jpg','jpeg','png','gif');//准许上传的文件类型
        $info=$upload->upload();
        if($info){
            return $info['upimage'];
        }else{
            $result['status']=-1;
            $result['error']=$upload->getError();
            return $result;
        }
    }
}
