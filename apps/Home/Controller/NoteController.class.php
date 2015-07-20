<?php
namespace Home\Controller;
use Common\Model\NoteModel;

class NoteController extends StageController {
	public function index() {
		$noteModel = new NoteModel();
		$data = $noteModel->grid(array('page' => (int)$_GET['page'], 'rows' => 20));
		$this->assign('lists', $data['rows']);
		if($data) {
			$pager = pager($data['total'], $data['pagecount']);
			$this->assign('pager', $pager);
		}
		$this->display();
	}

	public function save() {
		if(IS_AJAX && IS_POST) {
			$noteModel = new NoteModel();
			$data = $noteModel->create();
			if($data) {
				$data['note_name'] = strip_tags($data['note_name']);
				$data['content'] = strip_tags($data['content']);
				if($noteModel->add($data)) {
					$this->success('提交成功');
				}else {
					$this->error('提交失败');
				}
			}else {
				$this->error($noteModel->getError());
			}
		}
	}

} 