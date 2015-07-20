<?php
namespace Home\Controller;
use Common\Model\PageModel;
class PageController extends StageController {
    public function _empty($name){
    	$pageModel = new PageModel();
    	$data = $pageModel->where("template = '%s'", $name)->find();
    	if(!$data) $this->error(L('PAGE_NOT_FOUND'));
    	$this->assign('data', $data);
        $this->display('Index:page');
    }
}