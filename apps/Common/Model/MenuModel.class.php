<?php
/**
 * ====================================
 * 菜单模型
 * ====================================
 * Author: Hugo
 * Date: 14-5-20 下午9:28
 * ====================================
 * File: MenuModel.class.php
 * ====================================
 */
namespace Common\Model;
use Common\Library\Common;

class MenuModel extends CommonModel{
    protected $_validate = array(
        array('text','require','{%menu_name_lost}'),
        array('controller','require','{%class_name_lost}'),
        array('method','require','{%method_name_lost}'),
        array('pid', 'legal', '{%PARENT_ERROR}', self::EXISTS_VALIDATE, 'callback', self::MODEL_UPDATE)
    );

    /**
     * 判断父级ID正确性
     * @param $pid
     * @param $id
     * @return bool
     */
    protected function legal($pid){
        return $pid != I('post.id');
    }

    public function filter($params) {
        $where = array();
        if($params['text']) {
            $where['text'] = array('like', "%{$params['text']}%");
        }

        is_null($params['pid']) || $where['pid'] = $params['pid'];
        is_null($params['display']) || $where['display'] = $params['display'];
        empty($params['id']) || $where['id'] = array('in', $params['id']);

        $this->where($where);
        return $this;
    }

    /**
     * 读取树型数据
     * @return mixed
     */
    public function grid($params = array()) {

        $data = $this->field("*, id AS tree_id")->order("pid ASC, orderby DESC")->getAll();

        if($data){
            foreach($data as $key => $row){
                $action = empty($row['method']) || $row['method'] == '*' ? C('DEFAULT_ACTION') : $row['method'];
                $row['href'] = U($row['controller'] . '/' . $action);
                $row['power'] = "power({$row['controller']}-{$row['method']})";
                $data[$key] = $row;
            }
            Common::tree($data, $params['selected'], $params['type']);
        }
        return $data ? $data : array();
    }
}