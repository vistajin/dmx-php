<?php

namespace Home\Controller;

use Think\Controller;
use Think\Verify;

class MessageController extends Controller {
	public function inputmsg() {
		loadFixedItem();
		$this->assign ( "otherleftads", getAdsList ( "otherleftads" ) );
		$re = A ( "RichEdit" );
		// 公告 & 活动
		//$re->getList ( "announcement", "announcementList" );
		$re->getList ( "activity", "activityList" );
		$this->display ();
	}
	public function savemsg() {
		if (IS_POST) {
			$code = $_POST ["code"];
			$v = new Verify ();
			if (! $v->check ( $code, "" )) {
				$this->error ( "验证码有误！" );
			}
			
			$m = M ( "Message" );
			$data ["title"] = $_POST ["title"];
			$data ["content"] = $_POST ["content"];			
			$data ["contact_name"] = $_POST ["contact_name"];
			$data ["contact_phone"] = $_POST ["contact_phone"];
			$data ["email"] = trim ( $_POST ["email"] );
			$data ["submit_time"] = date ( "Y-m-d H:i:s", time () );
			$m->add ( $data );
			
			loadFixedItem();			
			$this->display ();
		} else {
			$this->error ( "非法访问！" );
		}
	}
}