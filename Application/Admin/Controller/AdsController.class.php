<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

// 继承了AuthController，需要登陆才能访问
class AdsController extends AuthController {
	public function adsList($category) {
		$m = M ( "Ads" );
		$where ["category"] = $category;
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		$limit = $Page->firstRow . ',' . $Page->listRows;
		
		$joinStr = "think_seller on think_seller.uid = think_ads.seller_uid";
		$fields = "think_seller.name as name, think_ads.isShow as isShow, think_ads.category as category, think_ads.uid as uid";
		$list = $m->join ( $joinStr )->where ( $where )->field ( $fields )->limit ( $limit )->order ( "isShow desc, seq" )->select ();
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		$this->assign ( "rtlist", $list );
		$this->assign ( "category", $category );
		$this->assign ( "categoryText", C ( $category ) );
		$this->assign ( "nowPage", $Page->nowPage );
		$this->assign ( "totalPages", $Page->totalPages );
		$this->display ();
	}
	public function createAdsInput($category) {
		// 商家列表
		$m = M ( "Seller" );
		$r = $m->select ();
		$this->assign ( "sellerList", $r );
		
		$this->assign ( "category", $category );
		$this->assign ( "categoryText", C ( $category ) );
		$this->display ();
	}
	public function addAds() {
		$m = M ( "Ads" );
		$data ["seller_uid"] = $_POST ["seller_uid"];
		$data ["category"] = $_POST ["category"];
		$data ["isShow"] = $_POST ["isShow"];
		
		$r = $m->where ( "category='%s'", $_POST ["category"] )->field ( "max(seq) as seq" )->find ();
		$data ["seq"] = intval ( $r ["seq"] ) + 1;
		
		$m->add ( $data );
		$this->redirect ( "Admin/Ads/adsList/category/" . $_POST ["category"] );
	}
	public function upAds($uid, $category) {
		self::moveOrder ( $uid, $category, "up" );
	}
	public function downAds($uid, $category) {
		self::moveOrder ( $uid, $category, "down" );
	}
	private function moveOrder($uid, $category, $upOrDown) {
		if ($upOrDown == "up") {
			$comp = "<";
			$fun = "max";
			$err = "该记录已经是第一条记录！";
		} else {
			$comp = ">";
			$fun = "min";
			$err = "该记录已经是最后一条可显示记录！";
		}
		$m = M ( "Ads" );
		$r = $m->where ( "uid = %d", $uid )->find ();
		$rr = $m->where ( "isShow = 1 and category = '%s' and seq " . $comp . " %d", $category, $r ["seq"] )->field ( $fun . "(seq) as seq" )->find ();
		
		if ($rr ["seq"] != null) {
			$seq1 = $r ["seq"];
			$seq2 = $rr ["seq"];
			
			$r2 = $m->where ( "isShow = 1 and category = '%s' and seq = %d", $category, $seq2 )->field ( "uid" )->find ();
			$uid2 = $r2 ["uid"];
			
			$r ["seq"] = $seq2;
			$m->where ( "uid = %d", $uid )->save ( $r );
			
			$r2 ["seq"] = $seq1;
			$m->where ( "uid = %d", $uid2 )->save ( $r2 );
			
			$this->redirect ( "Admin/Ads/adsList/category/" . $category );
		} else {
			$this->error ( $err );
		}
	}
	
	public function editAdsInput($uid) {
		// 商家列表
		$m = M ( "Seller" );
		$r = $m->select ();
		$this->assign ( "sellerList", $r );
		
		$m = M ( "Ads" );
		$result = $m->where ( "uid = %d", $uid )->find ();
		$this->assign ( "record", $result );
		$this->assign ( "categoryText", C ( $result ["category"] ) );
	
		$this->display ();
	}
	
	public function updateAds($uid, $seller_uid, $category) {
		$m = M ( "Ads" );
		$data ["isShow"] = $_POST ["isShow"];
		$data ["seller_uid"] = $seller_uid;
		$m->where ( "uid = %d", $uid )->save ( $data );
		$this->redirect ( "Admin/Ads/adsList/category/" . $category );
	}
	
	public function deleteAds($uid, $category) {
		$data = M ( "Ads" );
		$result = $data->where ( "uid = %d", $uid )->delete ();
		if ($result === 1) {
			$this->redirect ( "Admin/Ads/adsList/category/" . $category );
		} else {
			echo "删除" . C ( $category ) . "失败！";
		}
	}
}