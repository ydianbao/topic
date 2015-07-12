<?php
/**
 * 公共控制器
 * User: 91336
 * Date: 2015/3/24
 * Time: 14:51
 */
namespace Common\Controller;
use Common\Library\Config;
use Think\Controller;

class PublicController extends Controller {
    public $jumpUrl = '';

    public function __construct() {
        parent::__construct();
        Config::init();
    }
}