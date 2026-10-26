<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GFRtl_persian{

	public function __construct() {
		
		add_filter( 'gform_currencies', array( $this, 'iran_currencies' ) );
		add_action( 'gform_editor_js', array( $this, 'iran_cities_editor_js' ) );
		add_action( 'gform_field_standard_settings', array( $this, 'iran_cities_option' ), 10, 2 );
		add_filter( 'gform_address_types', array( $this, 'iran_address_type' ) );
		add_filter( 'gform_predefined_choices', array( $this, 'iran_provinces_choices' ), 1 );
		add_filter( 'gform_field_content', array( $this, 'iran_cities_field_type' ), 10, 5 );
		add_action( 'gform_register_init_scripts', array( $this, 'init_script' ), 10, 1 );
		add_action( 'gform_enqueue_scripts', array( $this, 'external_js' ), 10, 2 );
		add_action( 'gform_editor_js', array( $this, 'jalali_settings' ) );
		add_action( 'gform_field_standard_settings', array( $this, 'jalali_calendar' ), 11, 2 );
		add_action( 'gform_enqueue_scripts', array( $this, 'jalali_datepicker' ), 11, 1 );
		add_filter( 'gform_date_min_year', array( $this, 'jalali_date_min' ), 10, 3 );
		add_filter( 'gform_date_max_year', array( $this, 'jalali_date_max' ), 10, 3 );
		add_filter( 'gform_field_validation', array( $this, 'jalali_validator' ), 999999, 4 );
		//add_action( 'admin_footer', array( $this, 'hide_license' ), 9999 );
	}
	public function hide_license() {
		 ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('img').each(function () {
                        if (this.title.indexOf("licensed") !== -1 || this.alt.indexOf("licensed") !== -1)
                            $(this).hide().parent("a").hide().parent("div").hide();
                    });
                });
            </script>
			<?php
		
	}
	public function jalali_calendar( $position, $form_id ) {
		if ( $position == 25 ) { ?>
            <li class="calendar_check_option field_setting">
                <input type="checkbox" id="calendar_jalali_option"onclick="SetFieldProperty('calendar_jalali_option', jQuery(this).is(':checked') ? 1 : 0);"/>
                <label class="inline gfield_value_label" for="calendar_jalali_option" class="inline">
                    فعالسازی تاریخ شمسی
					
                </label>
            </li>
			<?php
		}
	}
	public function jalali_datepicker( $form ) {

		if ( ! is_admin() && ( wp_script_is( 'gform_datepicker_init' ) || wp_script_is( 'gform_datepicker_init', 'registered' ) ) ) {

			foreach ( $form['fields'] as $field ) {

				if ( $field['type'] == 'date' && rgar( $field, 'calendar_jalali_option' ) ) {

					wp_dequeue_script( 'jquery-ui-datepicker' );
					wp_deregister_script( 'jquery-ui-datepicker' );
					wp_register_script( 'jquery-ui-datepicker', plugins_url( '', __FILE__ ) . '/js/gfrtl3.js', array(), GF_MIN_WP_VERSION, true );
					wp_enqueue_script( 'jquery-ui-datepicker' );
					
					//wp_dequeue_script( 'gform_gravityforms' );
					//wp_deregister_script( 'gform_gravityforms' );
					
					wp_localize_script(
			'gform_gravityforms', 'gform_i18nreq', array(
				'il8nreq' =>'sda33eeewerf4r4ff4',
			), 999
		);
					
					break;
				}
			}
		}
	}
	public function GF_tr_num( $str, $mod = 'en', $mf = '٫' ) {
	$num_a = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.' );
	$key_a = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', $mf );

	return ( $mod == 'fa' ) ? str_replace( $num_a, $key_a, $str ) : str_replace( $key_a, $num_a, $str );
}
	public function GF_gregorian_to_jalali( $g_y, $g_m, $g_d, $mod = '' ) {
	$g_y   = $this->GF_tr_num( $g_y );
	$g_m   = $this->GF_tr_num( $g_m );
	$g_d   = $this->GF_tr_num( $g_d );/* <= :اين سطر ، جزء تابع اصلي نيست */
	$d_4   = $g_y % 4;
	$g_a   = array( 0, 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
	$doy_g = $g_a[ (int) $g_m ] + $g_d;
	if ( $d_4 == 0 and $g_m > 2 ) {
		$doy_g ++;
	}
	$d_33 = (int) ( ( ( $g_y - 16 ) % 132 ) * .0305 );
	$a    = ( $d_33 == 3 or $d_33 < ( $d_4 - 1 ) or $d_4 == 0 ) ? 286 : 287;
	$b    = ( ( $d_33 == 1 or $d_33 == 2 ) and ( $d_33 == $d_4 or $d_4 == 1 ) ) ? 78 : ( ( $d_33 == 3 and $d_4 == 0 ) ? 80 : 79 );
	if ( (int) ( ( $g_y - 10 ) / 63 ) == 30 ) {
		$a --;
		$b ++;
	}
	if ( $doy_g > $b ) {
		$jy    = $g_y - 621;
		$doy_j = $doy_g - $b;
	} else {
		$jy    = $g_y - 622;
		$doy_j = $doy_g + $a;
	}
	if ( $doy_j < 187 ) {
		$jm = (int) ( ( $doy_j - 1 ) / 31 );
		$jd = $doy_j - ( 31 * $jm ++ );
	} else {
		$jm = (int) ( ( $doy_j - 187 ) / 30 );
		$jd = $doy_j - 186 - ( $jm * 30 );
		$jm += 7;
	}

	return ( $mod == '' ) ? array( $jy, $jm, $jd ) : $jy . $mod . $jm . $mod . $jd;
}	
	public function jalali_date_min( $min_year, $form, $field ) {

		if ( rgar( $field, 'type' ) == 'date' && rgar( $field, 'calendar_jalali_option' ) ) {
			$min_year = $this->GF_gregorian_to_jalali( $min_year, 03, 21 );
			$min_year = $min_year[0] + 1;
		}

		return apply_filters( 'gform_jalali_date_min_year', $min_year, $form, $field );
	}

	public function jalali_date_max( $max_year, $form, $field ) {

		if ( rgar( $field, 'type' ) == 'date' && rgar( $field, 'calendar_jalali_option' ) ) {
			$max_year = $this->GF_gregorian_to_jalali( $max_year, 03, 21 );
			$max_year = $max_year[0] + 20;
		}

		return apply_filters( 'gform_jalali_date_max_year', $max_year, $form, $field );
	}

	public function jalali_validator( $result, $value, $form, $field ) {

		if ( rgar( $field, 'type' ) == 'date' && rgar( $field, 'calendar_jalali_option' ) ) {

			$format      = rgar( $field, 'dateFormat', 'mdy' );
			$formats     = array(
				'mdy'       => 'mm/dd/yyyy',
				'dmy'       => 'dd/mm/yyyy',
				'dmy_dash'  => 'dd-mm-yyyy',
				'dmy_dot'   => 'dd.mm.yyyy',
				'ymd_slash' => 'yyyy/mm/dd',
				'ymd_dash'  => 'yyyy-mm-dd',
				'ymd_dot'   => 'yyyy.mm.dd'
			);
			$format_name = ! empty( $formats[ $format ] ) ? $formats[ $format ] : '';
			$message     = $format_name && rgar( $field, 'dateType' ) == 'datepicker' ? sprintf( esc_html__( 'Please enter a valid date in the format (%s).', 'gravityforms' ), $format_name ) : esc_html__( 'Please enter a valid date.', 'gravityforms' );

			/*این شرط مشخص میکنه فقط اگر خطایی وجود نداشت و یا اگر خطا مربوط به ولیدیت تاریخ بود وارد بررسی شود*/
			if ( ! empty( $result['message'] ) && $message != $result['message'] ) {
				return $result;
			}

			if ( is_array( $value ) && rgempty( 0, $value ) && rgempty( 1, $value ) && rgempty( 2, $value ) ) {
				$value = null;
			}

			if ( ! empty( $value ) ) {
				$date  = GFCommon::parse_date( $value, $format );
				$day   = intval( rgar( $date, 'day' ) );
				$month = intval( rgar( $date, 'month' ) );
				$year  = intval( rgar( $date, 'year' ) );

				$result['is_valid'] = $this->GF_jcheckdate( $month, $day, $year );
				if ( ! $result['is_valid'] && empty( $result['message'] ) ) {
					$result['message'] = $message;
				}
			}
		}

		return $result;
	}
	
	public function GF_jcheckdate( $jm, $jd, $jy ) {
	$jm  = $this->GF_tr_num( $jm );
	$jd  = $this->GF_tr_num( $jd );
	$jy  = $this->GF_tr_num( $jy );
	$l_d = ( $jm == 12 ) ? ( ( $jy % 33 % 4 - 1 == (int) ( $jy % 33 * .05 ) ) ? 30 : 29 ) : 31 - (int) ( $jm / 6.5 );

	return ( $jm > 0 and $jd > 0 and $jy > 0 and $jm < 13 and $jd <= $l_d ) ? true : false;
}

	
	
	
	public function is_gravity_page() {

		$is_gform     = class_exists( 'RGForms' ) ? RGForms::is_gravity_page() : false;
		$current_page = trim( strtolower( rgget( 'page' ) ) );

		return $is_gform || substr( $current_page, 0, 2 ) == 'gf' || stripos( $current_page, 'gravity' ) !== false;
	}
	public function iran_currencies( $currencies ) {

		unset( $currencies['IRR'], $currencies['irr'], $currencies['IRT'], $currencies['irt'] );

	

		$ir_currencies = array(

			'IRR' => array(
				'name'               => 'ریال',
				'symbol_left'        => '',
				'symbol_right'       => 'ریال',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 0,
				'code'		     => 'IRR'
			),

			'IRHR' => array(
				'name'               => 'هزار ریال',
				'symbol_left'        => '',
				'symbol_right'       => 'هزار ریال',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 0,
				'code'		     => 'IRHR'
			),

			'IRT' => array(
				'name'               => 'تومان',
				'symbol_left'        => '',
				'symbol_right'       => 'تومان',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 0,
				'code'		     => 'IRT'
			),

			'IRHT' => array(
				'name'               => 'هزار تومان',
				'symbol_left'        => '',
				'symbol_right'       => 'هزار تومان',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 0,
				'code'		     => 'IRHT'
			),

		);

		return array_merge( $ir_currencies, $currencies );
	}
	
	
	public function iran_provinces() {
		return array(
			'آذربایجان شرقی',
			'آذربایجان غربی',
			'اردبیل',
			'اصفهان',
			'البرز',
			'ایلام',
			'بوشهر',
			'تهران',
			'چهارمحال و بختیاری',
			'خراسان جنوبی',
			'خراسان رضوی',
			'خراسان شمالی',
			'خوزستان',
			'زنجان',
			'سمنان',
			'سیستان و بلوچستان',
			'فارس',
			'قزوین',
			'قم',
			'کردستان',
			'کرمان',
			'کرمانشاه',
			'کهگیلویه و بویراحمد',
			'گلستان',
			'گیلان',
			'لرستان',
			'مازندران',
			'مرکزی',
			'هرمزگان',
			'همدان',
			'یزد'
		);
	}
	
	public function iran_address_type( $address_types ) {

		$address_types['iran'] = array(
			'label'       => 'ایران',
			'country'     => 'ایران',
			'zip_label'   => 'کدپستی',
			'state_label' => 'استان',
			'states'      => array_merge( array( '' ), $this->iran_provinces() )
		);

		return $address_types;
	}

	public function iran_provinces_choices( $choices ) {

		$states['استان های ایران'] = $this->iran_provinces();

		return $choices = array_merge( $states, $choices );
	}
	public function jalali_settings() { ?>
        <script type='text/javascript'>
            fieldSettings['date'] += ', .calendar_check_option';
            jQuery(document).bind('gform_load_field_settings', function (event, field, form) {
                jQuery('#calendar_jalali_option').attr('checked', field['calendar_jalali_option'] == true);
            });
        </script>
		<?php
	}
	public function iran_cities_option( $position, $form_id ) {
		if ( $position == 25 ) { ?>
            <li class="iran_cities field_setting">
                <input type="checkbox" id="iran_cities"
                       onclick="SetFieldProperty('iran_cities', jQuery(this).is(':checked') ? 1 : 0);"/>
                <label class="inline gfield_value_label" for="iran_cities">فعالسازی شهرهای ایران</label>
            </li>
			<?php
		}
	}

	public function iran_cities_editor_js() { ?>

        <script type='text/javascript'>
            jQuery(document).ready(function ($) {
                fieldSettings["address"] += ", .iran_cities";
                $(document).bind("gform_load_field_settings", function (event, field, form) {
                    $("#iran_cities").attr("checked", field["iran_cities"] == true);
                    var $address_type = $('#field_address_type');
                    if (!$('#iran_cities_div').length) {
                        var $iran_cities = $(".iran_cities");
                        var $iran_cities_input = $iran_cities.html();
                        $iran_cities.remove();
                        $address_type.after('<div id="iran_cities_div"><br>' + $iran_cities_input + '</div>');
                    }
                    if ($address_type.val() === 'iran')
                        $('#iran_cities_div').show();
                    else
                        $('#iran_cities_div').hide();
                    $address_type.change(function () {
                        if ($(this).val() === 'iran')
                            $("#iran_cities_div").slideDown();
                        else
                            $("#iran_cities_div").slideUp();
                    });
                });
            });
        </script>

		<?php
	}

	public function iran_cities_field_type( $content, $field, $value, $entry_id, $form_id ) {

		if ( $this->is_iran_cities( $field ) ) {

			$id = absint( $field['id'] );

			preg_match( '/<input.*?(name=["\']input_' . $id . '.3["\'].*?)\/??>/i', $content, $match );

			if ( ! empty( $match[0] ) && ! empty( $match[1] ) ) {
				$city_input = trim( $match[1] );
				$city_input = str_ireplace( 'value=', 'data-selected=', $city_input );
				$content    = str_replace( $match[0], "<select {$city_input}><option value='' selected='selected'>&nbsp;&nbsp;</option></select>", $content );
			}
		}

		return $content;
	}

	public function external_js( $form, $ajax ) {

		$fields = GFCommon::get_fields_by_type( $form, array( 'address' ) );

		foreach ( (array) $fields as $field ) {

			if ( $this->is_iran_cities( $field ) ) {

				wp_dequeue_script( 'gfrtl_ircity' );
				wp_deregister_script( 'gfrtl_ircity' );

				wp_register_script( 'gfrtl_ircity', plugins_url( '', __FILE__ ) . '/js/gfrtl1.js', array('jquery'), GF_MIN_WP_VERSION, false );
				wp_enqueue_script( 'gfrtl_ircity' );

				add_action( 'gform_enqueue_scripts', array( $this, 'frontend_rtl' ), 999, 1 );

				break;
			}
		}
	}

	public function frontend_rtl( $form ) {

		
		?>
        <style type="text/css">
            <?php
			foreach ( $form['fields'] as &$field ) {
				if ( $this->is_iran_cities( $field ) ) {
				    $id = $form['id'] . '_' . $field['id'];
					echo '
					html[dir="rtl"] #input_' . $id . '_4_container{
						float: right !important;
						padding-right: 0 !important;
						padding-left: 16px !important;
					}
					html[dir="rtl"] #input_' . $id . '_3_container{
						float: left !important;
						padding-right: 0 !important;
						padding-left: 0 !important;
					}';
				}
			}
			?>
        </style>
		<?php
	}

	public function init_script( $form ) {

		foreach ( $form['fields'] as &$field ) {

			if ( $this->is_iran_cities( $field ) ) {

				$id = $form['id'] . '_' . $field['id'];

				$script = ';jQuery().ready(function($){' .
				          '$(".has_city #input_' . $id . '_3").html(gfrtl_ir_city(""+$(".has_city #input_' . $id . '_4").val()));' .
				          'if ($(".has_city #input_' . $id . '_3").attr("data-selected")) {' .
				          '$(".has_city #input_' . $id . '_3").val($(".has_city #input_' . $id . '_3").attr("data-selected"));' .
				          '}' .
				          '$(document.body).on("change", ".has_city #input_' . $id . '_4" ,function(){' .
				          '$(".has_city #input_' . $id . '_3").html(gfrtl_ir_city(""+$(".has_city #input_' . $id . '_4").val()));' .
				          '}).on("change", ".has_city #input_' . $id . '_3" ,function(){' .
				          '$(this).attr("data-selected", $(this).val());' .
				          '})' .
				          '})';
				GFFormDisplay::add_init_script( $form['id'], 'iran_address_city_' . $id, GFFormDisplay::ON_PAGE_RENDER, $script );
			}
		}
	}

	private function is_iran_cities( $field ) {
		return $field['type'] == 'address' && $field['addressType'] == 'iran' && rgar( $field, 'iran_cities' ) && ! is_admin();
	}
	
	
}
class RTLGF_National_ID{

