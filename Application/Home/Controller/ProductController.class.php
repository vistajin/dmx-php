<?php

namespace Home\Controller;

use Think\Controller;
use Admin\Controller\RichEditController;

class ProductController extends Controller {
	public function index() {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		$this->assign ( "tree", json_encode ( $common->getProductCategory () ) );
		
		$product = M ( "Product" );
		$productList = $product->join ( "think_product_image on think_product_image.product_uid = think_product.uid and think_product_image.isCover = 1" )->select ();
		$this->assign ( "productList", $productList );
		
		$this->display ();
		
		// dump ( $productList );
	}
	public function details($uid) {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->assign ( "tree", json_encode ( $common->getProductCategory () ) );
		
		$product = M ( "Product" );
		$productRecord = $product->where ( "uid = %d", $uid )->find ();
		$this->assign ( "productRecord", $productRecord );
		
		$product = M ( "ProductImage" );
		$imageList = $product->where ( "product_uid = %d", $uid )->order ( "isCover desc" )->select ();
		$this->assign ( "imageList", $imageList );
		
		$this->display ();
		//dump($productRecord);
		//dump($imageList);
	}
}