<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

class SellerController extends AuthController {
	public function sellerList() {
		$m = M ( "Seller" );
		$where = null;
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$list = $m->where ( $where )->order("oseq desc, name")->limit ( $limit )->select ();
		$this->assign ( "list", $list );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		$this->display ();
	}
	public function addSellerIn() {
		self::getBusinessType ();
		self::getFloors ();
		$this->display ();
	}
	public function addSeller() {
		$m = M ( "Seller" );
		$data ["name"] = $_POST ["name"];
		$data ["oseq"] = $_POST ["oseq"];
		$data ["floor"] = $_POST ["floor"];
		$data ["major_business"] = "." . implode ( '.', $_POST ["major_business"] ) . ".";
		$data ["deal_in"] = $_POST ["deal_in"];
		// $data ["second_business"] = $_POST ["second_business"];
		$data ["address"] = $_POST ["address"];
		$data ["contact_person"] = $_POST ["contact_person"];
		$data ["contact_phone"] = $_POST ["contact_phone"];
		$data ["email"] = $_POST ["email"];
		$data ["postcode"] = $_POST ["postcode"];
		$data ["introduction"] = $_POST ["introduction"];
		$data ["update_time"] = date ( "Y-m-d H:i:s", time () );
		
		if ($_FILES ["photo"] ["name"] != "") {
			$info = upload ();
			if ($info [0] == true) {				
				foreach ( $info [1] as $file ) {
					$data ["logo"] = $file ["savepath"] . $file ["savename"];
				}				
			} else {
				$this->error ( $info [1] );
			}
		}
		$m->add ( $data );
		$this->redirect ( "Admin/Seller/sellerList" );
	}
	protected function getFloors() {
		// TODO: 硬编码
		for($i = 1; $i < 5; $i ++) {
			$floors [$i] = $i;
		}
		$this->assign ( "floors", $floors );
	}
	private function getSeller($uid) {
		$m = M ( "Seller" );
		$r = $m->where ( "uid=%d", $uid )->find ();
		$r ["major_business_text"] = self::formatBusinessTypes ( $r ["major_business"] );
		$this->assign ( "record", $r );
	}
	public function editSellerIn($uid) {
		self::getBusinessType ();
		self::getSeller ( $uid );
		self::getFloors ();
		$this->display ();
	}
	public function viewSeller($uid) {
		// self::getBusinessType ();
		self::getSeller ( $uid );
		self::getFloors ();
		$this->display ();
	}
	private function getBusinessType() {
		$m = M ( "BusinessType" );
		$r = $m->select ();
		// 转换成一维数组方便映射
		$ary = array ();
		foreach ( $r as $entry ) {
			$ary [$entry ["uid"]] = $entry ["text"];
		}
		$this->assign ( "businessTypeList", $ary );
	}
	public function deleteSeller() {
		$uid = $_POST ["uid"];
		
		// 检查该商家是否在广告表think_ads
		$mAds = M ( "Ads" );
		$rAds = $mAds->where ( "seller_uid=%d", $uid )->find ();
		if ($rAds != null) {
			$this->error ( "该商家在广告列表，不能被删除！" );
		}
		
		$m = M ( "Seller" );
		$r = $m->where ( "uid=%d", $uid )->find ();
		$result = $m->where ( "uid=%d", $uid )->delete ();
		if ($result === 1) {
			unlink ( $_SERVER ['DOCUMENT_ROOT'] . __ROOT__ . "/Uploads/" . $r ["logo"] );
			$this->redirect ( "Admin/Seller/sellerList" );
		} else {
			$this->error ( "删除失败！" );
		}
	}
	public function updateSeller() {
		$m = M ( "Seller" );
		$data ["name"] = $_POST ["name"];
		$data ["oseq"] = $_POST ["oseq"];
		$data ["floor"] = $_POST ["floor"];
		$data ["major_business"] = "." . implode ( '.', $_POST ["major_business"] ) . ".";
		$data ["deal_in"] = $_POST ["deal_in"];
		// $data ["second_business"] = $_POST ["second_business"];
		$data ["address"] = $_POST ["address"];
		$data ["contact_person"] = $_POST ["contact_person"];
		$data ["contact_phone"] = $_POST ["contact_phone"];
		$data ["email"] = $_POST ["email"];
		$data ["postcode"] = $_POST ["postcode"];
		$data ["introduction"] = $_POST ["introduction"];
		$data ["update_time"] = date ( "Y-m-d H:i:s", time () );
		
		if ($_FILES ["photo"] ["name"] != "") {
			$info = upload ();
			if ($info [0] == true) {
				foreach ( $info [1] as $file ) {
					$data ["logo"] = $file ["savepath"] . $file ["savename"];
				}
				// 删除之前旧的文件
				$r = $m->where ( "uid=%d", $_POST ["uid"] )->find ();
				unlink ( $_SERVER ['DOCUMENT_ROOT'] . __ROOT__ . "/Uploads/" . $r ["logo"] );
			} else {
				$this->error ( $info [1] );
			}
		}
		
		$m->where ( "uid=%d", $_POST ["uid"] )->save ( $data );
		$this->redirect ( "Admin/Seller/sellerList" );
	}
	private function formatBusinessTypes($businessTypes) {
		$m = M ( "BusinessType" );
		$r = $m->select ();
		// 转换成一维数组方便映射
		$ary = array ();
		foreach ( $r as $entry ) {
			$ary [$entry ["uid"]] = $entry ["text"];
		}
		$ary2 = explode ( ".", trim ( $businessTypes, "." ) );
		$text = "";
		foreach ( $ary2 as $e ) {
			$text .= $ary [$e] . ",";
		}
		return trim ( $text, "," );
	}
}