	public function __construct() {

		if ( is_admin() ) {
			add_filter( 'gform_add_field_buttons', array( $this, 'button' ) );
			add_filter( 'gform_field_type_title', array( $this, 'title' ) );
			add_filter( 'gform_editor_js_set_default_values', array( $this, 'label' ) );
			add_action( 'gform_editor_js', array( $this, 'settings' ) );
			add_action( 'gform_field_standard_settings', array( $this, 'standard_settings' ), 10, 2 );
			add_filter( 'gform_tooltips', array( $this, 'tooltips' ) );
			add_filter( 'gform_admin_pre_render', array( $this, 'admin_conditional_logic' ) );
		}

		add_action( 'gform_field_input', array( $this, 'input' ), 10, 5 );
		add_action( 'gform_field_css_class', array( $this, 'classes' ), 10, 3 );
		add_action( 'gform_pre_submission', array( $this, 'pre_submission' ) );
		add_filter( 'gform_field_validation', array( $this, 'validator' ), 10, 4 );
		add_action( 'gform_register_init_scripts', array( $this, 'frontend_script' ), 10, 1 );
	}

	public function button( $field_groups ) {

		foreach ( $field_groups as &$group ) {
			if ( $group['name'] == 'advanced_fields' ) {
				$group['fields'][] = array(
					'class'     => 'button',
					'value'     => 'کد ملی',
					'data-type' => 'gfrtl_na_id',
					//'onclick' => "StartAddField('gfrtl_na_id');"//deprecated
				);
			}
		}

		return $field_groups;
	}

