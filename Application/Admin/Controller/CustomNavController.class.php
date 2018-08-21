<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;

/**
 * 后台菜单管理
 */
class CustomNavController extends AdminBaseController
{
    /**
     * 菜单列表
     */
    public function index()
    {
        $data = D('CustomNav')->getTreeData('tree', 'order_number,id');
        $assign = array(
            'data' => $data
        );
        $this->assign($assign);
        $this->display();
    }

    public function Menus()
    {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $result["total"] = D('CustomNav')->where(array('pid' => 0))->count();

        $data = D('CustomNav')->where(array('pid' => 0))->limit($offset . ',' . $rows)->select();
        foreach ($data as $key => $value) {
            $children = D('CustomNav')->where(array('pid' => $value['id']))->select();
            $data[$key]['children'] = $children;
        }
        $result["rows"] = $data;
        $this->ajaxReturn($result, 'JSON');
    }

    public function menuLevel()
    {
        $pid = I('get.pid');
        $data = D('CustomNav')->field('id,name')->where(array('pid' => $pid))->select();
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 添加菜单
     */
    public function add()
    {
        $data = I('post.');
        unset($data['id']);
        $result = D('CustomNav')->addData($data);
        if ($result) {
            $message['status'] = 1;
            $message['message'] = '添加菜单成功';
        } else {
            $message['status'] = 0;
            $message['message'] = '添加菜单失败';
        }
        $this->ajaxReturn($message, 'JSON');
    }

    /**
     * 修改菜单
     */
    public function edit()
    {
        $data = I('post.');
        $map = array(
            'id' => $data['id']
        );
        $id = I('post.id');
        $kind = I('post.kind');
        $dataKind = D('CustomNav')->field('kind')->where(array('id' => $id))->select();
        if ($kind != $dataKind[0]) {
            if ($kind == 1 || $kind == 2) {
                $data['title'] = "";
                $data['description'] = "";
                $data['linkurl'] = "";
                $data['upload'] = "";
            } else if ($kind == 3) {
                $data['kindcontent'] = "";
            } else {
                $data['title'] = "";
                $data['description'] = "";
                $data['linkurl'] = "";
                $data['upload'] = "";
                $data['kindcontent'] = "";
            }
        }
        $result = D('CustomNav')->editData($map, $data);
        if ($result) {
            $message['status'] = 1;
            $message['message'] = '修改成功';
        } else {
            $message['status'] = 0;
            $message['message'] = '修改失败';
        }
        $this->ajaxReturn($message, 'JSON');
    }

    /**
     * 删除菜单
     */
    public function delete()
    {
        $id = I('get.id');
        $map = array(
            'id' => $id
        );
        $result = D('CustomNav')->deleteData($map);
        if ($result) {
            $message['status'] = 1;
            $message['message'] = '删除菜单成功';
        } else {
            $message['status'] = 0;
            $message['message'] = '删除菜单失败';
        }
        $this->ajaxReturn($message, 'JSON');
    }

    /**
     * 菜单排序
     */
    public function order()
    {
        $data = I('post.');
        D('CustomNav')->orderData($data);
        $this->success('排序成功', U('Admin/Nav/index'));
    }


}
