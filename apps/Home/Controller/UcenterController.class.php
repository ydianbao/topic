<?php
namespace Home\Controller;

class UcenterController extends StageController {
	public function __construct() {
        parent::__construct();

        if(!login('user_id')) {
        	$from = $_SERVER['HTTP_REFERER'];
        	redirect(U('passport/login') . '?from=' . $from);
        }else {
        	define('USER_ID', login('user_id'));
        }

    } 
} 