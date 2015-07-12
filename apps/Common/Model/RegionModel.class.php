<?php
/**
 * ====================================
 * 地区数据模型
 * ====================================
 * Author: 91336
 * Date: 2014/11/29 9:34
 * ====================================
 * File: RegionModel.class.php
 * ====================================
 */
namespace Common\Model;
use Common\Library\Common;

class RegionModel extends CommonModel {
    protected $_validate = array(
        array('text','require','{%region_name_lost}'),
        array('pid', 'validateEq', '{%parent_error}', self::EXISTS_VALIDATE, 'callback', self::MODEL_UPDATE),
        array('text','validateName','{%region_name_exists}', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    /**
     * 验证地区地址是否存在
     * @param $region_name
     * @param $data
     * @return bool
     */
    protected function validateName($text, $data) {
        $data = I('post.');
        $where = array();

        //如果是新增地区，判断所选上级地区下是否存在新增的地区名称
        $where['pid'] = (int)$data['pid'];
        $where['text'] = trim($data['text']);

        //如果是编辑地区，判断所选上级地区是否存在除所编辑地区外的新地区名称
        if(!empty($data['id'])) {
            $where['id'] = array('neq', (int)$data['id']);
        }
        return $this->where($where)->count() > 0 ? false : true;
    }

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
        $where = array();
        if($params['id']) {
            $where['b.pid'] = (int)$params['id'];
        }elseif($params['keyword']) {
            $where['b.text'] = array('LIKE', "%{$params['keyword']}%");
        }else{
            $where['b.pid'] = 0;
        }

        if(empty($params['type'])) {
            $data = $this->alias(' AS b')
                ->where($where)
                ->join("__REGION__ AS s ON s.pid = b.id", 'left')
                ->field("b.id, b.pid, b.text, count(s.id) as have_children")
                ->group('b.id')
                ->order('b.pid ASC')
                ->select();

            foreach($data as $key => $row){
                if($row['have_children']) {
                    $row['state'] = 'closed';
                    $data[$key] = $row;
                }
            }
        }else {
            $data = $this->alias(' AS b')
                ->field("b.id, b.pid, b.text")
                ->order('b.pid ASC')
                ->select();
            Common::tree($data, $params['selected']);
        }

        return $data;
    }


    function getRegion($pid){
        $data = $this->where(array('pid' => $pid))->select();
        return $data;
    }
}