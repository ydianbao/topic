<?php
/**
 * ====================================
 * 公共函数类
 * ====================================
 * Author: Hugo
 * Date: 14-5-20 下午10:06
 * ====================================
 * File: Common.class.php
 * ====================================
 */

namespace Common\Library;
class Common {
    /**
     * 树型结构
     * @param $data
     * @param string $selected
     * @return array|null
     */
    public static function tree(& $data, $selected = '', $type = '') {
        if($selected) {
            $selected = strpos($selected, ',') ? explode(',', $selected) : array($selected);
            foreach($data as $key => $row){
                $row['tree_id'] = $row['id'];
                if(in_array($row['id'], $selected)){
                    $row['checked'] = true;
                }
                $data[$key] = $row;
            }
        }

        $data = Tree::treeArray($data);
        if(in_array($type,array('select','parent'))) {
            $data = array(
                array('id' => 0, 'text' => L($type . '_NODE') , 'pid' => 0, 'children' => $data)
            );
        }

        return $data;
    }

    /**
     * 判断是否手机号码
     * @param $phone
     * @return int
     */
    public static function isMobile($phone) {
        return  preg_match("/^1[3-5,7-8]{1}[0-9]{9}$/", $phone);
    }

    /**
     * 验证邮箱地址
     * @param $string
     * @return int
     */
    public static function isEmail($string) {
        return preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $string);
    }

    /**
     * 判断是否为微信号
     * @param $string
     * @return int
     */
    public static function isWeiXin($string){
        return preg_match("/^\w{1}[\w\d\-_]{5,19}$/", $string);
    }
}