	public function title( $type ) {
		if ( $type == 'gfrtl_na_id' ) {
			return 'کد ملی';
		}

		return $type;
	}

	public function label() { ?>
        case 'gfrtl_na_id':
        field.label = 'کد ملی';
        break;
		<?php
	}

	public function classes( $classes, $field, $form ) {
		if ( $field['type'] == 'gfrtl_na_id' ) {
			$classes .= ' gform_gfrtl_na_id';
		}

		return $classes;
	}

	public function tooltips( $tooltips ) {
		$tooltips['tooltip_showLocation']   = '<h6>نمایش لحظه ای شهر از روی کد ملی </h6>نمایش شهر و پیغام زیر فیلد کد ملی بعد از پر شدن فیلد. تذکر : در صورتی که این گزینه را فعال نمایید ،ممکن است فراخوانی شهر های ایران با توجه به زیاد بودن آنها سبب سنگین شدن صفحه گردد.';
		$tooltips['tooltip_showSeperator']  = '<h6>جدا سازی ارقام</h6>در صورتی که این گزینه را فعال نمایید ، پس از پر شدن فیلد کد ملی ، <strong>در صورتی که کد ملی وارد شده صحیح تشخصی داده شود</strong> ؛ کد ملی به صورت زیر در خواهد آمد و در غیر این صورت علی صحیح نبودن کد ملی زیر فیلد نمایش داده خواهد شد :<br>xxx-xxxxxx-x';
		$tooltips['tooltip_forceEnglish']   = '<h6>تبدیل اعداد فارسی و عربی به انگلیسی</h6>در صورتی که کاربر کد ملی خود را به صورت انگلیسی وارد نکند با خطا مواجه خواهد شد. پس با فعالسازی این گزینه اعداد وارد شده برای کد ملی را به صورت خودکار به انگلیسی تبدیل کنید.';
		$tooltips['tooltip_notDigitError']  = 'با توجه به اینکه کد ملی فقط باید به صورت عدد باشد ، در صورتی که کاراکتری غیر از عدد وارد شده باشد پیغام خطا نمایش داده خواهد شد.<br/>پیغام پیشفرض : کد ملی فقط باید به صورت عدد وارد شود.';
		$tooltips['tooltip_qtyDigitError']  = 'با توجه به اینکه کد ملی می بایست 10 رقمی باشد اگر تعداد رقم وارد شده ، اشتباه باشد پیغام خطا نمایش داده خواهد شد.<br>پیغام پیشفرض : کد ملی می بایست 10 رقمی باشد. تنها در صورتی مجاز به استفاده از کد های 8 یا 9 رقمی هستید که ارقام سمت چپ 0 باشند.';
		$tooltips['tooltip_duplicateError'] = 'در صورتی که از تب وِیژگی تیک گزینه بدون تکرار را زده باشید ؛ بعد از پر شدن فرم و زدن دکمه ارسال پیغامی مبتنی بر تکراری بودن کد ملی نمایش داده خواهد شد. <br/>پیغام پیشفرض : این کد ملی توسط فرد دیگری ثبت شده است.';
		$tooltips['tooltip_isInvalidError'] = 'در صورتی که کد ملی وارد شده مطابق با الگوریتم کشور نباشد پیغام خطا نمایش داده خواهد شد.<br/>پیغام پیشفرض : کد ملی وارد شده مطابق با استانداردهای کشور نمی باشد.';

		return $tooltips;
	}

