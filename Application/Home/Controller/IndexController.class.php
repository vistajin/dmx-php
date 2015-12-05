<?php

namespace Home\Controller;

use Think\Controller;
use Admin\Controller\RichEditController;

class IndexController extends Controller {
	public function index() {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		
		// 公司简介
		$re = A ( "RichEdit" );
		$re->getRichText ( "corpbrief" );
		
		// 公告
		$re->getList ( "announcement", "announcementList" );
		
		// 活动
		$re->getList ( "activity", "activityList" );
		
		// 广告
		$this->assign ( "homeleftads", getAdsList ( "homeleftads" ) );
		$this->assign ( "homerightads", getAdsList ( "homerightads" ) );
		
		// 入驻商家
		$m = M ( "Seller" );
		$sellerList = $m->order ( "oseq desc, name" )->limit ( 20 )->select ();
		$this->assign ( "sellerList", $sellerList );
		
		// 拍卖
		$m = M ( "Auction" );
		$list = $m->order ( "start_date desc" )->limit ( 4 )->select ();
		$this->assign ( "auctionList", $list );
		
		
		$this->display ();
	}
}