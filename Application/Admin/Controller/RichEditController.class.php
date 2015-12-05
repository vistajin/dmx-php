<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

class RichEditController extends AuthController {
	// 公司简介，联系信息等没有title的文本
	public function editSimpleRichText($category) {
		$m = M ( "RichText" );
		$r = $m->where ( "category = '%s'", $category )->find ();
		if ($r == NULL) {
			$this->assign ( "richText", "" );
		} else {
			$this->assign ( "richText", $r ["content"] );
		}
		$this->assign ( "category", $category );
		$this->assign ( "categoryText", C ( $category ) );
		$this->display ();
	}
	// 公司简介，联系信息等没有title的文本
	public function setSimpleRichText($category, $content) {
		$m = M ( "RichText" );
		$data ["content"] = $content;
		$r = $m->where ( "category = '%s'", $category )->find ();
		if ($r == NULL) {
			$data ["category"] = $category;
			$data ["modified_time"] = date ( "Y-m-d H:i:s", time () );
			$m->add ( $data );
		} else {
			$m->where ( "category = '%s'", $category )->save ( $data );
		}
		// $this->success("更新成功！");
		$this->redirect ( "Admin/RichEdit/editSimpleRichText/category/" . $category );
	}
	
	// 公告，活动，新闻等有标题的富文本
	public function getList($category) {
		$m = M ( "RichText" );
		$where ["category"] = $category;
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$list = $m->where ( $where )->limit ( $limit )->order ( "isShow desc, seq" )->select ();
		$show = $Page->show ();
		$this->assign ( "page", $show );
		$this->assign ( "rtlist", $list );
		$this->assign ( "category", $category );
		$this->assign ( "categoryText", C ( $category ) );
		$this->assign ( "nowPage", $Page->nowPage );
		$this->assign ( "totalPages", $Page->totalPages );
		$this->display ();
	}
	// 公告，活动，新闻等有标题的富文本
	public function createRichTextInput($category) {
		$this->assign ( "category", $category );
		$this->assign ( "categoryText", C ( $category ) );
		$this->display ();
	}
	// 公告，活动，新闻等有标题和日期的富文本
	public function addRichText() {
		$m = M ( "RichText" );
		
		//if ($_POST ["isShow"] == "1") {
			$r = $m->where ( "category='%s'", $_POST ["category"] )->field("max(seq) as seq")->find ();
			$data ["seq"] = intval($r["seq"]) + 1; // TODO $r["seq"] = NULL?
		//} else {
		//	$data ["seq"] = 0;
		//}
				
		$data ["category"] = $_POST ["category"];
		$data ["title"] = $_POST ["title"];
		$data ["thedate"] = $_POST ["thedate"];
		$data ["isShow"] = $_POST ["isShow"];
		
		$data ["content"] = $_POST ["content"];
		$data ["modified_time"] = date ( "Y-m-d H:i:s", time () );
		$m->add ( $data );
		
		$this->redirect ( "Admin/RichEdit/getList/category/" . $data ["category"] );
	}
	public function editRichTextInput($uid) {
		$m = M ( "RichText" );
		$result = $m->where ( "uid = %d", $uid )->find ();
		$this->assign ( "record", $result );
		$this->assign ( "categoryText", C ( $result ["category"] ) );
		
		$this->display ();
	}
	public function updateRichText($uid, $title, $content, $category, $thedate) {
		$m = M ( "RichText" );
		$data ["title"] = $title;
		$data ["isShow"] = $_POST ["isShow"];
		$data ["thedate"] = $_POST ["thedate"];
		$data ["content"] = $content;
		$m->where ( "uid = %d", $uid )->save ( $data );
		$this->redirect ( "Admin/RichEdit/getList/category/" . $category );
	}
	public function deleteRichText($uid, $category) {
		$data = M ( "RichText" );
		$result = $data->where ( "uid = %d", $uid )->delete ();
		if ($result === 1) {
			$this->redirect ( "Admin/RichEdit/getList/category/" . $category );
		} else {
			echo "删除" . C ( $category ) . "失败！";
		}
	}
	public function copyRichText($uid, $category) {
		$m = M ( "RichText" );
		$r = $m->where ( "uid = %d", $uid )->find ();
		unset ( $r ["uid"] );
		$r ["isShow"] = 1;
		$r ["modified_time"] = date ( "Y-m-d H:i:s", time () );
		$m->add ( $r );
		$this->success("更新成功！");
		//$this->redirect ( "Admin/RichEdit/getList/category/" . $category );
	}
	public function upRichText($uid, $category) {
		self::moveOrder($uid, $category, "up");
	}
	public function downRichText($uid, $category) {
		self::moveOrder($uid, $category, "down");
	}
	
	private function moveOrder($uid, $category, $upOrDown) {
		if ($upOrDown == "up") {
			$comp = "<";
			$fun = "max";
			$err = "该记录已经是第一条记录！";
		} else {
			$comp = ">";
			$fun = "min";
			$err = "该记录已经是最后一条可显示记录！";
		}
		$m = M ( "RichText" );
		$r = $m->where ( "uid = %d", $uid )->find ();
		$rr = $m->where ( "isShow = 1 and category = '%s' and seq ".$comp." %d", $category, $r ["seq"] )
		->field ( $fun."(seq) as seq" )->find ();
		
		if ($rr["seq"] != null) {
			$seq1 = $r ["seq"];
			$seq2 = $rr ["seq"];
		
			$r2 = $m->where ( "isShow = 1 and category = '%s' and seq = %d", $category, $seq2 )->field ( "uid" )->find ();
			$uid2 = $r2 ["uid"];
		
			$r ["seq"] = $seq2;
			$m->where ( "uid = %d", $uid )->save ( $r );

			$r2 ["seq"] = $seq1;
			$m->where ( "uid = %d", $uid2 )->save ( $r2 );
		
			$this->redirect ( "Admin/RichEdit/getList/category/" . $category );
		} else {
			$this->error ( $err );
		}
	}
}