<?php
/**
 * ====================================
 * 这里是说明
 * ====================================
 * Author: Tommy
 * Date: 2015 2015/5/17 13:28
 * ====================================
 * File: EmptyController.class.php
 * ====================================
 */
namespace Admin\Controller;
use Common\Controller\PublicController;

class EmptyController extends PublicController {
    public function _empty($name) {
        $this->error(CONTROLLER_NAME . '/' . ACTION_NAME . L('NOT_EXISTS'));
    }
}