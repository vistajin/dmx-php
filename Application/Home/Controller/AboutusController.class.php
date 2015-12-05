<?php

namespace Home\Controller;

use Think\Controller;

class AboutusController extends Controller {
	public function corpbrief() {
		$this->assign ( "corpbriefActive", "active" );
		self::_show ( "corpbrief" );
	}
	public function contactus() {
		$this->assign ( "contactusActive", "active" );
		self::_show ( "contactus" );
	}
	public function position() {
		$this->assign ( "positionActive", "active" );
		self::_show ( "position" );
	}
	private function _show($category) {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		
		$re = A ( "RichEdit" );
		$re->getList ( "activity", "activityList" );
		$re->getRichText ( $category );
		
		$this->assign ( "subtitle", C ( $category ) );
		$this->display ( "aboutus" );
	}
}