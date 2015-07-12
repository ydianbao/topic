<?php
/**
 * ====================================
 * 配置逻辑处理
 * ====================================
 * Author: Hugo
 * Date: 14-5-22 下午11:16
 * ====================================
 * File: ConfigService.class.php
 * ====================================
 */
namespace Common\Library;
class Config{

    //初始化配置选项
    public static function init() {
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config = self::lists();
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
    }

    //查询配置选项
    public static function lists(){
        $data   = M('Config')->field('type,name,value')->select();
        $config = array();
        if($data && is_array($data)){
            foreach ($data as $value) {
                $config[$value['name']] = self::parse($value['type'], $value['value']);
            }
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type  配置类型
     * @param  string  $value 配置值
     */
    private static function parse($type, $value){
        switch ($type) {
            case 4: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if(strpos($value,':')){
                    $value  = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k]   = is_numeric($v) ? floatval($v) : $v;
                    }
                }else{
                    $value =    $array;
                }
                break;
        }
        return $value;
    }
}