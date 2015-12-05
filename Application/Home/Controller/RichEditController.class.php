<?php

namespace Home\Controller;

use Think\Controller;

// 这个控制器跟Admin那个同名，主要是区分后台与前段
class RichEditController extends Controller {
	public function getRichText($category) {
		$m = M ( "RichText" );
		$r = $m->where ( "category = '%s'", $category )->find ();
		if ($r == NULL) {
			$this->assign ( "richText", "" );
		} else {
			$this->assign ( "richText", $r ["content"] );
		}
		$this->assign ( "category", $category );
		$this->assign ( "categoryText", C ( $category ) );
	}
	public function getList($category, $assignTo = "rtlist", $count = 5) {
		$m = M ( "RichText" );
		$r = $m->where ( "category = '%s' and isshow=%d", $category, 1 )->limit ( $count )->order ( "seq" )->select ();
		$this->assign ( $assignTo, $r );
	}
	public function viewSimpleRichText($uid) {
		$m = M ( "RichText" );
		$r = $m->where ( "uid = %d", $uid )->find ();
		
		$r ["content"] = str_replace("<img ", "<img class=\"carousel-inner img-responsive\"", $r["content"]);
		echo $r ["content"];
	}
}