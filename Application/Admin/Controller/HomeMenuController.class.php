<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

class HomeMenuController extends AuthController {
	public function getHomeMenuListData() {
		$menu = M("HomeMenu");
		$firstLevelMenus = $menu->where("parent_uid is null")->order("seq")->select();
		foreach ($firstLevelMenus as &$firstLevelMenu) {
			$secondLevelMenus = $menu->where("parent_uid = %d", $firstLevelMenu["uid"])->order("seq")->select();			
			if ($secondLevelMenus != null) {
				$firstLevelMenu["nodes"] = $secondLevelMenus;
			}
		}
		//$this->assign ( "firstLevelMenus", $firstLevelMenus );
// 		$this->assign ( "rtlist", $r );
// 		$this->assign ( "category", $category );
// 		$this->assign ( "categoryText", C ( $category ) );
// 		$this->display ();

		$data['text']  = "Home";
// 		$parent[""]
		
		//$notes[]
		$this->ajaxReturn($firstLevelMenus);
	}
	
	public function homeMenuList() {
		$this->display();
	}
	
	//{"status":1,"content":"content"}
	
// 	var tree = [{
// 		"text": "首页"
// 	},{
// 		text: "关于我们",
// 		nodes: [{
// 			text: "企业介绍"
// 		},{
// 			text: "联系我们"
// 		}
// 		]
// 	},{
// 		text: "新闻动态"
// 	},{
// 		text: "Parent 4"
// 	},{
// 		text: "Parent 5"
// 	}
// 		];
}