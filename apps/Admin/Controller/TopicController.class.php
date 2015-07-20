<?php
/**
 * ====================================
 * 题库管理
 * ====================================
 * Author: Administrator
 * Date: 2015/7/12 9:47
 * ====================================
 * File: TopicController.class.php
 * ====================================
 */

namespace Admin\Controller;


class TopicController extends CommonController {
    protected $tableName = 'Topic';

    public function save() {
        $params = I('post.');
        if($params['answer_config']) {
            $params['answer_config'] = json_encode($params['answer_config']);
        }
        if(!isset($params['is_free'])) {
            $params['is_free'] = 0;
        }
        if($params = $this->dbModel->create($params)) {
            if($params['topic_id']) {
                $result = $this->dbModel->save($params);
            }else {
                $result = $this->dbModel->add($params);
            }
            if($result) {
                $this->success(L('SAVE') . L('SUCCESS'));
            }
        }
        $this->error(L('SAVE') . L('ERROR'));
    }

    public function form() {
        $id = intval(I('get.id'));
        if($id > 0) {
            $info = $this->dbModel->find($id);
            $info['answer_config'] = json_decode($info['answer_config'], true);
        }else {
            //查询排序最大值
            $orderby = $this->dbModel->max('orderby');
            $info['orderby'] = $orderby + 1;
        }
        $this->assign('info', $info);
        $this->display();
    }
}