<?php
namespace Home\Controller;
use Common\Model\MemberModel;
use Common\Model\PayLogModel;
use Common\Library\PayPal;

class MemberController extends UcenterController {
	public $dbModel;

	public function __construct() {
		parent::__construct();
		$this->dbModel = new MemberModel();
	}

	//会员中心
	public function index() {
		$data = $this->dbModel->find(USER_ID);
		$this->assign('data', $data);
		$this->display();
	}

	public function save() {
		$params = I('post.');
		$params['user_id'] = USER_ID;
		$result = $this->dbModel->modifyPassword($params);
		if($result) {
			$this->success('保存成功！');
		}else {
			$this->error('数据没更新！');
		}
		
	}

	public function payLog() {
		$logModel = new PayLogModel();
		$data = $logModel->filter(array('user_id' => USER_ID))->grid(array('page' => (int)$_GET['page'], 'rows' => 10));
		$this->assign('lists', $data['rows']);
		if($data) {
			$pager = pager($data['total'], $data['pagecount']);
			$this->assign('pager', $pager);
		}
		$this->display();
	}

	public function buy() {
		//查询是否存在未回答的题目
		$where = array('user_id' => USER_ID, 'answer_time' => 0);
    	$topicCount = M('MemberTopic')->where($where)->count();
    	if($topicCount > 0) $this->error('您还有未答完的题目呢！', U('topic/index'));
		$buytype = session('buytype');
		if(empty($buytype)) $this->redirect('topic/buy');
		//记录日志
		$payLogModel = new PayLogModel();
		$where = array(
			'user_id' => USER_ID,
			'pay_status' => 0,
			'buy_type' => $buytype,
			'pay_time' => array('gt', time() - 7200) //两小时内的支付
		);
		$data = $payLogModel->where($where)->order('pay_id desc')->find();
		if(!$data) {
			//写入日志
			$data = $where;
			$data['money'] = $buytype == 1 ? 10 : 50;
			$data['buy_type'] = $buytype;
			$data['pay_time'] = time();
			$data['pay_sn'] = paySn();
			$payLogModel->add($data);
		}
		session('paySn', $data['pay_sn']);
		//开始生成支付连接
		$html = PayPal::getCode($data);
		$this->assign('html', $html);
		$this->display('Member:gotopay');
	}

	public function respond() {
		if(PayPal::respond()) {
			//发放题目
			$this->success('充值成功！', U('topic/index'));
		}else {
			$this->error('充值失败！');
		}
	}
} 