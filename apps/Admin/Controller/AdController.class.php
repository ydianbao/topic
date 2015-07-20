<?php
/**
 * ====================================
 * 广告管理
 * ====================================
 * Author: Administrator
 * Date: 2015/7/12 20:45
 * ====================================
 * File: AdController.class.php
 * ====================================
 */

namespace Admin\Controller;


class AdController extends CommonController {
    protected $tableName = 'Ad';

    public function form() {
        $id = intval($_GET['id']);
        if($id) {
            $info = $this->dbModel->find($id);
            $info['ad_config'] = json_decode($info['ad_config'], true);
            $this->assign('info', $info);
        }
        $this->display();
    }

    public function save() {
        $params = I('post.');
        $adConfig = array();
        foreach($params['ad_img'] as $key => $img) {
            if(empty($img)) continue;
            $adConfig[] = array(
                'img' => $img,
                'href' => $params['ad_href'][$key],
                'width' => $params['ad_width'][$key],
                'height' => $params['ad_height'][$key]
            );
        }
        if($params = $this->dbModel->create()) {
            $params['ad_config'] = empty($adConfig) ? '' : json_encode($adConfig);
            if(empty($params['ad_id'])) {
                $result = $this->dbModel->add($params);
            }else {
                $result = $this->dbModel->save($params);
            }
            if($result) $this->success(L('SAVE') . L('SUCCESS'));
            $this->error(L('SAVE') . L('ERROR'));
        }else {
            $this->error($this->dbModel->getError());
        }
    }
}