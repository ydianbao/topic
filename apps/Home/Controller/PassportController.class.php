<?php
namespace Home\Controller;
use Common\Model\MemberModel;

class PassportController extends StageController {
	public function _empty() {
		redirect(U('login'));
	}

	public function logout() {
		session('member_login', null);
        session_destroy();
        redirect(U('passport/index'));
	}

	public function login() {
		if(IS_POST) {
			$memberModel = new MemberModel();
			$params = I('post.');
			$where = array(
				'email' => strip_tags($params['email']),
				'password' => password($params['password'])
			);
			$data = $memberModel->where($where)->find();

			if($data) {
				//登录成功
				$session = array(
					'user_id' => $data['user_id'],
					'email' => $data['email'],
					'invalid_time' => $data['invalid_time']
				);
				session('member_login', serialize($session));
				redirect(U('member/index'));
			}
			$this->error('邮箱或密码不正确！');
		}
		$this->display('Member:login');
	}

	public function register() {
		if(IS_POST) {
			$memberModel = new MemberModel();
			$data = $memberModel->create();
			if($data) {
				$result = $memberModel->add($data);
				if($result) {
					$this->success('注册成功!', U('member/index'));
				}
			}
			$this->error($memberModel->getError());
		}
	}
} 