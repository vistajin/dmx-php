<?php

function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

function test() {
	$this->assign ( "richText", "asdf" );
	echo "#############test###########";
}