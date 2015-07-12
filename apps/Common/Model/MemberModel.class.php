<?php
/**
 * ====================================
 * 会员管理
 * ====================================
 * Author: Administrator
 * Date: 2015/7/12 9:27
 * ====================================
 * File: MemberModel.class.php
 * ====================================
 */

namespace Common\Model;


class MemberModel extends CommonModel {
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
        array('password', 'password', self::MODEL_BOTH, 'function'),
    );

    protected $_validate = array(
        array('email','require','{%USER_NAME_REQUIRE}'),
        array('email','','{%USER_NAME_EXISTS}', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('confirm_password','password','{%CONFIRM_PASSWORD_ERROR}', self::EXISTS_VALIDATE, 'confirm'), // 验证确认密码是否和密码一致
        array('password', '6,32', '{%PASSWORD_ERROR}', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
    );

    public function modifyPassword($params) {
        $_validate = array(
            array('confirm_password','password','{%CONFIRM_PASSWORD_ERROR}', self::MUST_VALIDATE, 'confirm'),
            array('password', '6,32', '{%PASSWORD_ERROR}', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        );
        $this->validate($_validate);
        if($this->create($params)) {
            return $this->save();
        }
        return $this->getError();
    }

    /**
     * 获取列表
     * @param array $params
     * @return mixed
     */
    public function filter($params) {
        $where = array();
        if($params['keyword']) {
            $where['email'] = array('LIKE', "%{$params['keyword']}%");
        }
        return $this->where($where);
    }
}