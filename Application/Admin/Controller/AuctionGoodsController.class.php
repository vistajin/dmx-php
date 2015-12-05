<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

class AuctionGoodsController extends AuthController {
	public function auctionGoodsList() {
		$m = M ( "AuctionGoods" );
		$where = null;
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$msglist = $m->where ( $where )->order ( "update_time desc" )->limit ( $limit )->select ();
		$this->assign ( "list", $msglist );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		
		// 为了在列表把category的数字ID转换成文字
		self::getProductCategory ();
		$this->assign ( "goodsStatusList", C ( "goodsStatus" ) );
		$this->display ();
	}
	public function addAuctionGoodsIn() {
		self::getProductCategory ();
		$this->assign ( "goodsStatusList", C ( "goodsStatus" ) );
		$this->display ();
	}
	private function getProductCategory() {
		$m = M ( "ProductCategory" );
		$r = $m->where ( "parent_uid=0" )->select (); // 只选择第一级分类
		                                           // 转换成一维数组方便映射
		$ary = array ();
		foreach ( $r as $entry ) {
			$ary [$entry ["uid"]] = $entry ["text"];
		}
		$this->assign ( "productCategoryList", $ary );
	}
	public function addAuctionGoods() {
		$info = upload ();
		if ($info [0] == true) {
			$m = M ( "AuctionGoods" );
			foreach ( $info [1] as $file ) {
				$data ["name"] = $_POST ["name"];
				$data ["category"] = $_POST ["category"];
				$data ["price"] = $_POST ["price"];
				// $data ["begin_price"] = $_POST ["begin_price"];
				// $data ["end_price"] = $_POST ["end_price"];
				// $data ["begin_time"] = $_POST ["begin_time"];
				// $data ["end_time"] = $_POST ["end_time"];
				$data ["description"] = $_POST ["description"];
				$data ["winner"] = $_POST ["winner"];
				$data ["status"] = $_POST ["status"];
				$data ["photo_url"] = $file ["savepath"] . $file ["savename"];
				$data ["update_time"] = date ( "Y-m-d H:i:s", time () );
				$m->add ( $data );
			}
			$this->redirect ( "Admin/AuctionGoods/auctionGoodsList" );
		} else {
			$this->error ( $info [1] );
		}
	}
	public function editAuctionGoodsIn($uid) {
		self::getAuctionGoods ( $uid );
		$this->assign ( "goodsStatusList", C ( "goodsStatus" ) );
		$this->display ();
	}
	public function viewAuctionGoods($uid) {
		self::getAuctionGoods ( $uid );
		$this->assign ( "goodsStatusList", C ( "goodsStatus" ) );
		$this->display ();
	}
	public function deleteAuctionGoods() {
		$uid = $_POST ["uid"];
		// 如果物品已被关联，则不能删除
		$mMap = M ("AuctionGoodsMap");
		$rMap = $mMap->where("auction_goods_uid=%d", $uid)->find();
		if ($rMap != null) {
			dump($mMap);
			//$this->error("该物品已在拍卖活动中，不能删除！");
		}
		
		$m = M ( "AuctionGoods" );
		$r = $m->where ( "uid=%d", $uid )->find ();
		
		$result = $m->where ( "uid=%d", $uid )->delete ();
		if ($result === 1) {
			// 同时删除物品图片
			unlink ( $_SERVER ['DOCUMENT_ROOT'] . __ROOT__ . "/Uploads/" . $r ["photo_url"] );
			$this->redirect ( "Admin/AuctionGoods/auctionGoodsList" );
		} else {
			$this->error ( "删除失败！" );
		}
	}
	public function updateAuctionGoods() {
		$m = M ( "AuctionGoods" );
		$data ["name"] = $_POST ["name"];
		$data ["category"] = $_POST ["category"];
		$data ["status"] = $_POST ["status"];
		$data ["price"] = $_POST ["price"];
		// $data ["begin_price"] = $_POST ["begin_price"];
		// $data ["end_price"] = $_POST ["end_price"];
		$data ["begin_time"] = $_POST ["begin_time"];
		$data ["end_time"] = $_POST ["end_time"];
		$data ["description"] = $_POST ["description"];
		$data ["winner"] = $_POST ["winner"];
		$data ["update_time"] = date ( "Y-m-d H:i:s", time () );
		
		if ($_FILES ["photo"] ["name"] != "") { // 有上传新文件
			$info = upload ();
			if ($info [0] == true) {
				foreach ( $info [1] as $file ) {
					$data ["photo_url"] = $file ["savepath"] . $file ["savename"];
				}
				// 删除之前旧的文件
				$r = $m->where ( "uid=%d", $_POST ["uid"] )->find();
				unlink($_SERVER['DOCUMENT_ROOT'].__ROOT__."/Uploads/".$r["photo_url"]);
			} else {
				$this->error ( $info [1] );
			}
		}
		$m->where ( "uid=%d", $_POST ["uid"] )->save ( $data );
		$this->redirect ( "Admin/AuctionGoods/auctionGoodsList" );
	}
	private function getAuctionGoods($uid) {
		self::getProductCategory ();
		$m = M ( "AuctionGoods" );
		$r = $m->where ( "uid=%d", $uid )->find ();
		$this->assign ( "record", $r );
	}
	public function copyAuctionGoods($uid) {
		$m = M ( "AuctionGoods" );
		$r = $m->where ( "uid = %d", $uid )->find ();
		unset ( $r ["uid"] );
		unset ( $r ["photo_url"] ); // 复制的物品起始无缩略图
		$r ["status"] = 0; // 默认可拍卖
		$r ["update_time"] = date ( "Y-m-d H:i:s", time () );
		$m->add ( $r );
		
		$this->redirect ( "Admin/AuctionGoods/auctionGoodsList" );
	}
}