<?php
/**
 * ====================================
 * 这里是说明
 * ====================================
 * Author: Administrator
 * Date: 2015/7/12 20:57
 * ====================================
 * File: AdModel.class.php
 * ====================================
 */

namespace Common\Model;


class AdModel extends CommonModel {
    protected $_validate = array(
        array('ad_title', 'require', '{%ad_title_lost}'),
    );

    public function filter($params = array()) {
        $where = array();
        if ($params['keywords']) {
            $where['ad_title'] = array('LIKE', "%{$params['keywords']}%");
        }
        return $this->where($where);
    }

    public function format($data) {
        foreach($data['rows'] as $key => $row) {
            $row['func'] = "ad({$row['ad_id']})";
            $data['rows'][$key] = $row;
        }
        return $data;
    }
}