(function($){
	var rangineMessageBox;
	$('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', ranginedata.cssUrl+'styles.css') );
	var defaultMessage = '<p class="p-1">برای استفاده از این درگاه نیاز به یک پنل پیامکی با قابلیت ارسال وب سرویس در سامانه پیام کوتاه رنگینه دارید.</p><p class="p-2">جهت مشاهده عملکرد این درگاه می توانید در کادر نام کاربری و رمز و شماره فرستنده این درگاه عبارت "demo" را درج کنید یا <a class="demotest">اینجا کلیک کنید</a>.</p><p class="p-3">برای تهیه پنل پیام کوتاه رنگینه به آدرس <a href="https://rangine.ir/help" target="_blank">rangine.ir/help</a> مراجعه و طبق راهنما ثبت نام نمایید. اقتصادی ترین نوع پنل <b>«وب سرویس - مختص افزونه ها»</b> می باشد.</p><p>برای ارسال سریع پیامک گزینه ارسال بر اساس الگو را فعال نمایید.</p><p><a href="https://rangine.ir/digits" target="_blank">راهنمای تنظیم دیجیتس برای رنگینه</a></p>';
	$('#rangine_garantee').parent().append('<p>با خرید سرویس ضمانت ارسال پیامک، در صورتی که به هر علت سامانه پیامک رنگینه پاسخ نداد و ارسال نکرد، بلافاصله پیامک شما با شیوه دیگری ارسال خواهد شد. برای فعال سازی این سرویس با پشتیبان سامانه پیامک رنگینه تماس بگیرید. لازم به ذکر است امکانات سامانه پیامک رنگینه و دیگر سامانه ها پیامکی ارتباطی با فروشنده دیجیتس از جهت کارایی و مالی ندارد</p>');
	function demotest(){
		$('.demotest').click(function(){
			$('#rangine_username, #rangine_password, #rangine_sender').val('demo').change();
			enableSave();
		});
	}
	function rangineCheckUser(service, apikey, user,pass,sender,rangineMessageBox) {

		if(service == 'apikey') return false;
		jQuery.ajax({
			type: 'post',
			async: true,
			url: digsetobj.ajax_url,
			data: {
				action: 'digits_rangine_auth',
				serviceType: service,
				apikey: apikey,
				uname: user,
				pass: pass,
				sender: sender,
			},
			beforeSend: function(){
				rangineMessageBox.find('td .loading').remove();
				rangineMessageBox.find('td').prepend('<div class="loading"><img src="' + ranginedata.cssUrl + 'loader.gif"/> در حال بررسی وضعیت درگاه<span style="">...</span></div>');
			},
			success: function (res) {
				if(user.toLowerCase() == 'demo'){
					var remainDemo = 30 - res;
					message = '<p style="color:red">تعداد پیامک مجاز باقیمانده در حالت دمو: '+remainDemo+' - <a href="https://rangine.ir/digits" target="_blank">تهیه پنل و ایجاد الگوی پیامک اختصاصی</a></p>';
					rangineMessageBox.find('td').html(defaultMessage + message);
				} else {
					var obj = JSON.parse(res);
					var message = '';				
					if(obj['auth'] == true) {	
						if(obj['credit'] < 20000 && obj['credit'] > 10000){
							var creditbgColor = '#ffcf7b';
						} else if (obj['credit'] < 8000){
							var creditbgColor = '#ff7b7b';
						} else {
							var creditbgColor = '#baddba';
						}
						message = '<p><a href="https://rangine.ir/digits" target="_blank">راهنمای تنظیم دیجیتس برای رنگینه</a></p><span class="credit" style="background:'+creditbgColor+'">اعتبار پنل پیامک : '+obj['credit']+' ریال</span>';
						if(obj['ownSenderLine'] == false) {
						message += '<p style="color:red">شما مجوز استفاده از خط ارسال کننده '+sender+' را ندارید.( با پشتیبان سامانه پیامک رنگینه تماس بگیرید)</p>';
						}
						rangineMessageBox.find('td').html(message);
					}else{
						message += '<p style="color:red">نام کاربری و یا رمز عبور وارد شده درست نمی باشد.</p>';
						rangineMessageBox.find('td').html(defaultMessage + message);
						demotest();
					}
				}
			},
			complete: function(){
				rangineMessageBox.find('td .loader').hide();
			},
		});

		return false;
	}
	function rangineShowMessage(title,message,footer){
		$('body').append('<div id="rmWrapper"><div class="rmTitle">'+title+'<span class="close">X</span></div><div class="rmBody">'+message+'</div><div class="rmFooter">'+footer+'<button class="close button-secondary">بستن</button></div></div>');
		$('#rmWrapper .close').click(function(){
			$('#rmWrapper').remove();
		})
	}
	function usercheck(elm,rangineMessageBox){
		var gatewaytable = elm.parents('table.form-table').parent();
		if(gatewaytable.find('#rangine_username').length){
			var ranginewebservicetype = gatewaytable.find('#rangine_webservicetype').val();
			var rangineapikey = gatewaytable.find('#rangine_apikey').val();
			var rangineusername = gatewaytable.find('#rangine_username').val();
			var ranginepassword = gatewaytable.find('#rangine_password').val();
			var ranginesender = gatewaytable.find('#rangine_sender').val();
			if((rangineusername.toLowerCase() == 'demo' && ranginepassword.toLowerCase() == 'demo' ) || (rangineusername == 'دمو' && ranginepassword == 'دمو' )) {
				var panelMessage = "شما در حال استفاده از دموی سامانه رنگینه هستید. استفاده از دمو محدودیت هایی در تعداد کل ارسال و تعداد ارسال در دقیقه دارد.";
				rangineMessageBox.find('td').html(defaultMessage);
				rangineMessageBox.find('.p-2').html(panelMessage).css('color','red');
				rangineCheckUser(rangineusername,ranginepassword,ranginesender,rangineMessageBox);
			} else if(rangineapikey != '' || (rangineusername != '' && ranginepassword != '')) {
				rangineCheckUser(ranginewebservicetype, rangineapikey,rangineusername,ranginepassword,ranginesender,rangineMessageBox);
			} else {
				rangineMessageBox.find('td').html(defaultMessage);
				demotest();
			}
		}
	}

	$(document).ready(function(){
		$('.ippanel_patterncode_wrapper label,.farazsms_patterncode_wrapper label,.modirpayam_patterncode_wrapper label,.jahanbartar_patterncode_wrapper label,.modirpayamak_com_patterncode_wrapper label,.onlinsms_patterncode_wrapper label,.novinweb_patterncode_wrapper label,.shahvarpayam_patterncode_wrapper label,.samanepayamak_patterncode_wrapper label,.popupsms_patterncode_wrapper label,.payamnam_patterncode_wrapper label,.arashpayamak_patterncode_wrapper label,.hamkaransms_patterncode_wrapper label,.mrpayamak_patterncode_wrapper label,.artapayamak_patterncode_wrapper label').after('<a class="ippanel-patternlearn">آموزش ثبت پترن(الگو)</a>');
		$('.ippanel-patternlearn').on('click',function(){
			var patternLearnText = '<p>	برای ارسال سریع پیامک با خطوط خدماتی از این سامانه، نیاز به ثبت الگو یا پترن دارید. جهت ایجاد الگو و ثبت آن در این افزونه از دستورالعمل زیر استفاده نمایید.</p><p>	وارد پنل پیامک خود شوید و در منوی پنل گزینه ارسال بر اساس پترن را کلیک کنید.</p><p>	در صفحه ظاهر شده روی دکمه + جدید کلیک کنید.</p><p>	مانند متن زیر یک الگو را تایپ کرده و دکمه ثبت را کلیک نمایید:</p><p>	<strong>کد تأیید شما: %code%<br />	فروشگاه ابزار خودرو</strong></p><p>	(حتما نام فروشگاه خود را ثبت کنید)</p><p>در کادر توضیحات آدرس سایت خود را ثبت نمایید. و در کادر جداکننده علامت % را انتخاب کنید. سپس در کادر متغیرهای پترن تنها کلمه code (همان متغیری که در پترن دارید) را ثبت کنید. نوع متغیر را عدد تنظیم     نمایید</p><p>	بعد از ثبت موفق الگو منتظر بمانید تا حداکثر 1 ساعت بعد الگوی شما تایید شود. وضعیت تایید یا رد الگوی خود را در همان صفحه پنل پیامکی خود می توانید مشاهده نمایید.&nbsp;</p><p>	پس از اینکه الگو تایید و فعال شد، تنظیمات درگاه افزونه دیجیتس (یعنی همین صفحه) را به شکل زیر انجام دهید:</p><p>	گزینه &laquo;ارسال بر اساس الگو&raquo; را روی بله تنظیم نمایید.</p><p>	در کادر &laquo;کد الگو&raquo; کد پترن ایجاد شده خود که در صفحه ارسال بر اساس پترن سامانه پیامک نمایش داده می شود را وارد کنید</p><p>	در کادر &laquo;متغیرهای الگو&raquo; متغیر موجود در پترن خود را نوشته و بعد از : {OTP} را وارد کنید. در مثال متن الگوی بالا به این شکل وارد می کنیم:&nbsp;</p><p dir="ltr" style="text-align: center;">	<strong>code:{OTP}</strong></p><p>	سپس دکمه ذخیره تغییرات را کلیک کنید.</p>';
			rangineShowMessage('آموزش ثبت پترن(الگو)',patternLearnText,'در صورت نیاز به راهنمایی بیشتر با پشتیبانی سامانه پیامک خود تماس بگیرید.')
		});
		$('.rangine_patternvars_wrapper label').after('<a class="rangine-patternlearn">آموزش ثبت پترن(الگو)</a><br><a class="rangine-patterntool">ابزار تنظیم پترن در دیجیتس</a>');
		$('.rangine-patternlearn').on('click',function(){
			var patternLearnText = '<p>	سامانه رنگینه برای فراهم نمودن ارسال سریع کد تایید با خطوط خدماتی، سیستم ارسال بر اساس الگو(پترن) را پیشنهاد می کند. شما در درگاه رنگینه افزونه دیجیتس دو انتخاب برای ارسال پیامک احراز هویت دارید:</p><p>	1- ارسال از طریق الگو: برای استفاده از این شیوه گزینه &laquo;ارسال از طریق الگو&raquo; را روی <strong>بله </strong>تنظیم کرده و بر اساس دستورالعملی که در ادامه می آید الگوی خود را ثبت نمایید.</p><p>	2- ارسال پیامک به صورت معمولی بدون الگو: برای استفاده از این شیوه گزینه &laquo;ارسال از طریق الگو&raquo; را روی&nbsp;<strong>خیر&nbsp;</strong>تنظیم کرده و متنی که در کادر &laquo;نمونه متن&raquo; تایپ می کنید پیامک خواهد شد. این نوع ارسال از خطوط خدماتی اشتراکی سامانه با تاخیر انجام می گیرد و پیشنهاد نمی شود.</p><p><strong><a href="https://rangine.ir/pattern" target="_blank">راهنمای ثبت الگوی اختصاصی</a></strong></p><p>با مراجعه به صفحه بالا الگو یا پترن بسازید (فیلم آموزشی: <a href="https://rangine.ir/digits" target="_blank">rangine.ir/digits</a>) و پس از اینکه الگو تایید و فعال شد، تنظیمات درگاه افزونه دیجیتس (یعنی همین صفحه) را به شکل زیر انجام دهید:</p><p>	گزینه &laquo;<label for="rangine_sample">&nbsp;ارسال بر اساس الگو</label>&raquo; را روی <strong>بله </strong>تنظیم نمایید.</p><p>روی لینک ابزار تنظیم پترن کلیک کنید و طبق راهنمای آن کد پترن و متغیرهایش را در کادرهای مربوطه وارد نمایید.</p><p>	سپس دکمه ذخیره تغییرات را کلیک کنید.</p>';
			rangineShowMessage('آموزش ثبت پترن(الگو)',patternLearnText,'در صورت نیاز به راهنمایی بیشتر با پشتیبانی سامانه پیامک خود تماس بگیرید.')
		});
		$('.rangine-patterntool').on('click',function(){
			var patternToolText = 'پس از ثبت پترن در سامانه پیامک رنگینه و تأیید آن، کد پترن را در کادر زیر وارد نمایید:<div id="onlinepattern"><label>کد پترن:</label> <input class="patterncodeinput" placeholder="کد الگوی شما" type="text"><div class="pattern-message"></div><div><button class="onlinepcodechecker" type="button">بررسی الگو</button></div><div class="pcodecheckresult"></div><div class="variableshelp hidden">متغیر کد تأیید: {OTP}</div><div><button class="patterninsert hidden" type="button">ثبت الگو در کادر پیامک</button></div></div><br><div><b>راهنمایی:</b><p class="ranginepatternstephelp"> متنی مانند زیر را در سامانه پیامک رنگینه > صفحه ارسال بر اساس پترن ایجاد کنید:<br><br>کد تأیید شما: %code%<br>نام فروشگاه شما<br>(<a href="https://rangine.ir/pattern" target="_blank">راهنمای ثبت الگوی اختصاصی</a>)<br>پس از مدتی که پترن شما تأیید شد، کد پترن را از همان صفحه ارسال بر اساس پترن کپی و در کادر فوق درج نمایید.</p></div>';
			rangineShowMessage('ابزار تنظیم پترن',patternToolText,'درگاه سامانه پیامک رنگینه');
			
			$('.onlinepcodechecker').on('click',function(){
				if($("#rangine_username").val().toLowerCase() == 'demo'){
					$('#onlinepattern .pattern-message').html('امکان بررسی پترن با نام کاربری demo وجود ندارد. ابتدا نام کاربری و رمز عبور تنظیمات درگاه دیجیتس را اصلاح کنید.');
					return false;
				}
				pcode = $('#onlinepattern .patterncodeinput').val();
				jQuery.ajax({
					type: 'post',
					async: true,
					url: digsetobj.ajax_url,
					data: {
						action: 'digits_rangine_CheckPattern',
						uname: $("#rangine_username").val(),
						pass: $("#rangine_password").val(),
						patternCode: pcode,
					},
					beforeSend: function(){
						$('#onlinepattern .pattern-message').html('<span class="process-icon-loading">در حال بررسی...</span>');
					},
					success: function (json) {
						var obj = JSON.parse(json);
						var message = '';				
							  if(obj.status.code == 0){
								$('#onlinepattern .pattern-message').html(obj.data.patternMessage.replace("\n","<br>"));
								var pvars = obj.data.patternParams;
								var output = '<p>لطفاً پارامترهای پترن را با متغیرهای سایت تکمیل نمایید.</p>';
								pvars.forEach(function(value, index, array){
									output += '<div><label>'+value+'</label><input type="text"/></div>';
								});
								$('.pcodecheckresult').html(output);
								$('.variableshelp,.patterninsert').removeClass('hidden');
								$('.onlinepcodechecker').addClass('hidden');
								$('.ranginepatternstephelp').html('حالا پارامترهای پترن خود را در کادرهای ظاهر شده تکمیل نمایید. از متغیر {OTP} برای جایگزینی با کد تأیید موبایل استفاده کنید.');
								var patternoutput = '';
								$('.patterninsert').click(function(){
									$('#rangine_sample').val(1);
									$('.pcodecheckresult div').each(function(){
										if(patternoutput !== '') patternoutput += "\n";
										patternoutput += $(this).find('label').text()+':'+$(this).find('input').val();
									})
									$('#rangine_patterncode').val(pcode);
									$('#rangine_patternvars').html(patternoutput);
									$('#rangine_patternvars').val(patternoutput);
									$('#rmWrapper').remove();
								});
							  }else if(obj.status.code == 404){
								  $('#onlinepattern .pattern-message').html('پترن در دسترس نیست. مطمئن شوید که کد پترن را درست و بدون فاصله در چپ و راستش درج کرده اید. اگر به تازگی ثبت کرده اید منتظر باشید تا توسط سامانه تایید شود.');
							  }else if(obj.status.code == 962){
								  $('#onlinepattern .pattern-message').html('نام کاربری یا رمز عبور اشتباه است. اطلاعات ورود پنل پیامکتان را درست وارد کنید.');
							  }else{
								  $('#onlinepattern .pattern-message').html('این الگو در دسترس شما نیست. اگر به تازگی ثبت کرده اید منتظر باشید تا توسط سامانه تایید شود.');
							  }
					},
					complete: function(){
						rangineMessageBox.find('td .loader').hide();
					},
				});

				return false;
			});
		});
		$('#rangine_sample').on('change', function() {
			if($(this).val()== 0) {
				$('.rangine_patterncode_wrapper,.rangine_patternvars_wrapper').addClass('hidden');
			}else{
				$('.rangine_patterncode_wrapper,.rangine_patternvars_wrapper').removeClass('hidden');
			}
		});

		$('#rangine_sample').change();

		$('#rangine_webservicetype').on('change', function() {
			if($(this).val() == 'user') {
				$('.rangine_apikey_wrapper, .rangine_sendvoiceotp_wrapper').addClass('hidden');
				$('.rangine_username_wrapper,.rangine_password_wrapper').removeClass('hidden');
			}else{
				$('.rangine_apikey_wrapper, .rangine_sendvoiceotp_wrapper').removeClass('hidden');
				$('.rangine_username_wrapper,.rangine_password_wrapper').addClass('hidden');
			}
		});
		$('#rangine_webservicetype').change();


		$('#rangine_internationalwebservicetype').on('change', function() {
			if($(this).val() == 'panel') {
				$('.rangine_internationalapi_wrapper').addClass('hidden');
				$('.rangine_international_patterncode_wrapper,.rangine_international_patternvars_wrapper').removeClass('hidden');
			}else{
				$('.rangine_internationalapi_wrapper').removeClass('hidden');
				$('.rangine_international_patterncode_wrapper,.rangine_international_patternvars_wrapper').addClass('hidden');
			}
		});
		$('#rangine_internationalwebservicetype').change();

		$('.rangine_webservicetype_wrapper').before('<tr class="ranginecred rangineMessageBox" style="display:none;"><td colspan="2"></td></tr>');
		rangineMessageBox = $('.rangineMessageBox');
		if($("#digit_tapp").val() == 313){
			rangineMessageBox.show();
			$('.dig_current_gateway').hide();
		}
		$('#mersaweb_sample').on('change', function() {
			if($(this).val()== 0) {
				$('.mersaweb_patterncode_wrapper,.mersaweb_patternvars_wrapper').addClass('hidden');
			}else{
				$('.mersaweb_patterncode_wrapper,.mersaweb_patternvars_wrapper').removeClass('hidden');
			}
		});

		$('#mersaweb_sample').change();

		$('#mersaweb_webservicetype').on('change', function() {
			console.log($(this).val());
			if($(this).val() == 'user') {
				$('.mersaweb_apikey_wrapper, .mersaweb_sendvoiceotp_wrapper').addClass('hidden');
				$('.mersaweb_username_wrapper,.mersaweb_password_wrapper').removeClass('hidden');
			}else{
				$('.mersaweb_apikey_wrapper, .mersaweb_sendvoiceotp_wrapper').removeClass('hidden');
				$('.mersaweb_username_wrapper,.mersaweb_password_wrapper').addClass('hidden');
			}
		});
		$('#mersaweb_webservicetype').change();

		// Ippanel gateway handlers
		$('#ippanel_webservicetype').on('change', function() {
			if($(this).val() == 'user') {
				$('.ippanel_apikey_wrapper').addClass('hidden');
				$('.ippanel_username_wrapper,.ippanel_password_wrapper').removeClass('hidden');
			}else{
				$('.ippanel_apikey_wrapper').removeClass('hidden');
				$('.ippanel_username_wrapper,.ippanel_password_wrapper').addClass('hidden');
			}
		});
		$('#ippanel_webservicetype').change();

		// tabansms gateway handlers
		$('#tabansms_webservicetype').on('change', function() {
			if($(this).val() == 'user') {
				$('.tabansms_apikey_wrapper').addClass('hidden');
				$('.tabansms_username_wrapper,.tabansms_password_wrapper').removeClass('hidden');
			}else{
				$('.tabansms_apikey_wrapper').removeClass('hidden');
				$('.tabansms_username_wrapper,.tabansms_password_wrapper').addClass('hidden');
			}
		});
		$('#tabansms_webservicetype').change();

		// eftekharnet gateway handlers
		$('#eftekharnet_webservicetype').on('change', function() {
			if($(this).val() == 'user') {
				$('.eftekharnet_apikey_wrapper').addClass('hidden');
				$('.eftekharnet_username_wrapper,.eftekharnet_password_wrapper').removeClass('hidden');
			}else{
				$('.eftekharnet_apikey_wrapper').removeClass('hidden');
				$('.eftekharnet_username_wrapper,.eftekharnet_password_wrapper').addClass('hidden');
			}
		});
		$('#eftekharnet_webservicetype').change();

		// markazepayamaki gateway handlers
		$('#markazepayamaki_webservicetype').on('change', function() {
			if($(this).val() == 'user') {
				$('.markazepayamaki_apikey_wrapper').addClass('hidden');
				$('.markazepayamaki_username_wrapper,.markazepayamaki_password_wrapper').removeClass('hidden');
			}else{
				$('.markazepayamaki_apikey_wrapper').removeClass('hidden');
				$('.markazepayamaki_username_wrapper,.markazepayamaki_password_wrapper').addClass('hidden');
			}
		});
		$('#markazepayamaki_webservicetype').change();

		// hamkaransms gateway handlers
		$('#hamkaransms_webservicetype').on('change', function() {
			if($(this).val() == 'user') {
				$('.hamkaransms_apikey_wrapper').addClass('hidden');
				$('.hamkaransms_username_wrapper,.hamkaransms_password_wrapper').removeClass('hidden');
			}else{
				$('.hamkaransms_apikey_wrapper').removeClass('hidden');
				$('.hamkaransms_username_wrapper,.hamkaransms_password_wrapper').addClass('hidden');
			}
		});
		$('#hamkaransms_webservicetype').change();

		// mrpayamak gateway handlers
		$('#mrpayamak_webservicetype').on('change', function() {
			if($(this).val() == 'user') {
				$('.mrpayamak_apikey_wrapper').addClass('hidden');
				$('.mrpayamak_username_wrapper,.mrpayamak_password_wrapper').removeClass('hidden');
			}else{
				$('.mrpayamak_apikey_wrapper').removeClass('hidden');
				$('.mrpayamak_username_wrapper,.mrpayamak_password_wrapper').addClass('hidden');
			}
		});
		$('#mrpayamak_webservicetype').change();

		// artapayamak gateway handlers
		$('#artapayamak_webservicetype').on('change', function() {
			if($(this).val() == 'user') {
				$('.artapayamak_apikey_wrapper').addClass('hidden');
				$('.artapayamak_username_wrapper,.artapayamak_password_wrapper').removeClass('hidden');
			}else{
				$('.artapayamak_apikey_wrapper').removeClass('hidden');
				$('.artapayamak_username_wrapper,.artapayamak_password_wrapper').addClass('hidden');
			}
		});
		$('#artapayamak_webservicetype').change();

		$("select.digit_gateway").on('change', function() {
			if($(this).val() == 313){
				$('.dig_current_gateway').hide();
				
				rangineMessageBox.show();
			} else {
				$('.dig_current_gateway').show();
				rangineMessageBox.hide();
			}
		});
	});
	$(window).on('load', function(){
	    if (typeof rangineMessageBox !== typeof undefined){
    		$("select.digit_gateway").each(function(){
    			if($(this).val() == 313) usercheck($(this),rangineMessageBox);
    		});
    		jQuery("#digits_setting_update button[type='submit']").click(function(){
    			$("select.digit_gateway").each(function(){
    				if($(this).val() == 313) usercheck($(this),rangineMessageBox);
    			});
    		});
	    }
	});
})(jQuery);