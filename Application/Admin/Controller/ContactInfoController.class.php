<?php

namespace Admin\Controller;

use Think\Controller;

class ContactInfoController extends Controller {
	public function aboutus() {
		$m = M("RichText");
		$data["content"] = $content;
		$r = $m->where("category = 'AboutUs'")->find();
		if ($r == NULL) {
			$this->assign("aboutus", "");
		} else {
			$this->assign("aboutus", $r["content"]);
		}
		$this->display ();
	}
	public function updateAboutUs($content) {
		$m = M("RichText");
		$data["content"] = $content;
		$r = $m->where("category = 'AboutUs'")->find();
		if ($r == NULL) {
			$data["category"] = "AboutUs";			
			$data["modified_time"] = date("Y-m-d H:i:s", time());
			$m->add($data);
		} else {
			$m->where("category = 'AboutUs'")->save($data);
		}
		//$this->success("更新成功！");
		$this->redirect("Admin/ContactInfo/aboutus");
	}
	
	public function getRichText($category) {
		$m = M("RichText");
		$data["content"] = $content;
		$r = $m->where("category = '%s'", $category)->find();
		if ($r == NULL) {
			$this->assign("richText", "");
		} else {
			$this->assign("richText", $r["content"]);
		}
		$this->assign("category", $category);
		$this->display ();
	}
	public function setRichText($category, $content) {
		$m = M("RichText");
		$data["content"] = $content;
		$r = $m->where("category = '%s'", $category)->find();
		if ($r == NULL) {
			$data["category"] = $category;
			$data["modified_time"] = date("Y-m-d H:i:s", time());
			$m->add($data);
		} else {
			$m->where("category = '%s'", $category)->save($data);
		}
		//$this->success("更新成功！");
		$this->redirect("Admin/ContactInfo/getRichText/category/".$category);
	}
}