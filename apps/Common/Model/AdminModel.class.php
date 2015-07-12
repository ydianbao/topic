<?php
/**
 * ====================================
 * 这里是说明
 * ====================================
 * Author: Tommy
 * Date: 2015 2015/4/12 21:03
 * ====================================
 * File: AdminModel.class.php
 * ====================================
 */

namespace Common\Model;


class AdminModel extends CommonModel {
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
        array('password', 'password', self::MODEL_BOTH, 'function'),
    );

    protected $_validate = array(
        array('user_name','require','{%USER_NAME_REQUIRE}'),
        array('real_name','require','{%REAL_NAME_REQUIRE}'),
        array('user_name','','{%USER_NAME_EXISTS}', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
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
        if($params['keyword']){
            $where['a.user_name'] = array('LIKE', "%{$params['keyword']}%");
            $where['a.real_name'] = array('LIKE', "%{$params['keyword']}%");
            $where['_logic'] = 'OR';
        }

        if($params['role_id']) {
            $where['a.role_id'] = intval($params['role_id']);
        }

        if($params['group_id']) {
            $where['a.group_id'] = intval($params['group_id']);
        }

        $this->alias(' AS a')
            ->join("__GROUP__ AS g ON a.group_id = g.id", 'left')
            ->join("__ROLE__ AS r ON a.role_id = r.id", 'left')
            ->field('a.user_id, a.user_name, a.real_name, a.sex, a.locked, a.group_id, a.role_id, a.create_time, a.update_time, a.is_open, g.text as group_name, r.text as role_name');

        return $this->where($where);
    }

    /**
     * 记录日志
     * @param $message
     * @return bool
     */
    public function addLog($message, $user_id = USER_ID) {
        $this->table('__ADMIN_LOG__');
        $this->data(array(
            'user_id' => $user_id,
            'module_name' => MODULE_NAME,
            'controller_name' => CONTROLLER_NAME,
            'action_name' => ACTION_NAME,
            'note' => $message,
            'create_time' => time()
        ));
        $result = $this->add();
        return $result ? true : false;
    }

    public function getMenu($role_id) {
        //读取管理员权限菜单
        $menu_id = $this->table('__ROLE__')->where("id = '%d'", $role_id)->getField('menu_id');
        if(empty($menu_id)) return null;

        $data = $this->table('__MENU__')->field('id, controller, method')->where("id IN ({$menu_id})")->select();
        $menu = array();
        foreach($data as $row){
            $menu[$row['id']] = $row['controller'] . '-' . $row['method'];
        }
        return $menu;
    }

    /**
     * 获取个人信息
     * @param $user_id
     * @return mixed
     */
    public function getRow($user_id) {
        $this->alias(' AS a');
        $this->join('__ROLE__ AS r ON a.role_id = r.id', 'LEFT');
        $this->join('__GROUP__ AS g ON a.group_id = g.id', 'LEFT');
        $this->field('a.*, r.text as role_name, g.text as group_name');
        $this->where(array(
            'a.user_id' => $user_id
        ));
        return $this->find();
    }
}