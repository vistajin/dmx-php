<?php

namespace Home\Controller;
use Think\Controller;

class FreeServiceController extends Controller {
	public function freeService() {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		
		$re = A ( "RichEdit" );
		// 公告 & 活动
		//$re->getList ( "announcement", "announcementList" );
		$re->getList ( "activity", "activityList" );
		$re->getRichText("freeservice");
		$this->display();		
	}
}