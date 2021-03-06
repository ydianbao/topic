<?php

/**
 * 获取当前完整路径
 * @return string
 */
function location_href() {
    $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
    $url .= $_SERVER['HTTP_HOST'];
    if($_SERVER['SERVER_PORT'] != '80') {
        $url .= ":" . $_SERVER['SERVER_PORT'];
    }
    $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);
    return $url;
}

/**
 * 分析枚举类型配置值 格式 a:名称1,b:名称2
 * @param $string
 * @return array
 */
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 格式化时间
 * @param $data
 * @param string $format
 */
function format_time(& $data, $format = 'Y-m-d H:i:s') {
    if(empty($data)) return false;
    $format = empty($format) ? C('DATE_FORMAT') : $format;
    foreach($data as $key => $item){
        if(is_array($item)){
            format_time($data[$key], $format);
        }else{
            if(strpos($key, 'time')){
                $data[$key] = $item > 0 ? date($format, $item) : null;
            }
        }
    }
}

/**
 * 验证手机端访问
 * @return bool
 */
function mobile_browser() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))  return true;
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array ('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
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

function ad($adId) {
    $adModel = M('Ad');
    $ad = $adModel->find($adId);
    if(!$ad) return null;
    $adConfig = json_decode($ad['ad_config'], true);
    $view = \Think\Think::instance('Think\View');
    if($adConfig) {
        $view->assign('ad_config', $adConfig);
        $html = $view->fetch('Public/ad');
        echo $html;
    }
}

function pager($total, $pageCount, $data = array(), $showNumber = 5) {
        //没有数据不显示分页
        if(empty($total) || empty($pageCount)) return null;

        $pager = array();
        $pager['total'] = $total;
        $pager['page_count'] = $pageCount;
        $pager['page'] = max((int)I('page'), 1);

        $url = strtolower(U('', $data));


        //设置上一页
        if($pager['page'] > 1) {
            $pager['prev_url'] = $url . '?page=' . ($pager['page'] - 1);
        }
        //设置下一页
        if(($pager['page'] + 1)  <= $pager['page_count']) {
            $pager['next_url'] = $url . '?page=' . ($pager['page'] + 1);
        }
        
        $showStart = ceil($pager['page'] / $showNumber) * $showNumber - ($showNumber - 1);//设置开始页
        $showEnd = $showStart + $showNumber -1;
        $showEnd = $showEnd > $pager['page_count'] ? $pager['page_count'] : $showEnd;

        if($pager['page_count'] > 1) {
            for($i = $showStart; $i <= $showEnd; $i++){
                $pager['pages'][$i]['page_url'] = $pager['page'] == $i ? 'javascript:;' : $url . '?page=' . $i;
            }
        }
        
        return $pager;
}