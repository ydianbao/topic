<?php
/**
 * 后台公共类
 * User: 91336
 * Date: 2015/3/24
 * Time: 14:52
 */
namespace Admin\Controller;
use Common\Controller\PublicController;
use Common\Library\Common;

abstract class CommonController extends PublicController {
    protected $dbModel;
    protected $tableName;
    protected $template;

    public function __construct() {
        parent::__construct();
        $this->tableName and $this->dbModel = D(ucfirst($this->tableName));

        if($user_id = login('user_id')) {
            if(false == $this->access()) $this->error(L('NOT_ACCESS'));
            define('USER_ID', $user_id);
            define('USER_NAME', login('user_name'));
        }else {
            $jumpUrl = U('passport/index');
            'Index' == CONTROLLER_NAME ? redirect($jumpUrl) : $this->error(L('LOGIN_AGAIN'), $jumpUrl);
        }
    }

    /**
     * 验证操作权限
     * 设计思路:
     * 1、每个控制器中都可以设置不需要验证的操作;
     * 2、带get的方法不需要验证
     * 3、如果当前菜单的ID在管理员所拥有的菜单ID中则验证通过;
     * @return bool
     */
    final protected function access($action = ACTION_NAME) {
        //如果是超级管理员不需要验证
        if(login('is_open')) return true;

        $action = strtolower($action);
        $controller = strtolower(CONTROLLER_NAME);

        //如果在允许访问列表内，不需要验证权限
        if(!empty($this->allowAction) && ($this->allowAction == '*' || in_array($action, $this->allowAction))) return true;

        //如果未分配菜单，则不允许访问
        if(is_null(login('menu'))) return false;

        //验证当前操作是否有权限
        return power(array($controller.'-'.$action, $controller.'-*'));
    }

    /**
     * 查列表
     */
    public function index() {
        if(IS_AJAX) {
            $params = I('request.');
            //先判断Model层是否存在
            if(method_exists($this->dbModel, 'grid')) {
                if(method_exists($this->dbModel, 'filter')){
                    $this->dbModel->filter($params);
                }
                $data = $this->dbModel->grid($params);
                if(method_exists($this->dbModel, 'format')) {
                    $data = $this->dbModel->format($data);
                }
            }
            //判断公共方法grid是否存在
            elseif(method_exists($this, 'grid')) {
                $data = $this->grid($params);
            }
            //默认输出树结构
            else {
                if($params) {
                    $fields = $this->dbModel->getDbFields();
                    $where = array();
                    foreach($params as $key => $val) {
                        if(in_array($key, $fields)) {
                            if(is_numeric($val)) {
                                $where[$key] = $val;
                            }else{
                                $where[$key] = array('like', "%$val%");
                            }
                        }
                    }
                    $this->dbModel->where($where);
                }
                $data = $this->dbModel->getAll();
                Common::tree($data, $params['selected'], $params['type']);
            }
            $this->ajaxReturn($data);
            exit;
        }
        $this->display($this->template);
    }

    /**
     * 添加或更改记录
     */
    public function save() {
        $params = I('post.');
        $data = $this->dbModel->create($params) or $this->error($this->dbModel->getError());
        $pk = $this->dbModel->getPk();
        if($params[$pk]) {
            $result = $this->dbModel->save();
        }else {
            $result = $this->dbModel->add();
            $params[$pk] = $this->dbModel->getLastInsID();
        }
        if($result) {
            //调用保存后需要处理的方法
            if(method_exists($this, '_after_save')) {
                $this->_after_save($params);
            }
            $this->success(L('SAVE') . L('SUCCESS'));
        }else {
            $this->error(L('SAVE') . L('ERROR'));
        }
    }

    /**
     * 删除记录
     * @param $itemid
     */
    public function delete() {
        $item_id = I('request.item_id');
        $item_id or $this->error(L('item_lost'));
        $where = array();
        $pk = $this->dbModel->getPk();
        if(strpos($item_id, ',') !== false) {
            $where[$pk] = array('IN', $item_id);
        }else {
            $where[$pk] = $item_id;
        }
        if($this->dbModel->where($where)->delete()) {
            if(method_exists($this, '_after_delete')) {
                $this->_after_delete($item_id);
            }
            $this->success(L('DELETE') . L('SUCCESS'));
        }else {
            $this->error(L('DELETE') . L('ERROR'));
        }
    }
}