	public function input( $input, $field, $value, $entry_id, $form_id ) {

		if ( $field['type'] == 'gfrtl_na_id' ) {

			$field_id        = $field['id'];
			$form_id         = ! empty( $form_id ) ? $form_id : rgget( 'id' );
			$is_admin        = is_admin();
			$is_frontend     = ! $is_admin;
			$is_entry_detail = GFCommon::is_entry_detail();
			$is_form_editor  = GFCommon::is_form_editor();

			if ( $is_frontend && RGFormsModel::get_input_type( $field ) == 'adminonly_hidden' ) {
				return '';
			}

			$size         = rgar( $field, 'size' );
			$class_suffix = $is_entry_detail ? '_admin' : '';
			$class        = $size . $class_suffix;

			$input_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$field_id" : 'input_' . $form_id . "_$field_id";

			$tabindex      = GFCommon::get_tabindex();
			$disabled_text = $is_form_editor ? "disabled='disabled'" : '';

			$max_length = rgar( $field, 'showSeperator' ) ? 12 : 10;
			$max_length = "maxlength='{$max_length}'";
			
			$placeholder_attribute = $field->get_field_placeholder_attribute();
			$required_attribute    = $field->isRequired ? 'aria-required="true"' : '';
			$invalid_attribute     = $field->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';
			$html5_attributes      = " {$placeholder_attribute} {$required_attribute} {$invalid_attribute} {$max_length} ";

			$input = '<div class="ginput_container ginput_container_text ginput_container_gfrtl_na_id">';
			$input .= '<input onblur="gfrtl_na_id_' . $field_id . '(this);" name="input_' . $field_id . '" id="' . $input_id . '" type="text" value="' . esc_attr( $value ) . '" class="gfrtl_na_id ' . esc_attr( $class ) . '" ' . $tabindex . ' ' . $html5_attributes . ' ' . $disabled_text . '/>';
			$input .= '</div>';

			if ( $is_frontend ) {
				$input .= '<span class="gfrtl_na_id_location" id="gfrtl_na_id_location_' . $field_id . '"></span>';
			}
		}

		return $input;
	}

