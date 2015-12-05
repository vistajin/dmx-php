<?php

namespace Home\Controller;

use Think\Controller;

class NewsController extends Controller {
	public function activityList() {
		$this->assign ( "activityActive", "active" );
		self::showList ( "activity" );
	}
	public function newsList() {
		$this->assign ( "newsActive", "active" );
		self::showList ( "news" );
	}
	public function storyList() {
		$this->assign ( "storyActive", "active" );
		self::showList ( "story" );
	}
	private function showList($category) {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		
		$m = M ( "RichText" );
		$where ["category"] = $category;
		$where ["isShow"] = 1; //注意，isShow, show的S必须大些
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$list = $m->where ( $where )->limit ( $limit )->order ( "seq" )->select ();
		$this->assign ( "rtlist", $list );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
				
		$this->assign ( "subtitle", C ( $category ) );
		
		$this->display ( "newsList" );
	}
	public function viewDetails() {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		
		// 可以是POST，GET
		$uid = I("param.uid");
		$m = M ( "RichText" );
		$r = $m->where ( "uid = %d", $uid )->find ();
		if ($r != NULL) {
			$r ["content"] = str_replace("<img ", "<img class=\"carousel-inner img-responsive\"", $r["content"]);
			$this->assign ( "richText", $r ["content"] );
			$this->assign ( "subtitle", $r ["title"] );
			$this->assign ( $r ["category"]."Active", "active" );
		}
		
		$this->display ( "newsDetails" );
	}
}