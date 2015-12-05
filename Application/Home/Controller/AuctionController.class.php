<?php

namespace Home\Controller;

use Think\Controller;
use Think\Verify;

class AuctionController extends Controller {
	public function latestAuctionList() {
// 		$today = date ( "Y-m-d", time () );
// 		$where ["start_date"] = array (
// 				"EGT",
// 				$today 
// 		); // >= today
		
		$order = "start_date desc";
		$this->assign ( "latestActive", "active" );
		session ( "latestActive", "active" );
		session ( "historyActive", null );
		self::auctionList ( "latestAuction", $where, $order );
	}
	public function historyAuctionList() {
		$today = date ( "Y-m-d", time () );
		$where ["start_date"] = array (
				"LT",
				$today 
		);
		$order = "start_date desc";
		session ( "latestActive", null );
		session ( "historyActive", "active" );
		self::auctionList ( "historyAuction", $where, $order );
	}
	private function auctionList($category, $where, $order) {
		$this->assign ( "subtitle", C ( $category ) );
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		
		$m = M ( "Auction" );
		$Page = getpage ( $m, $where, 5);// C ( "LIST_PAGE_SIZE" ) );
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$list = $m->where ( $where )->limit ( $limit )->order ( $order )->select ();
		$show = $Page->show ();
		$this->assign ( "page", $show );
		
		// 获取拍卖物品
		$mm = M ( "AuctionGoodsMap" );
		foreach ( $list as &$auction ) {
			$auction_uid = $auction ["uid"];
			$goodsIDs = $mm->where ( "auction_uid=%d", $auction_uid )->field ( "auction_goods_uid" )->select ();
			if ($goodsIDs != null) {
				$ids = "";
				foreach ( $goodsIDs as $goodsId ) {
					$ids .= $goodsId ["auction_goods_uid"] . ",";
				}
				$w = null;
				if ($ids != "") {
					$w ["uid"] = array (
							"IN",
							$ids 
					);
				}
				
				$mGoods = M ( "AuctionGoods" );
				$goodsPhotos = $mGoods->where ( $w )->field ( "photo_url" )->select ();
				$auction ["goods_photo_urls"] = $goodsPhotos;
				// dump($auction["goods_photo_urls"]);
			}
		}
		// dump ( $list );
		
		$this->assign ( "auctionList", $list );
		
		$this->display ( "auctionList" );
	}
	public function viewAuction() {
		$uid = I("param.uid");
		if ($uid == null) {
			$uid = session ( "auction_id" );
		}
		
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		
		$m = M ( "Auction" );
		$r = $m->where ( "uid = %d", $uid )->find ();
		$this->assign ( "record", $r );
		
		if (session ( "latestActive" ) == null) {
			$this->assign ( "subtitle", C ( "historyAuction" ) );
		} else {
			$this->assign ( "subtitle", C ( "latestAuction" ) );
		}
		self::getAuctionGoods ( $uid );
		self::getAuctionMessage ( $uid );
		session ( "auction_id", $uid );
		$this->display ();
	}
	private function getAuctionMessage($uid) {
		$m = M ( "AuctionMessage" );
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$list = $m->join ( "left join think_auction_goods on think_auction_goods.uid = think_auction_message.goods_uid " )->where ( "auction_uid=%d", $uid )->limit ( $limit )->order ( "post_time desc" )->select ();
		$show = $Page->show ();
		$this->assign ( "pageMessage", $show );
		$this->assign ( "messageList", $list );
	}
	private function getAuctionGoodsMessage($uid) {
		$m = M ( "AuctionMessage" );
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$list = $m->where ( "goods_uid=%d", $uid )->limit ( $limit )->order ( "post_time desc" )->select ();
		$show = $Page->show ();
		$this->assign ( "page", $show );
		$this->assign ( "messageList", $list );
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
		
		$this->assign ( "auction_uid", $uid );
		
		// 为了在列表把category的数字ID转换成文字
		self::getProductCategory ();
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
	public function viewAuctionGoods($uid) {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		
		// self::getProductCategory ();
		self::getAuctionGoodsMessage ( $uid );
		$m = M ( "AuctionGoodsMap" );
		$where ["auction_uid"] = session ( "auction_id" );
		$where ["auction_goods_uid"] = $uid;
		$join = "think_auction_goods on think_auction_goods.uid = think_auction_goods_map.auction_goods_uid";
		$r = $m->join ( $join )->where ( $where )->find ();
		$r["description"] = str_replace("<img ", "<img class=\"carousel-inner img-responsive\"", $r["description"]);
		$this->assign ( "record", $r );
		$this->display ();
	}
	public function addAuctionMsg() {
		if (IS_POST) {
			$code = $_POST ["code"];
			$v = new Verify ();
			if (! $v->check ( $code, "" )) {
				$this->error ( "验证码有误！" );
			}
			
			$data ["auction_uid"] = session ( "auction_id" );
			$data ["goods_uid"] = $_POST ["goods_uid"];
			$author = session ( "user" );
			
			if ($author == null) {
				$author = "游客";
				// 游客才需要显示IP及位置
				// 获取IP所在地
				if (isMobile()) {
					$data ["author_location"] = "手机用户";
				} else {
					$ipLocation = new \Org\Net\IpLocation ( 'QQWry.dat' ); // 实例化类 参数表示IP地址库文件
					$ip = get_client_ip ();
					$data ["author_ip"] = $ip;
					$area = $ipLocation->getlocation ( $ip ); // '203.34.5.66'); // 获取某个IP地址所在的位置
					$author_location = iconv ( 'gbk', 'utf-8', $area ['country'] . $area ['area'] );
					$data ["author_location"] = $author_location;
				}
			}
			$data ["author"] = $author;
			
			$data ["post_time"] = date ( "Y-m-d H:i:s", time () );
			$data ["message"] = $_POST ["message"];
			
			$m = M ( "AuctionMessage" );
			$m->add ( $data );
			
			if ($_POST ["goods_uid"] == null) {
				$this->redirect ( "Home/Auction/viewAuction/uid/" . session ( "auction_id" ) );
			} else {
				$this->redirect ( "Home/Auction/viewAuctionGoods/uid/" . $_POST ["goods_uid"] );
			}
		} else {
			$this->error ( "非法访问！" );
		}
	}
}