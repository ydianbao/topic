<?php
/**
 * ====================================
 * 公共数据模型类
 * ====================================
 * Author: Hugo
 * Date: 14-5-20 下午9:58
 * ====================================
 * File: CommonModel.class.php
 * ====================================
 */

namespace Common\Model;
use Think\Model;

abstract class CommonModel extends Model {
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );
    /**
     * easyui分页处理
     * @param array $where
     * @param string $order_by
     * @param string $join
     * @param string $alias
     * @param int $pagesize
     * @return array
     */
    public function grid($params = array()) {
        $orderBy = isset($params['sort']) ? trim($params['sort']) . ' ' .  trim($params['order']) : '';
        $page = isset($params['page']) && $params['page'] > 0 ? intval($params['page']) : 1;
        $pageSize = isset($params['rows']) && $params['rows'] > 0 ? intval($params['rows']) : 10;

        //统计总记录数
        $options = $this->options;
        $total = $this->count();

        //排序并获取分页记录
        $options['order'] = empty($options['order']) ? $orderBy : $options['order'];
        $this->options = $options;
        $this->limit($pageSize)->page($page);
        $rows = $this->getAll();
        return array('total' => (int)$total, 'rows' => (empty($rows) ? false : $rows), 'pagecount' => ceil($total / $pageSize));
    }

    /**
     * 查询全部记录
     * @return mixed
     */
    public function getAll() {
        $rows = $this->select();
        format_time($rows);
        return $rows;
    }

    /**
     * 获取指定数据库的所有表名
     * @author huajie <banhuajie@163.com>
     */
    public function getTables(){
        $tables = M()->query('SHOW TABLES;');
        foreach ($tables as $key=>$value){
            $table_name = $value['Tables_in_'.C('DB_NAME')];
            $table_name = substr($table_name, strlen(C('DB_PREFIX')));
            $tables[$key] = $table_name;
        }
        return $tables;
    }
}