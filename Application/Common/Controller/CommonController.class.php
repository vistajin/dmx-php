<?php

namespace Common\Controller;

use Think\Controller;

class CommonController extends Controller {
	public function getHomeMenu() {
		$menu = M ( "HomeMenu" );
		$firstLevelMenus = $menu->where ( "parent_uid is null and isShow = 1" )->order ( "seq" )->select ();
		foreach ( $firstLevelMenus as &$firstLevelMenu ) {
			$secondLevelMenus = $menu->where ( "parent_uid = %d", $firstLevelMenu ["uid"] )->order ( "seq" )->select ();
			if ($secondLevelMenus != null) {
				$firstLevelMenu ["second_level_menus"] = $secondLevelMenus;
			}
		}
		$this->assign ( "firstLevelMenus", $firstLevelMenus );
	}
	public function getHomeSlide() {
		$homeSlide = M ( "HomeSlide" );
		$homeSlideList = $homeSlide->select ();
		$this->assign ( "slidelist", $homeSlideList );
	}
	public function getProductCategory() {
		$prodCat = M ( "ProductCategory" );
		$firstLvls = $prodCat->where ( "parent_uid = 0" )->select (); // 第一层父ID=0
		
		foreach ( $firstLvls as &$firstLvl ) {
			$secondLvls = $prodCat->where ( "parent_uid = %d", $firstLvl ["uid"] )->order ( "uid" )->select ();
			if ($secondLvls != null) {
				$firstLvl ["nodes"] = $secondLvls;
			}
		}
		return $firstLvls;
	}	
}