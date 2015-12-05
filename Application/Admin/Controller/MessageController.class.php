<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

class MessageController extends AuthController {
	public function msglist() {
		$m = M ( "Message" );
		$where = null;
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$msglist = $m->where ( $where )->order("submit_time desc")->limit ( $limit )->select ();
		$this->assign ( "list", $msglist );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		$this->display ();
	}
	
	public function viewmsg($uid) {
		$m = M ( "Message" );
		$r = $m->where("uid = %d", $uid)->find();
		$r["content"] = str_replace("\n", "<br>", $r["content"]);
		$this->assign ( "record", $r );
		$this->display ();
	}
	
	public function deleteMsg($uid) {
		$m = M ( "Message" );
		$r = $m->where("uid = %d", $uid)->delete();
		$this->redirect("msglist");
	}
}