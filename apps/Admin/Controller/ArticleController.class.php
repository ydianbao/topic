<?php
/**
 * ====================================
 * 管理
 * ====================================
 * Author: Hugo
 * Email: service@ydianbao.com
 * Date: 14-5-20 下午9:28
 * ====================================
 * Url: http://www.ydianbao.com
 * File: CategoryController.class.php
 * ====================================
 */
namespace Admin\Controller;

class ArticleController extends CommonController {
    protected $tableName = 'Article';

    public function form() {
        $id = intval(I('get.id'));
        if($id > 0) {
            $info = $this->dbModel->find($id);
            $this->assign('info', $info);
        }
        $this->display();
    }

    //编辑管理员状态
    public function lock() {
        $params = I('post.');
        if($params['item_id']) {
            $where = array(
                'id' => array('in', $params['item_id'])
            );
            $result = $this->dbModel->where($where)->setField('locked', (int)$params['locked']);
            if($result) {
                $this->success(L('EDIT').L('SUCCESS'));
            }else
                $this->error(L('EDIT').L('ERROR'));
        }
        $this->error(L('SELECT_NODE') . L('ARTICLE'));
    }
}