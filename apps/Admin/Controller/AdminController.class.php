<?php
/**
 * ====================================
 * 管理员操作
 * ====================================
 * Author: Tommy
 * Date: 2015 2015/5/10 15:58
 * ====================================
 * File: AdminController.class.php
 * ====================================
 */
namespace Admin\Controller;

class AdminController extends CommonController {
    protected $tableName = 'Admin';
    protected $allowAction = array('info');

    public function information() {
        if(IS_POST) {
            $params = I('post.', '', 'trim');
            if(!empty($params['password'])) {
                $params['user_id'] = USER_ID;
                $result = $this->dbModel->modifyPassword($params);
                if($result) {
                    $this->success(L('SAVE') . L('SUCCESS'));
                }else {
                    $this->error($result);
                }
            }
            exit;
        }
        $info = $this->dbModel->getRow(USER_ID);
        $this->assign('info', $info);
        $this->display();
    }

    //编辑管理员状态
    public function lock() {
        $params = I('post.');
        if($params['item_id']) {
            $where = array(
                'user_id' => array('in', $params['item_id'])
            );
            $result = $this->dbModel->where($where)->setField('locked', (int)$params['locked']);
            if($result) {
                $this->success(L('EDIT').L('SUCCESS'));
            }else
                $this->error(L('EDIT').L('ERROR'));
        }
        $this->error(L('SELECT_NODE') . L('ADMIN'));
    }
}