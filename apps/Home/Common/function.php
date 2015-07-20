<?php

/**
 * 获取登录信息
 * @param $key
 * @return null
 */
function login($key) {
    static $cookie;
    if(!$cookie) {
        $cookie = unserialize(session('member_login'));
    }

    return isset($cookie[$key]) ? $cookie[$key] : null;
}

/**
 * 得到新订单号
 * @return  string
 */
function paySn($type='') {
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);
    $paySn = $type.date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    $add_time = strtotime(date("Y-m-d")) - 604800;
    $row = M('PayLog')->where("pay_time > $add_time AND pay_sn = '$paySn'")->count();
    if($row) {
        return paySn($type);
    }
    return $paySn;

}

//设置已支付
function topicPaid($payId) {
    $data = array(
        'pay_status' => 1,
        'pay_id' => $payId
    );
    M('PayLog')->save($data);

    $log = M('PayLog')->find($payId);
    if($log['buy_type'] == 2) {
        //更新会员充值有效期
        $data = array(
            'user_id' => $log['user_id'],
            'invalid_time' => strtotime('+1 months')
        );
        M('Member')->save($data);
        $member = session('member_login');
        if($member) {
            $member['invalid_time'] = $data['invalid_time'];
            session('member_login', $member);
        }
    }else {
        //发放题目
        $orderBy = M('Topic')->alias(' AS t')
                ->join('__MEMBER_TOPIC__ AS mt ON mt.topic_id = t.topic_id', 'right')
                ->where("mt.user_id = '%d'", login('user_id'))
                ->order('mt.create_time desc')
                ->getField('t.orderby');
        $maxOrder = M('Topic')->max('orderby');
        $where = array(
            'is_free' => 0
        );
        $size = 50;
        if($orderBy <= ($maxOrder - $size)) {
            $where['orderby'] = array('gt', $orderBy);
        }elseif($orderBy == $maxOrder) {
            $where['orderby'] = array('gt', 1);
        }else {
            $where['orderby'] = array('gt', $maxOrder - 50);
        }
        $topic = M('Topic')->field('topic_id')->where($where)->order('orderby asc')->limit($size)->select();
        if($topic) {
            foreach($topic as $key => $row) {
                $data = array(
                    'user_id' => login('user_id'),
                    'topic_id' => $row['topic_id'],
                    'create_time' => time()
                );
                M('MemberTopic')->add($data);
            }
        }
    }
}