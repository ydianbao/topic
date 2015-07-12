<?php
namespace Common\Model;
use Common\Library\Common;

class GroupModel extends CommonModel {
    protected $_validate = array(
        array('text','require','{%GROUP_NAME_EMPTY}'),
        array('text','','{%GROUP_NAME_EXISTS}', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('pid', 'validateEq', '{%PARENT_ERROR}', self::EXISTS_VALIDATE, 'callback', self::MODEL_UPDATE)
    );

    /**
     * 判断父级ID正确性
     * @param $pid
     * @param $id
     * @return bool
     */
    protected function validateEq($pid){
        return $pid != I('post.id');
    }

    public function grid($params) {
        if($params['keyword']) {
            $this->where(array(
                'text' => array('LIKE', "%{$params['keyword']}%")
            ));
        }
        $data = $this
            ->field('*,id as group_id')
            ->order("pid ASC, orderby DESC")
            ->getAll();
        Common::tree($data, $params['selected'], $params['type']);
        return $data;
    }

}