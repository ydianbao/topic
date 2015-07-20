<?php
/**
 * ====================================
 * 专题模型
 * ====================================
 * Author: Administrator
 * Date: 2015/7/12 13:15
 * ====================================
 * File: PageModel.class.php
 * ====================================
 */

namespace Common\Model;


class PageModel extends CommonModel {
    protected $_validate = array(
        array('page_title', 'require', '{%text_lost}'),
        array('template','','{%template_EXISTS}', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),

    );

    public function filter($params = array()) {
        $where = array();
        if ($params['keywords']) {
            $where['title'] = array('LIKE', "%{$params['keywords']}%");
        }
        return $this->where($where);
    }
}