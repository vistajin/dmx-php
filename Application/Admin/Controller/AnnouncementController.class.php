<?php
namespace Admin\Controller;
use Common\Controller\AuthController;

// 继承了AuthController，需要登陆才能访问
class AnnouncementController extends AuthController {

    public function getList() {
    	$rt = A("RichEdit");
    	$rt->getList("announcement");
    	$this->display();
    	
    }

    public function addSlide() {
    	$this->display();
    }

    public function editSlide($uid) {
    	$homeSlide = M("HomeSlide");
	    $result = $homeSlide->where("uid = %d", $uid)->find();
	    $this->assign("record", $result);

    	$this->display();
    }

    public function updateSlide($uid, $description) {
    	// TODO: 更新文件
    	$homeSlide = M("HomeSlide");
    	$data["description"] = $description;
	    $homeSlide->where("uid = %d", $uid)->save($data);
	    $this->redirect("Admin/HomeSlide/slideList");
    }

    public function deleteSlide($uid) {
    	// TODO: 同时删除文件
    	$data = M("HomeSlide");
	    $result = $data->where("uid = %d", $uid)->delete();

	    if ($result === 1) {
	        $this->redirect("Admin/HomeSlide/slideList");
	    } else {	        
	        echo "删除幻灯片失败！";
	    }
    }

}