<?php

namespace Home\Controller;

use Think\Controller;
use Think\Verify;

class CommonController extends Controller {
	public function genverify() {
		/*
		 * expire 验证码的有效期（秒）
		 * useImgBg 是否使用背景图片 默认为false
		 * fontSize 验证码字体大小（像素） 默认为25
		 * useCurve 是否使用混淆曲线 默认为true
		 * useNoise 是否添加杂点 默认为true
		 * imageW 验证码宽度 设置为0为自动计算
		 * imageH 验证码高度 设置为0为自动计算
		 * length 验证码位数
		 * fontttf 指定验证码字体 默认为随机获取
		 * useZh 是否使用中文验证码
		 * bg 验证码背景颜色 rgb数组设置，例如 array(243, 251, 254)
		 * seKey 验证码的加密密钥
		 * codeSet 验证码字符集合 3.2.1 新增
		 * zhSet 验证码字符集合（中文） 3.2.1 新增
		 */
		$config = array (
				"fontSize" => 18,
				"length" => 4,
				"imageH" => 34,
				"codeSet" => "1234567890",
				"useNoise" => false 
		)
		// 关闭验证码杂点
		// "userCurve" => false
		;
		$v = new Verify ( $config );
		$v->entry ();
	}
}