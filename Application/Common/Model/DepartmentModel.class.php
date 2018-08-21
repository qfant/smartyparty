<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 权限规则model
 */
class DepartmentModel extends BaseModel{

    protected $_auto=array(
        array('createtime','time',1,'function'), // 对date字段在新增的时候写入当前时间戳
    );

    public function getTreeDatajax($type='tree',$order='',$alias=''){
        $pid= $type[pid];
        // 判断是否需要排序
        if(empty($order)) {
            $data = $this->select();
        }else{
            $data=$this->order($order)->select();
        }
        if(!empty($alias)){
            foreach($data as $k=>$val){
                $data[$k][$alias]=$val['name'];
            }
        }
        foreach($data as $k=>$val){
            $data[$k]['name']=htmlspecialchars_decode($val['name']);
            if($val['pid'] == 0){
                $data[$k]['state'] = 'open';
            }else {
                $data[$k]['state'] = 'open';
            }

        }
        // 获取树形或者结构数据
        $data=\Org\Nx\Data::tree2($data,0);
        return $data;
    }
    public function getDepartmentList ($id=0){
        $first=$this->where(array('pid'=>$id))->field('id')->select();
        $ids=array();
        $ids[]=$id;
        $idstemp=array();
        foreach($first as $k=>$val){
            $ids[]=$val['id'];
            $second=$this->where(array('pid'=>$val['id']))->field('id')->select();
            foreach($second as $key=>$val2){
                $idstemp[]=$val2['id'];
            }
        }
        return implode(',',$ids).','.implode(',',$idstemp);
    }

    public function getTreeData($type='tree',$order='',$alias='text'){
        $pid= $type[pid];
        // 判断是否需要排序
        if(empty($order)) {
            $data = $this->field("id,name,pid")->select();
        }else{
            $data=$this->order($order)->select();
        }
        if(!empty($alias)){
            foreach($data as $k=>$val){
                $data[$k][$alias]=$val['name'];
            }
        }
        foreach($data as $k=>$val){
            $data[$k]['name']=htmlspecialchars_decode($val['name']);

        }
        // 获取树形或者结构数据
        $data=\Org\Nx\Data::tree3($data,0);
        return $data;
    }
}
