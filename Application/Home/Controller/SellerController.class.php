<?php

namespace Home\Controller;

use Think\Controller;

class SellerController extends Controller {
	public function sellerList() {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		
		$re = A ( "RichEdit" );
		// 公告 & 活动
		// $re->getList ( "announcement", "announcementList" );
		$re->getList ( "activity", "activityList" );
		
		$m = M ( "Seller" );
		$where = null;
		$floor = $_POST ["floor"];
		if ($floor != null) {
			$where ["floor"] = $floor;
		}
		$major_business = $_POST ["major_business"];
		if ($major_business != null) {
			$where ["major_business"] = array("LIKE", "%".$major_business."%");
		}
// 		$second_business = $_POST ["second_business"];
// 		if ($second_business != null) {
// 			$where ["second_business"] = $second_business;
// 		}
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$list = $m->where ( $where )->order("oseq desc, name")->limit ( $limit )->select ();
		foreach($list as &$r) {
			$r ["major_business_text"] = self::formatBusinessTypes ( $r ["major_business"] );
		}
		$this->assign ( "list", $list );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		self::getBusinessType ();
		self::getFloors ();
		$this->assign ( "floor", $floor );
		$this->assign ( "major_business", $major_business );
// 		$this->assign ( "second_business", $second_business );
		$this->display ();
	}
	protected function getFloors() {
		// TODO: 硬编码
		for($i = 1; $i < 5; $i ++) {
			$floors [$i] = $i;
		}
		$this->assign ( "floors", $floors );
	}
	protected function getSeller($uid) {
		$m = M ( "Seller" );
		$r = $m->where ( "uid=%d", $uid )->find ();
		$r ["major_business_text"] = self::formatBusinessTypes ( $r ["major_business"] );
		$this->assign ( "record", $r );
	}
	protected function getBusinessType() {
		$m = M ( "BusinessType" );
		$r = $m->select ();
		// 转换成一维数组方便映射
		$ary = array ();
		foreach ( $r as $entry ) {
			$ary [$entry ["uid"]] = $entry ["text"];
		}
		$this->assign ( "businessTypeList", $ary );
	}
	public function sellerDetails($uid) {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		
		$re = A ( "RichEdit" );
		// 公告 & 活动
		$re->getList ( "activity", "activityList" );
		
		self::getBusinessType ();
		self::getSeller ( $uid );
		$this->display ();
	}
	private function formatBusinessTypes($businessTypes) {
		$m = M ( "BusinessType" );
		$r = $m->select ();
		// 转换成一维数组方便映射
		$ary = array ();
		foreach ( $r as $entry ) {
			$ary [$entry ["uid"]] = $entry ["text"];
		}
		$ary2 = explode ( ".", trim($businessTypes, ".") );
		$text = "";
		foreach ($ary2 as $e) {
			$text .= $ary[$e].",";
		}
		return trim($text, ",");
	}
}