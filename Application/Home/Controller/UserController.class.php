<?php

namespace Home\Controller;

use Think\Controller;
use Think\Verify;

class UserController extends Controller {
	public function registerin() {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		
		$this->display ();
	}
	public function register() {
		if (IS_POST) {
			$code = $_POST ["code"];
			$v = new Verify ();
			if (! $v->check ( $code, "" )) {
				$this->error ( "验证码有误！" );
			}
			
			$m = M ( "User" );
			$data ["user_id"] = trim ( $_POST ["user_id"] );
			$data ["password"] = encrypt ( $_POST ["password"] );
			$data ["email"] = trim ( $_POST ["email"] );
			$data ["mobile"] = $_POST ["mobile"];
			$data ["register_time"] = date ( "Y-m-d H:i:s", time () );
			$data ["last_logon_time"] = date ( "Y-m-d H:i:s", time () );
			$m->add ( $data );
			
			$common = A ( "Common/Common" );
			$common->getHomeMenu ();
			$common->getHomeSlide ();
			session ( "user", trim ( $_POST ["user_id"] ) );
			$this->display ();
		} else {
			$this->error ( "非法访问！" );
		}
	}
	public function checkExist($field) {
		$m = M ( "User" );
		$result = $m->where ( $field . "='%s'", trim ( $_POST ["param"] ) )->find ();
		if ($result == NULL) {
			$data = array (
					"info" => "",
					"status" => "y" 
			);
		} else {
			$errMsg = array (
					"email" => "该邮箱已被注册！",
					"user_id" => "该用户名已被注册！",
					"mobile" => "该手机号码已被注册！" 
			);
			$data = array (
					"info" => $errMsg [$field],
					"status" => "n" 
			);
		}
		echo json_encode ( $data );
	}
	public function checkCode() {
		$code = $_POST ["param"];
		$v = new Verify ();
		if ($v->check ( $code, "" )) {
			$data = array (
					"info" => "",
					"status" => "y" 
			);
		} else {
			$data = array (
					"info" => "验证码不正确！",
					"status" => "n" 
			);
		}
		echo json_encode ( $data );
	}
	public function login() {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->display ();
	}
	public function logout() {
		session ( "user", null );
		$this->success ( "成功退出", U ( "Home/Index/index" ) );
	}
	public function userinfo() {
		$common = A ( "Common/Common" );
		$common->getHomeMenu ();
		$common->getHomeSlide ();
		$this->display ();
	}
	public function dologin() {
		if (IS_POST) {
			$code = $_POST ["code"];
			$v = new Verify ();
			if (! $v->check ( $code, "" )) {
				$this->error ( "验证码有误！" );
			}
			
			$m = M ( "User" );
			$user_id = $_POST ["user_id"];
			$password = encrypt ( $_POST ["password"] );
			$result = $m->where ( "user_id='%s' and password='%s'", $user_id, $password )->find ();
			if ($result == null) {
				$this->error ( "错误的用户名或密码！" );
			}
			$result ["last_logon_time"] = date ( "Y-m-d H:i:s", time () );
			$m->save ( $result );
			
			session ( "user", $_POST ["user_id"] );
			$this->redirect ( "Index/index" );
		} else {
			$this->index ();
		}
	}
}