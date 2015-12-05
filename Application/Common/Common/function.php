<?php
function getpage(&$m, $where = "", $psize = 10) {
	$m1 = clone $m; // 浅复制一个模型
	$count = $m->where ( $where )->count (); // 连惯操作后会对join等操作进行重置
	$m = $m1; // 为保持在为定的连惯操作，浅复制一个模型
	$p = new Think\Page ( $count, $psize );
	$p->lastSuffix = false;
	
	$p->setConfig ( 'prev', '<span aria-hidden="true">上一页</span>' );
	$p->setConfig ( 'next', '<span aria-hidden="true">下一页</span>' );
	$p->setConfig ( 'first', '<span aria-hidden="true">首页</span>' );
	$p->setConfig ( 'last', '<span aria-hidden="true">尾页</span>' );
	$p->setConfig ( 'theme', '<li><a>共%TOTAL_ROW%条记录 第%NOW_PAGE%/%TOTAL_PAGE%页</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
	// $p->setConfig ( "theme", "%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%" );
	
	// $p->parameter = I ( "get." );
	
	$m->limit ( $p->firstRow, $p->listRows );
	
	return $p;
}
function encrypt($password) {
	return md5 ( crypt ( $password, substr ( $password, 0, 2 ) ) );
}
function loadFixedItem() {
	$common = A ( "Common/Common" );
	$common->getHomeMenu ();
	$common->getHomeSlide ();
}
function upload() {
	$upload = new \Think\Upload ();
	$upload->maxSize = 0; // 设置附件上传大小,0为不限大小
	$upload->exts = array (
			"jpg",
			"gif",
			"png",
			"jpeg",
			"zip" 
	);
	// $upload->rootPath = "./Public/Upload/"; // 设置附件上传根目录, 默认是./Uploads
	// $upload->subName = $savePath; // 设置附件上传（子）目录
	// $upload->saveName = ""; // 按原始文件名保存
	$info = $upload->upload (); // array($_FILES["photo"]));
	if ($info == false) {
		return array (
				false,
				$upload->getError () 
		);
	}
	return array (
			true,
			$info 
	);
}
function getAdsList($category) {
	$m = M ( "Ads" );
	$where ["isShow"] = 1;
	$where ["category"] = $category;
	$joinStr = "think_seller on think_seller.uid = think_ads.seller_uid";
	$fields = "think_seller.name as name, think_ads.isShow as isShow, ";
	$fields .= "think_ads.category as category, think_ads.seller_uid as uid, ";
	$fields .= "think_seller.logo as logo";
	return $m->join ( $joinStr )->where ( $where )->field ( $fields )->order ( "seq" )->select ();
}
function isMobile() {
	$useragent = isset ( $_SERVER ['HTTP_USER_AGENT'] ) ? $_SERVER ['HTTP_USER_AGENT'] : '';
	$useragent_commentsblock = preg_match ( '|\(.*?\)|', $useragent, $matches ) > 0 ? $matches [0] : '';
	
	$mobile_os_list = array (
			'Google Wireless Transcoder',
			'Windows CE',
			'WindowsCE',
			'Symbian',
			'Android',
			'armv6l',
			'armv5',
			'Mobile',
			'CentOS',
			'mowser',
			'AvantGo',
			'Opera Mobi',
			'J2ME/MIDP',
			'Smartphone',
			'Go.Web',
			'Palm',
			'iPAQ' 
	);
	$mobile_token_list = array (
			'Profile/MIDP',
			'Configuration/CLDC-',
			'160×160',
			'176×220',
			'240×240',
			'240×320',
			'320×240',
			'UP.Browser',
			'UP.Link',
			'SymbianOS',
			'PalmOS',
			'PocketPC',
			'SonyEricsson',
			'Nokia',
			'BlackBerry',
			'Vodafone',
			'BenQ',
			'Novarra-Vision',
			'Iris',
			'NetFront',
			'HTC_',
			'Xda_',
			'SAMSUNG-SGH',
			'Wapaka',
			'DoCoMo',
			'iPhone',
			'iPod' 
	);
	
	$found_mobile = _checkSubstrs ( $mobile_os_list, $useragent_commentsblock ) || _checkSubstrs ( $mobile_token_list, $useragent );
	
	if ($found_mobile) {
		return true;
	} else {
		return false;
	}
}

function _checkSubstrs($substrs, $text) {
	foreach ( $substrs as $substr )
		if (false !== strpos ( $text, $substr )) {
			return true;
		}
	return false;
}