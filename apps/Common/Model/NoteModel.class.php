<?php
/**
 * ====================================
 * 这里是说明
 * ====================================
 * Author: Administrator
 * Date: 2015/7/12 13:15
 * ====================================
 * File: ArticleModel.class.php
 * ====================================
 */

namespace Common\Model;


class NoteModel extends CommonModel {
    protected $_validate = array(
        array('note_name', 'require', '{%note_name_lost}'),
        array('content', 'require', '{%content_lost}'),

    );

    public function filter($params = array()) {
        $where = array();
        if ($params['keywords']) {
            $where['note_name'] = array('LIKE', "%{$params['keywords']}%");
        }
        return $this->where($where);
    }
}