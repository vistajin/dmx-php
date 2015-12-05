<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

class ProductController extends AuthController {
	public function getCategories() {
		$common = A ( "Common/Common" );
		$this->assign ( "tree", json_encode ( $common->getProductCategory () ) );
		$this->display ();
	}
	public function addCategoryInput($uid) {
		$this->assign ( "parent_uid", $uid );
		$this->display ();
	}
	public function updateCategoryInput($uid) {
		$m = M ( "ProductCategory" );
		$r = $m->where ( "uid = %d", $uid )->find ();
		$this->assign ( "data", $r );
		$this->display ();
	}
	public function addCategory($parent_uid, $text) {
		$m = M ( "ProductCategory" );
		$data ["parent_uid"] = $parent_uid;
		$data ["text"] = $text;
		$m->add ( $data );
		$this->redirect ( "Admin/Product/getCategories" );
	}
	public function updateCategory($uid, $text) {
		$m = M ( "ProductCategory" );
		$data ["text"] = $text;
		$m->where ( "uid = %d", $uid )->save ( $data );
		$this->redirect ( "Admin/Product/getCategories" );
	}
	public function delCategory($uid) {
		$m = M ( "Product" );
		$r = $m->where ( "category_uid = %d", $uid )->find ();
		if ($r == NULL) {
			$m = M ( "ProductCategory" );
			$m->where ( "uid = %d", $uid )->delete ();
			$this->redirect ( "Admin/Product/getCategories" );
		} else {
			$this->error ( "请先删除该产品类别下的产品再删除该产品类别！" );
		}
	}
	public function productList($category_uid = null, $productName = null) {
		$product = M ( "Product" );
		$where = null;
		if ($category_uid != null) {
			$where ["category_uid"] = $category_uid;
		}
		if ($productName != null) {
			$where ["think_product.text"] = array (
					"LIKE",
					"%" . $productName . "%" 
			);
		}
		$Page = getpage ( $product, $where, C ( "LIST_PAGE_SIZE" ) );
		
		$joinStr = "think_product_category on think_product_category.uid = think_product.category_uid";
		$fields = "think_product.uid as uid, think_product.text as product_name, think_product_category.text as category_name,";
		$fields .= "think_product.modified_time as modified_time,";
		$fields .= "think_product_category.uid as category_uid";
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$productList = $product->join ( $joinStr )->where ( $where )->field ( $fields )->limit ( $limit )->select ();
		$this->assign ( "list", $productList );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		$this->assign ( "productName", $productName );
		$this->display ();
		// dump ( $where );
	}
}