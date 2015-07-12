<?php
/**
 * 管理平台首页
 * User: 91336
 * Date: 2015/3/24
 * Time: 10:23
 */
namespace Admin\Controller;

use Common\Library\Tree;
use Common\Model\MenuModel;

class IndexController extends CommonController {
    protected $allowAction = array('*');

    public function index() {
        layout(false);
        $this->display();
    }

    public function menu() {
        $where = array('display' => 1);
        if(!login('is_open')) {
            if(login('menu')) {
                $where['id'] = array('IN', array_keys(login('menu')));
            }else {
                $where['id'] = 0;
            }
        }
        $menuModel = new MenuModel();
        $menu_list = $menuModel->field('id,pid,text,controller,method,icon')->where($where)->select();
        if($menu_list) {
            foreach($menu_list as $key => $row) {
                $action = empty($row['method']) || $row['method'] == '*' ? C('DEFAULT_ACTION') : $row['method'];
                $row['href'] = U($row['controller'] . '/' . $action);
                $menu_list[$key] = $row;
            }
            $menu_list = Tree::treeArray($menu_list);
        }
        $this->ajaxReturn($menu_list);
    }
}