	public function settings() { ?>
        <script type='text/javascript'>
            fieldSettings["gfrtl_na_id"] = ".placeholder_setting, .input_mask_setting, .label_placement_setting, .prepopulate_field_setting, .conditional_logic_field_setting, .label_setting, .admin_label_setting, .size_setting, .rules_setting, .visibility_setting, .duplicate_setting, .default_value_setting, .description_setting, .css_class_setting, .gfrtl_na_id_setting";
            jQuery(document).bind('gform_load_field_settings', function (event, field, form) {
                jQuery("#showLocation").attr("checked", field["showLocation"] == true);
                jQuery("#showSeperator").attr("checked", field["showSeperator"] == true);
                jQuery("#forceEnglish").attr("checked", field["forceEnglish"] == true);
                jQuery("#notDigitError").val(field["notDigitError"]);
                jQuery("#qtyDigitError").val(field["qtyDigitError"]);
                jQuery("#duplicateError").val(field["duplicateError"]);
                jQuery("#isInvalidError").val(field["isInvalidError"]);
            });
        </script>
		<?php
	}


	public function admin_conditional_logic( $form ) {

		if ( GFCommon::is_entry_detail() ) {
			return $form;
		}

		echo "<script type='text/javascript'>" .
		     " gform.addFilter('gform_is_conditional_logic_field', function (isConditionalLogicField, field) {" .
		     "     return field.type == 'gfrtl_na_id' ? true : isConditionalLogicField;" .
		     '	});' .
		     "	gform.addFilter('gform_conditional_logic_operators', function (operators, objectType, fieldId) {" .
		     '		var targetField = GetFieldById(fieldId);' .
		     "		if (targetField && targetField['type'] == 'gfrtl_na_id') {" .
		     "			operators = {'is':'is','isnot':'isNot', '>':'greaterThan', '<':'lessThan', 'contains':'contains', 'starts_with':'startsWith', 'ends_with':'endsWith'};" .
		     '		}' .
		     '		return operators;' .
		     '	});' .
		     '</script>';

		return $form;
	}

