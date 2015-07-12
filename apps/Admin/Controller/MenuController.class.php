<?php
/**
 * ====================================
 * 菜单管理
 * ====================================
 * Author: Hugo
 * Date: 14-5-20 下午9:58
 * ====================================
 * File: MenuController.class.php
 * ====================================
 */

namespace Admin\Controller;

class MenuController extends CommonController {
    protected $tableName = 'Menu';
    protected $allowAction = array('icon');
}