<?php
namespace Home\Controller;
use Common\Model\ArticleModel;

class ArticleController extends StageController {
	public function index() {
		$articleModel = new ArticleModel();
		$data = $articleModel->grid(array('page' => (int)$_GET['page'], 'rows' => 1));
		$this->assign('lists', $data['rows']);
		if($data) {
			$pager = pager($data['total'], $data['pagecount']);
			$this->assign('pager', $pager);
		}
		$this->display();
	}

	public function show($id) {
		$articleModel = new ArticleModel();
		$data = $articleModel->find($id);
		format_time($data);
		$this->assign('data', $data);
		$this->display();
	}
} 