	public function standard_settings( $position, $form_id ) {

		if ( $position == 50 ) { ?>

            <li class="gfrtl_na_id_setting field_setting">

                <input type="checkbox" id="showLocation"
                       onclick="SetFieldProperty('showLocation', this.checked);"/>
                <label for="showLocation" class="inline">
                    نمایش شهر بر اساس کد ملی
					<?php gform_tooltip( "tooltip_showLocation" ); ?>
                </label>

                <br/>
                <input type="checkbox" id="showSeperator"
                       onclick="SetFieldProperty('showSeperator', this.checked);"/>
                <label for="showSeperator" class="inline">
                    جداسازی خودکار ارقام توسط خط فاصله
					<?php gform_tooltip( "tooltip_showSeperator" ); ?>
                </label>

                <br/>
                <input type="checkbox" id="forceEnglish"
                       onclick="SetFieldProperty('forceEnglish', this.checked);"/>
                <label for="forceEnglish" class="inline">
                    تبدیل خودکار اعداد فارسی و عربی به انگلیسی
					<?php gform_tooltip( "tooltip_forceEnglish" ); ?>
                </label>
                <br/>

                <br/>
                <label for="notDigitError">
                    پیغام زمانی که مقدار وارد شده شامل کاراکتر غیر عددی باشد
					<?php gform_tooltip( "tooltip_notDigitError" ); ?>
                </label>
                <input type="text" class="fieldwidth-3" id="notDigitError" size="35"
                       onkeyup="SetFieldProperty('notDigitError', this.value);"/>
                <br/>

                <br/>
                <label for="qtyDigitError">
                    پیغام زمانیکه تعداد ارقام وارد شده استاندارد نباشد
					<?php gform_tooltip( "tooltip_qtyDigitError" ); ?>
                </label>
                <input type="text" class="fieldwidth-3" id="qtyDigitError" size="35"
                       onkeyup="SetFieldProperty('qtyDigitError', this.value);"/>
                <br/>

                <br/>
                <label for="isInvalidError">
                    پیغام زمانیکه کد ملی وارد شده مطابق با الگوی ملی نباشد
					<?php gform_tooltip( "tooltip_isInvalidError" ); ?>
                </label>
                <input type="text" class="fieldwidth-3" id="isInvalidError" size="35"
                       onkeyup="SetFieldProperty('isInvalidError', this.value);"/>
                <br/>

                <br/>
                <label for="duplicateError">
                    پیغام زمانیکه کد ملی وارد شده قبلا ثبت شده باشد
					<?php gform_tooltip( "tooltip_duplicateError" ); ?>
                </label>
                <input type="text" class="fieldwidth-3" id="duplicateError" size="35"
                       onkeyup="SetFieldProperty('duplicateError', this.value);"/>
                <br/>
            </li>
			<?php
		}
	}

