<?php

namespace Admin\Controller;
class PageController extends CommonController {
	protected $tableName = 'Page';

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
                'page_id' => array('in', $params['item_id'])
            );
            $result = $this->dbModel->where($where)->setField('locked', (int)$params['locked']);
            if($result) {
                $this->success(L('EDIT').L('SUCCESS'));
            }else
                $this->error(L('EDIT').L('ERROR'));
        }
        $this->error(L('SELECT_NODE') . L('PAGE'));
    }
}