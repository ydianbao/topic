<?php
/**
 * ====================================
 * 题库管理
 * ====================================
 * Author: Administrator
 * Date: 2015/7/12 15:29
 * ====================================
 * File: TopicModel.class.php
 * ====================================
 */

namespace Common\Model;


class TopicModel extends CommonModel {
    protected $_validate = array(
        array('topic_title', 'require', '{%topic_title_lost}'),
        array('orderby', 'require', '{%orderby_lost}'),
        array('orderby','','{%orderby_repeat}', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    public function filter($params = array()) {
        $where = array();
        if ($params['keywords']) {
            $where['topic_title'] = array('LIKE', "%{$params['keywords']}%");
        }
        if($params['sort']) {
            $this->order($params['sort'] . ' ' . $params['order']);
        }else {
            $this->order('orderby asc');
        }
        
        return $this->where($where);
    }
}