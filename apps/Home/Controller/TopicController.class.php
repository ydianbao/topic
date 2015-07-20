<?php
namespace Home\Controller;
use Common\Model\TopicModel;
class TopicController extends StageController {

	public function index() {
		$topicModel = new TopicModel();
		//判断当前题目是否存在
		$topic = session('topic');
		if($topic) {
			//判断当前题目是否已答，未答继续显示
			if($topic['user_answer']) {
				//查询下一题，判断是否当前题目为免费
				if($topic['is_free']) {
					//免费题判断是否还存在下免费题
					$where = array(
						'orderby' => array('gt', $topic['orderby']),
						'is_free' => 1
					);
					$topic = $topicModel->where($where)->order('orderby asc')->find();
					if($topic) {
						$this->_session($topic);
					}else {
						$topic = $this->_findPrivate();
					}
				}else {
					$topic = $this->_findPrivate();
				}
			}
		}else {
			$topic = $this->_findPrivate();
			if(!$topic) {
				//读取第一道免费题
				$where = array(
					'is_free' => 1
				);
				$topic = $topicModel->where($where)->order('orderby asc')->find();
				$this->_session($topic);
			}
		}

		if($topic) {
			$topic['answer_config'] = json_decode($topic['answer_config'], true);
			$this->assign('topic', $topic);
			$this->display('Member:topic');
		}else {
			session('topic', null);
			$this->redirect('topic/buy');
		}
		
	}

	private function _session($topic) {
		session(array('name'=>'topic','expire'=>86400));
		session('topic', $topic);
	}

	private function _findPrivate() {
		if(!login('user_id')) return false;
		$topicModel = new TopicModel();
		$topic = session('topic');
		$invalidTime = login('invalid_time');
		$nowTime = time();
		//读取最后一题
		$maxOrder = $topicModel->max('orderby');

		$topicModel->alias('AS t');
		$topicModel->order('t.orderby asc');
		//查询
		$where = array(
			't.is_free' => 0
		);
		if($topic) {
			$where['t.orderby'] = array('gt', $topic['orderby']);
		}

		//有效期内按顺序读取题目
		if($invalidTime) {
			if($nowTime <= $invalidTime) {
				//如果已做到最后一题，返回第一题重做
				if($topic && $topic['orderby'] >= $maxOrder) {
					$where['t.orderby'] = array('gt', 1);
				} 
			}else {
				return false;
			}
		}else {
			//查询是否存在未答的购买题目
			$where['mt.user_id'] = login('user_id');
			$where['mt.answer_time'] = 0;
			$table = '__MEMBER_TOPIC__';
			$topicModel->join("$table AS mt ON t.topic_id = mt.topic_id", "right");
			$topicModel->field('t.*, mt.id AS mt_id');
		}
		
		$topicModel->where($where);
		$topic = $topicModel->find();
		$this->_session($topic);
		return $topic;
	}

    public function setAnswer() {
    	$topic = session('topic');
    	if($topic) {
    		$topic['user_answer'] = I('post.answer');
	    	session('topic', $topic);
	    	//设置答案
	    	if($topic['mt_id']) {
	    		$mtModel = M('MemberTopic');
	    		$data = array(
	    			'id' => $topic['mt_id'],
	    			'answer' => $topic['user_answer'],
	    			'answer_time' => time(),
	    			'is_right' => $topic['user_answer'] == $topic['answer'] ? 1 : 0,
    			);
    			$mtModel->save($data);
	    	}

	    	$this->success();
    	}else {
    		$this->error('请选择题目', U('index'));
    	}
    	
    }

    public function showAnswer() {
    	$topic = session('topic');
    	if($topic) {
    		$topic['answer_config'] = json_decode($topic['answer_config'], true);
    		$topic['answer_title'] = $topic['answer_config'][$topic['answer']];
    		$this->assign('topic', $topic);
    		$this->display('Member:showAnswer');
    	}else {
    		redirect(U('index'));
    	}
    }

    public function buy() {
    	$this->display('Member:buy');
    }

    public function setBuy() {
    	$buytype = intval(I('post.buytype'));
    	if(empty($buytype)) $this->error('请选择充值类型');
    	session('buytype', $buytype);
    	$this->success();
    }
}