	public function frontend_script( $form ) {

		$flag_js = $flag_fn = false;
		$fields  = GFCommon::get_fields_by_type( $form, array( 'gfrtl_na_id' ) );

		foreach ( (array) $fields as $field ) {

			$location     = rgar( $field, 'showLocation', 0 ) ? 1 : 0;
			$seperator    = rgar( $field, 'showSeperator', 0 ) ? 1 : 0;
			$forceEnglish = rgar( $field, 'forceEnglish', 0 ) ? 1 : 0;

			if ( ! $flag_js && ( $location + $seperator ) ) {

				$flag_js = true;

				wp_dequeue_script( 'gform_gfrtl_na_id' );
				wp_deregister_script( 'gform_gfrtl_na_id' );

				wp_register_script( 'gform_gfrtl_na_id', plugins_url( '', __FILE__ ) . '/js/gfrtl2.js', array(), GF_MIN_WP_VERSION, false );
				wp_enqueue_script( 'gform_gfrtl_na_id' );
			}
			?>
            <script type="text/javascript">
				<?php if ( ! $flag_fn && $forceEnglish) : ?>
                function gfrtl_na_id_to_english(number) {
                    return number.replace(/[۰|٠]/g, '0').replace(/[۱|١]/g, '1')
                        .replace(/[۲|٢]/g, '2').replace(/[۳|٣]/g, '3')
                        .replace(/[۴|٤]/g, '4').replace(/[۵|٥]/g, '5').replace(/[۶|٦]/g, '6')
                        .replace(/[۷|٧]/g, '7').replace(/[۸|٨]/g, '8').replace(/[۹|٩]/g, '9')
                }
				<?php $flag_fn = true; endif;?>
                function gfrtl_na_id_<?php echo $field['id']; ?>(_this) {
					<?php if ( $forceEnglish ) : ?>
                    _this.value = gfrtl_na_id_to_english(_this.value);
					<?php endif;
					if ( $location + $seperator ) : ?>
                    var field_id = "<?php echo $field['id'] ?>";
                    var message1 = "<?php echo rgar( $field, 'notDigitError', 'کد ملی فقط باید به صورت عدد وارد شود.' ); ?>";
                    var message2 = "<?php echo rgar( $field, 'qtyDigitError', 'کد ملی می بایست 10 رقمی باشد.' ); ?>";
                    var message3 = "<?php echo rgar( $field, 'isInvalidError', 'کد ملی وارد شده مطابق با استانداردهای کشور نمی باشد.' ); ?>";
                    gfrtl_na_hand(_this, field_id, message1, message2, message3, <?php echo $seperator ?>, <?php echo $location ?>);
                    jQuery(_this).trigger('change');
					<?php endif;?>
                }
            </script>
			<?php
		}
	}

