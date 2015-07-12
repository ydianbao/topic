<?php
/**
 * 获取登录信息
 * @param $key
 * @return null
 */
function login($key) {
    static $cookie;
    if(!$cookie) {
        $cookie = unserialize(session('login_cookie'));
    }

    return isset($cookie[$key]) ? $cookie[$key] : null;
}

/**
 * 验证逻辑权限
 * @param $item
 * @return bool
 */
function power($item, $show_error = false) {
    static $menu = array();
    if(login('is_open')) return true;
    
    if(empty($menu)) {
        $menu = login('menu');
    }
    $result = false;
    if(is_array($item)) {
        foreach($item as $key => $sitem) {
            if(in_array(strtolower($sitem), $menu)) {
                $result = true;
                break;
            }
        }
    }else {
        $result = in_array(strtolower($item), $menu);
    }
    if(!$result && $show_error) {
        if(IS_AJAX) {
            $data['info']   =   L('_NOT_ACCESS_');
            $data['status'] =   0;
            exit(json_encode($data));
        }else {
            $view = \Think\Think::instance('Think\View');
            $view->assign('msgTitle', L('_OPERATION_FAIL_'));
            $view->assign('status', 0);   // 状态
            $view->assign('error', L('_NOT_ACCESS_'));// 提示信息
            $view->display(C('TMPL_ACTION_ERROR'));
        }
        exit;
    }
    return $result;
}

/**
 * 密码处理
 * @param $password
 * @return bool|string
 */
function password($password) {
    if(empty($password)) return false;
    return md5(md5($password) . C('CRYPT_KEY'));
}
