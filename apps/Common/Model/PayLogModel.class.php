<?php
/**
 * ====================================
 * 消费日志模型
 * ====================================
 * Author: Administrator
 * Date: 2015/7/12 13:15
 * ====================================
 * File: PageModel.class.php
 * ====================================
 */

namespace Common\Model;


class PayLogModel extends CommonModel {

    public function filter($params = array()) {
        $where = array();
        if ($params['user_id']) {
            $where['user_id'] = $params['user_id'];
        }
        return $this->where($where);
    }
}