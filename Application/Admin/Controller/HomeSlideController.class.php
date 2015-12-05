<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

// 继承了AuthController，需要登陆才能访问
class HomeSlideController extends AuthController {
	public function slideList() {
		$homeSlide = M ( "HomeSlide" );
		$homeSlideList = $homeSlide->select ();
		$this->assign ( "list", $homeSlideList );
		
		// $php_json = json_encode($homeSlideList); //把php数组格式转换成 json 格式的数据
		// $this->assign("php_json",$php_json);
		
		$this->display ();
	}
	public function addSlide() {
		$this->display ();
	}
	public function doAddSlide($description) {
		$info = upload ();
		if ($info[0] == true) {
			$homeSlide = M ( "HomeSlide" );
			foreach ( $info[1] as $file ) {
				// echo $file["savepath"].$file["savename"];
				$data ["description"] = $description;
				$data ["path"] = $file ["savepath"] . $file ["savename"];
				$data ["upload_time"] = date ( "Y-m-d H:i:s", time () );
				$homeSlide->add ( $data );
			}
			$this->redirect ( "Admin/HomeSlide/slideList" );
		} else {
			$this->error ( $info[1] );
		}
	}
	public function editSlide($uid) {
		$homeSlide = M ( "HomeSlide" );
		$result = $homeSlide->where ( "uid = %d", $uid )->find ();
		$this->assign ( "record", $result );
		
		$this->display ();
	}
	public function updateSlide($uid, $description) {
		$m = M ( "HomeSlide" );
		$data ["description"] = $description;
		
		// 如果更换幻灯片
		if ($_FILES ["photo"] ["name"] != "") {
			$info = upload ();
			if ($info[0] == true) {
				foreach ( $info[1] as $file ) {
					$data ["path"] = $file ["savepath"] . $file ["savename"];
					$data ["upload_time"] = date ( "Y-m-d H:i:s", time () );
				}
				// 删除之前旧的文件
				$r = $m->where ( "uid=%d", $uid )->find();
				unlink($_SERVER['DOCUMENT_ROOT'].__ROOT__."/Uploads/".$r["path"]);
			} else {
				$this->error ( $info[1] );
			}
		}
		$m->where ( "uid = %d", $uid )->save ( $data );
		$this->redirect ( "Admin/HomeSlide/slideList" );
	}
	public function deleteSlide($uid) {
		$m = M ( "HomeSlide" );
		$r = $m->where ( "uid=%d", $uid )->find();
		$result = $m->where ( "uid = %d", $uid )->delete ();		
		if ($result === 1) {			
			unlink($_SERVER['DOCUMENT_ROOT'].__ROOT__."/Uploads/".$r["path"]);
			$this->redirect ( "Admin/HomeSlide/slideList" );			
		} else {
			echo "删除幻灯片失败！";
		}
	}
}