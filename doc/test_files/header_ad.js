var random_number = Math.random();

if (random_number < 0.052) {
	// Lowrance Endura banner
	if (document.getElementById('header_ad')) {
		document.getElementById('header_ad').style.padding = '4px 0px 0px 0px';
	}
	document.write('<table cellspacing="0" cellpadding="0" border="0"><tr align="center" valign="middle">');
	document.write('<td bgcolor="#006600" width="12"><img src="/images/advertisement_10x90.gif" width="10" height="90"></td>');
	document.write('<td><a target="_blank" href="http://www.lowrance.com/Endura?X.src=gpsvisualizer">');
	document.write('<img src="/images/lowrance_endura_ad_728x90.jpg" width="728" height="90" border="2" alt="">');
	document.write('</a></td>');
	document.writeln('</tr></table>');
} else if (random_number < 0.50) {
	// EveryTrail iPhone Pro banner, topo background
	if (document.getElementById('header_ad')) {
		document.getElementById('header_ad').style.padding = '4px 0px 0px 0px';
	}
	document.write('<table cellspacing="0" cellpadding="0" border="0"><tr valign="middle">');
	document.write('<td bgcolor="#006600"><img src="/images/advertisement_10x90.gif" width="10" height="90"></td>');
	document.write('<td><a target="_blank" href="/misc/link.cgi?url=http%3A//itunes.apple.com/app/everytrail-pro/id353881166%3Fmt%3D8" rel="nofollow">');
	document.write('<img src="/images/everytrail_iphone_banner_offline2_728x90.jpg" width="728" height="90" border="2" alt="">');
	document.write('</a></td>');
	document.writeln('</tr></table>');
}

else { // random = 0.5 to 1
	// Google Adsense ads
	if (document.getElementById('header_ad')) {
		document.getElementById('header_ad').style.margin = '0px';
		document.getElementById('header_ad').style.height = '15px';
		document.getElementById('header_ad').style.backgroundColor = '#EEFFDD';
		document.getElementById('header_ad').style.borderTop = '1px solid #66CC66';
		document.getElementById('header_ad').style.borderBottom = '1px solid #66CC66';
	}
	document.writeln('<script type="text/javascript"><!--');
	document.writeln('google_ad_client = "pub-7605458219863413";');
	document.writeln('google_ad_width = 728;');
	document.writeln('google_ad_height = 15;');
	document.writeln('google_ad_format = "728x15_0ads_al_s";');
	document.writeln('google_ad_channel = "4924498378"; // 2007-07-04: gv-header-text-links');
	document.writeln('google_color_border = "EEFFDD";');
	document.writeln('google_color_bg = "EEFFDD";');
	document.writeln('google_color_link = "0000CC";');
	document.writeln('google_color_url = "004400";');
	document.writeln('google_color_text = "333333";');
	document.writeln('//--><'+'/'+'script>');
	document.writeln('<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"><'+'/'+'script>');
}
