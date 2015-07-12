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


class ArticleModel extends CommonModel {
    protected $_validate = array(
        array('title', 'require', '{%text_lost}'),
        array('cat_id', 'require', '{%cat_id_lost}'),

    );

    public function filter($params = array()) {
        $this->alias(" AS a")
            ->join('__ARTICLE_CAT__ as ac ON ac.id = a.cat_id', 'LEFT')
            ->field('a.*,ac.text as cat_name')
            ->order('a.create_time DESC');

        $where = array();
        if ($params['keywords']) {
            $where['a.title'] = array('LIKE', "%{$params['keywords']}%");
        }
        if ($params['cat_id']) {
            $where['a.cat_id'] = (int)$params['cat_id'];
        }
        return $this->where($where);
    }
}