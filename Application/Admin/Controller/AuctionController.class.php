<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

class AuctionController extends AuthController {
	public function auctionList() {
		$m = M ( "Auction" );
		$where = null;
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$msglist = $m->where ( $where )->order ( "start_date desc" )->limit ( $limit )->select ();
		$this->assign ( "list", $msglist );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		$this->display ();
	}
	public function addAuctionIn() {
		$this->display ();
	}
	public function addAuction() {
		$info = upload ();
		if ($info [0] == true) {
			$m = M ( "Auction" );
			foreach ( $info [1] as $file ) {
				$data ["title"] = $_POST ["title"];
				$data ["start_date"] = $_POST ["start_date"];
				$data ["end_date"] = $_POST ["end_date"];
				$data ["description"] = $_POST ["description"];
				$data ["photo_url"] = $file ["savepath"] . $file ["savename"];
				$data ["update_time"] = date ( "Y-m-d H:i:s", time () );
				$m->add ( $data );
			}
			$this->redirect ( "Admin/Auction/auctionList" );
		} else {
			$this->error ( $info [1] );
		}
	}
	public function editAuctionIn($uid) {
		self::getAuction ( $uid );
		$this->display ();
	}
	public function viewAuction($uid) {
		self::getAuction ( $uid );
		self::getAuctionGoods ( $uid );
		$this->display ();
	}
	private function getAuctionGoods($uid) {
		$m = M ( "AuctionGoodsMap" );
		$where ["auction_uid"] = $uid;
		$join = "think_auction_goods on think_auction_goods.uid = think_auction_goods_map.auction_goods_uid";
		
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$list = $m->join ( $join )->where ( $where )->limit ( $limit )->order("name")->select ();
		$this->assign ( "list", $list );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		
		// 为了在列表把category的数字ID转换成文字
		self::getProductCategory ();
		$this->assign ( "goodsStatusList", C ( "goodsStatus" ) );
	}
	public function deleteAuction() {
		$uid = $_POST ["uid"];
		$m = M ( "Auction" );
		$r = $m->where ( "uid=%d", $uid )->find ();
		$result = $m->where ( "uid=%d", $uid )->delete ();
		if ($result === 1) {
			// 恢复所有已关联的物品的状态为可拍卖
			$m = M ( "AuctionGoodsMap" );
			$rr = $m->where ( "auction_uid=%d", $uid )->select ();
			foreach ( $rr as $mapRecord ) {
				$goodsUid = $mapRecord ["auction_goods_uid"];
				$mGoods = M ( "AuctionGoods" );
				$data ["status"] = 0;
				$mGoods->where ( "uid=%d", $goodsUid )->save ( $data );
			}
			
			// 删除auction<->goods映射
			$m->where ( "auction_uid=%d", $uid )->delete ();
			
			// 删除图片
			unlink ( $_SERVER ['DOCUMENT_ROOT'] . __ROOT__ . "/Uploads/" . $r ["photo_url"] );
			$this->redirect ( "Admin/Auction/auctionList" );
		} else {
			$this->error ( "删除失败！" );
		}
	}
	public function updateAuction() {
		$m = M ( "Auction" );
		$data ["title"] = $_POST ["title"];
		$data ["start_date"] = $_POST ["start_date"];
		$data ["end_date"] = $_POST ["end_date"];
		$data ["description"] = $_POST ["description"];
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
		$this->redirect ( "Admin/Auction/auctionList" );
	}
	private function getAuction($uid) {
		$m = M ( "Auction" );
		$r = $m->where ( "uid=%d", $uid )->find ();
		$this->assign ( "record", $r );
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
	public function selectAuctionGoods($uid) {
		// 选择条件：
		// 未被关联的
		// 状态 = 可拍卖，流拍
		$m = M ( "AuctionGoodsMap" );
		$r = $m->where ( "auction_uid=%d", $uid )->select ();
		$goods_uids = "";
		foreach ( $r as $rr ) {
			$goods_uids .= $rr ["auction_goods_uid"] . ","; // 未被关联的
		}
		
		$m = M ( "AuctionGoods" );
		$where ["status"] = array (
				"IN",
				"0,3" 
		); // 可拍卖，流拍
		if ($goods_uids != "") {
			$where ["uid"] = array (
					"not in",
					$goods_uids 
			);
		}
		
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$list = $m->where ( $where )->order ( "update_time desc" )->limit ( $limit )->select ();
		$this->assign ( "list", $list );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		
		$this->assign ( "auction_uid", $uid );
		
		// 为了在列表把category的数字ID转换成文字
		self::getProductCategory ();
		$this->display ();
	}
	public function linkAuctionGoods() {
		if (isset ( $_POST ["goodsuid"] )) {
			$goodsUids = ($_POST ["goodsuid"]);
			// dump($goodsUids);
			$auction_uid = $_POST ["auction_uid"];
			$mAuction = M ( "Auction" );
			$rAuction = $mAuction->where ( "uid=%d", $auction_uid )->find ();
			// dump($rAuction);
			$m = M ( "AuctionGoodsMap" );
			$mGoods = M ( "AuctionGoods" );
			foreach ( $goodsUids as $goodsUid ) {
				$data ["auction_uid"] = $auction_uid;
				$data ["auction_goods_uid"] = $goodsUid;
				// 关联物品的起始时间默认为拍卖活动开始日期的零点，可手动更改
				$data ["begin_time"] = $rAuction ["start_date"] . " 00:00:00";
				// 关联物品的结束时间默认为拍卖活动结束日期的24点，可手动更改
				$data ["end_time"] = $rAuction ["end_date"] . " 23:59:59";
				// 关联物品的起始价格默认为物品的价格，可手动更改
				$rGoods = $mGoods->where ( "uid=%d", $goodsUid )->find ();
				// dump($rGoods);
				$data ["begin_price"] = $rGoods ["price"];
				// 关联物品的最终价格默认为0，可手动更改
				$data ["end_price"] = 0;
				// 物品在该拍卖活动的状态为“拍卖中”
				$data ["auction_status"] = 1;
				// dump($data);
				$m->add ( $data );
				
				// 同时要更新物品的状态为“拍卖中”
				$goodsData ["status"] = 1;
				$mGoods->where ( "uid=%d", $goodsUid )->save ( $goodsData );
			}
			$this->success ( "选择物品成功！" );
		} else {
			$this->error ( "请选择物品！" );
		}
	}

	public function detachAuctionGoods($auction_uid, $auction_goods_uid) {
		$m = M ( "AuctionGoodsMap" );
		$m->where ( "auction_uid=%d and auction_goods_uid=%d", $auction_uid, $auction_goods_uid )->delete ();
		// 恢复物品状态为可拍卖
		$m = M ( "AuctionGoods" );
		$data ["status"] = 0;
		$m->where ( "uid=%d", $auction_goods_uid )->save ( $data );
		$this->redirect ( "Admin/Auction/viewAuction/uid/" . $auction_uid );
	}
	public function editLinkedGoodsIn($auction_uid, $auction_goods_uid) {
		$m = M ( "AuctionGoodsMap" );
		$where ["auction_uid"] = $auction_uid;
		$where ["auction_goods_uid"] = $auction_goods_uid;
		$join = "think_auction_goods on think_auction_goods.uid = think_auction_goods_map.auction_goods_uid";
		$r = $m->join ( $join )->where ( $where )->find ();
		$this->assign ( "record", $r );
		
		// 为了在列表把category的数字ID转换成文字
		self::getProductCategory ();
		$this->assign ( "goodsStatusList", C ( "goodsStatus" ) );
		//dump($r);
		$this->display();
	}
	public function updateLinkedGoods() {
		$data["auction_status"] = $_POST ["auction_status"];
		$data["begin_time"] = $_POST ["begin_time"];
		$data["end_time"] = $_POST ["end_time"];
		$data["begin_price"] = $_POST ["begin_price"];
		$data["end_price"] = $_POST ["end_price"];		
		$m = M ( "AuctionGoodsMap" );
		$where ["auction_uid"] = $_POST ["auction_uid"];
		$where ["auction_goods_uid"] = $_POST ["auction_goods_uid"];
		$m->where($where)->save($data);
		
		$m = M ( "AuctionGoods" );
		$data["winner"] = $_POST ["winner"];
		$m->where("uid=%d",$_POST ["auction_goods_uid"])->save($data);
		$this->redirect ( "Admin/Auction/viewAuction/uid/" . $_POST ["auction_uid"] );
		//dump($data);
	}
}