function isMobile() {
	var browserName_ = navigator.userAgent;	
	return browserName_.indexOf("Mobile") > 0;
	/*if (browserName_.indexOf("iPad") < 0 && browserName_.indexOf("Windows NT") < 0
			&& browserName_.indexOf("Macintosh") < 0) {
		if (browserName_.indexOf("Linux") < 0) {
			if (browserName_.indexOf("Mobile") > 0
					|| browserName_.indexOf("U;") > 0) {
				// 手机浏览
			}
		} else {
		
		}
	}*/
}