	public function is_valid( $value = '' ) {

		if ( ! empty( $value ) ) {

			$_value = $value;

			if ( strlen( $value ) == 8 ) {
				$_value = '00' . $value;
			}

			if ( strlen( $value ) == 9 ) {
				$_value = '0' . $value;
			}

			$pre_check = array(
				'0000000000',
				'1111111111',
				'2222222222',
				'3333333333',
				'4444444444',
				'5555555555',
				'6666666666',
				'7777777777',
				'8888888888',
				'9999999999',
				'0123456789',
			);

			if ( in_array( $_value, $pre_check ) ) {
				return 2;
			}

			if ( ! is_numeric( $value ) ) {
				return 4;
			}

			$value = (string) preg_replace( '/[^0-9]/', '', $value );

			if ( strlen( $value ) > 10 || strlen( $value ) < 8 ) {
				return 3;
			}

			$value = $_value;

			$list_code = str_split( $value );
			$last      = (int) $list_code[9];
			unset( $list_code[9] );

			$i   = 10;
			$sum = 0;
			foreach ( $list_code as $key => $val ) {
				$sum += intval( $val ) * $i;
				$i --;
			}

			$mod = (int) $sum % 11;

			if ( $mod >= 2 ) {
				$mod = 11 - $mod;
			}
			if ( $mod != $last ) {
				return 2;
			} else {
				return 1;
			}
		}

		return false;
	}

	public function validator( $result, $value, $form, $field ) {

		if ( $field['type'] == 'gfrtl_na_id' ) {

			$value    = ! empty( $value ) ? str_replace( '-', '', $value ) : '';
			$is_valid = $this->is_valid( $value );

			if ( $is_valid == 4 ) {
				$result['is_valid'] = false;
				$result['message']  = rgar( $field, 'notDigitError', 'کد ملی فقط باید به صورت عدد وارد شود.' );

				return $result;
			}

			if ( $is_valid == 3 ) {
				$result['is_valid'] = false;
				$result['message']  = rgar( $field, 'qtyDigitError', 'کد ملی می بایست 10 رقمی باشد. تنها در صورتی مجاز به استفاده از کد های 8 یا 9 رقمی هستید که ارقام سمت چپ 0 باشند.' );

				return $result;
			}

			if ( $is_valid == 2 ) {
				$result['is_valid'] = false;
				$result['message']  = rgar( $field, 'isInvalidError', 'کد ملی وارد شده مطابق با استانداردهای کشور نمی باشد.' );

				return $result;
			}

			if ( $field['noDuplicates'] ) {

				if ( strlen( $value ) == 8 ) {
					$value = '00' . $value;
				}

				if ( strlen( $value ) == 9 ) {
					$value = '0' . $value;
				}

				if ( RGFormsModel::is_duplicate( $form['id'], $field, $value ) ) {
					$result['is_valid'] = false;
					$result['message']  = rgar( $field, 'duplicateError', 'این کد ملی توسط فرد دیگری ثبت شده است.' );

					return $result;
				}
			}
		}

		return $result;
	}

	public function pre_submission( $form ) {

		$gfrtl_na_id_fields = GFCommon::get_fields_by_type( $form, array( 'gfrtl_na_id' ) );

		foreach ( (array) $gfrtl_na_id_fields as $field ) {

			$input_name  = "input_{$field['id']}";
			$input_value = rgpost( $input_name );

			if ( ! empty( $input_value ) ) {

				if ( strlen( $input_value ) == 8 ) {
					$_POST["input_{$field['id']}"] = '00' . $input_value;
				} elseif ( strlen( $input_value ) == 9 ) {
					$_POST["input_{$field['id']}"] = '0' . $input_value;
				} else {
					$_POST["input_{$field['id']}"] = $input_value;
				}
			}
		}
	}
}

new GFRtl_persian();
new RTLGF_National_ID();
