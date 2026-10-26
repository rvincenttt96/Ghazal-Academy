<?php
/**
 * Redux Framework Sample Metabox Config File
 * For full documentation, please visit: http:https://devs.redux.io/
 * Metabox Lite support the following fields only:  checkbox, radio, text, textarea, media, & color
 * Post Format and Post Template options are not avaialble in Metabox Lite.
 * These advanced options are available in Redux Pro.
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Metaboxes' ) ) {
	return;
}
$opt_name = "shokrino_options";

Redux_Metaboxes::set_box(
	$opt_name,
	array(
		'id'         => 'opt-metaboxes',
		'title'      => esc_html__( 'گزینه های صفحه پست', 'shokrino' ),
		'post_types' => array( 'post' ),
		'position'   => 'normal', // normal, advanced, side.
		'priority'   => 'high', // high, core, default, low.
		'sections'   => array(
			array(
				'title'  => esc_html__( 'خلاصه پست' , 'shokrino' ),
				'id'     => 'excerpt_metabox',
				'icon'   => 'el-icon-cogs',
				'fields' => array(
					array(
						'id'       => 'post-excerpt',
						'type'     => 'editor',
						'title' => __('متن کوتاه بالای فهرست مطالب (باکس آنچه در این پست میخوانید)', 'shokrino' ),
					),
                    array(
                        'id'         => 'show_author_post',
                        'type'       => 'switch',
                        'title'      => 'نمایش نویسنده پست',
                        'default'    => true
                    ),
				),
			),
			array(
				'title'  => esc_html__( 'آپلود ویدیو' , 'shokrino' ),
				'id'     => 'post_inner_metabox',
				'icon'   => 'el-icon-cogs',
				'fields' => array(
                    array(
                        'id' => 'type_of_video',
                        'title' => esc_html__( 'نوع آپلود ویدیو را مشخص کنید', 'shokrino' ),
                        'type' => 'button_set',
                        'default' => 'disable',
                        'options' => array(
                            'disable' => esc_html__('غیرفعال', 'shokrino'),
                            'from_uploads' => esc_html__('آپلود روی وردپرس', 'shokrino'),
                            'from_url' => esc_html__('آپلود از لینک', 'shokrino'),
                            'shortcode' => esc_html__('جایگذاری شورتکد', 'shokrino'),
                            'aparat' => esc_html__('ویدیو آپارات', 'shokrino'),
                        )
                    ),
					array(
						'title'   => esc_html__('آپلود ویدیو', 'shokrino' ),
						'desc'    => esc_html__('آپلود فایل ویدیویی شما ( فرمت قابل قبول MP4 است )', 'shokrino' ),
						'id'      => 'shokrino_video_post',
						'type'    => 'media',
						'library_filter' => array('mp4'),
						'mode' => false,
						'url' => true,
						'preview' => true,
						'preview_size' => 'large',
                        'required' => array(
                            array(
                                'type_of_video',
                                '=',
                                'from_uploads'
                            )
                        ),
					),
                    array(
                        'id' => 'shokrino_video_post_url',
                        'type' => 'text',
                        'title' => esc_html__('لینک ویدیو', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                        'required' => array(
                            array(
                                'type_of_video',
                                '=',
                                'from_url'
                            )
                        ),
                    ),
                    array(
                        'id' => 'shokrino_video_shortcode_post',
                        'type' => 'text',
                        'title' => esc_html__('شورتکد ویدیو', 'shokrino'),
                        'subtitle' => esc_html__('میتوانید از افزونه های پلیر ویدیو که شورتکد تولید میکنند استفاده کنید', 'shokrino'),
                        'required' => array(
                            array(
                                'type_of_video',
                                '=',
                                'shortcode'
                            )
                        ),
                    ),
                    array(
                        'id' => 'shokrino_video_aparat_post',
                        'type' => 'text',
                        'title' => esc_html__('کد ویدیو آپارات را قرار دهید', 'shokrino'),
                        'subtitle' => esc_html__('شناسه ویدئوی آپارات را درج کنید : به‌عنوان مثال شناسه ویدئوی https://www.aparat.com/v/8OHwC عبارت است از : 8OHwC', 'shokrino'),
                        'required' => array(
                            array(
                                'type_of_video',
                                '=',
                                'aparat'
                            )
                        ),
                    ),
				),
			),
//			array(
//				'title'  => esc_html__( 'معرفی محصول در پست' , 'shokrino' ),
//				'id'     => 'post_product_inner_metabox',
//				'icon'   => 'el-icon-cogs',
//				'fields' => array(
//                    array(
//                        'title' => esc_html__( 'آیا محصولی دلخواه شما در این نوشته نمایش داده شود؟', 'shokrino' ),
//                        'desc' => esc_html__( 'اگر نیاز دارید که یک محصول خاص را در این نوشته معرفی کنید این گزینه را روی نمایش قرار دهید', 'shokrino' ),
//                        'id'   => 'post_product_offer_show',
//                        'type' => 'button_set',
//                        'options' => array(
//                            'true' => 'نمایش',
//                            'false' => 'مخفی',
//                        ),
//                        'default' => 'false',
//                    ),
//                    array(
//                        'title' => esc_html__( 'ID محصول مورد نظر:', 'shokrino' ),
//                        'subtitle' => esc_html__( 'درصورت خالی گذاشتن این بخش مخفی میشود', 'shokrino' ),
//                        'desc' => esc_html__( 'در قسمت همه محصولات میتوانید آیدی هر محصول را مشاهده کنید که بصورت عددی است', 'shokrino' ),
//                        'id'     => 'post_product_offer_id',
//                        'type'     => 'text',
//                        'required' => array(
//                            array(
//                                'post_product_offer_show',
//                                '=',
//                                'true'
//                            )
//                        )
//                    ),
//                    array(
//                        'title' => esc_html__( 'عنوان محصول', 'shokrino' ),
//                        'subtitle' => esc_html__( 'درصورت خالی گذاشتن این بخش مخفی میشود', 'shokrino' ),
//                        'desc' => esc_html__( 'نامی که برای محصول نمایش داده میشود', 'shokrino' ),
//                        'id'     => 'post_product_offer_title',
//                        'type'     => 'text',
//                        'required' => array(
//                            array(
//                                'post_product_offer_show',
//                                '=',
//                                'true'
//                            )
//                        )
//                    ),
//                    array(
//                        'title' => esc_html__( 'توضیحات محصول', 'shokrino' ),
//                        'subtitle' => esc_html__( 'درصورت خالی گذاشتن این بخش مخفی میشود', 'shokrino' ),
//                        'desc' => esc_html__( 'متنی که زیر عنوان محصول نمایش داده میشود', 'shokrino' ),
//                        'id'     => 'post_product_offer_text',
//                        'type'     => 'editor',
//                        'required' => array(
//                            array(
//                                'post_product_offer_show',
//                                '=',
//                                'true'
//                            )
//                        )
//                    ),
//                    array(
//                        'title' => esc_html__( 'نمایش قیمت محصول انتخاب شده', 'shokrino' ),
//                        'id'   => 'post_product_offer_price',
//                        'type' => 'button_set',
//                        'options' => array(
//                            'true' => 'نمایش قیمت',
//                            'false' => 'مخفی کردن قیمت',
//                        ),
//                        'default' => 'true',
//                        'required' => array(
//                            array(
//                                'post_product_offer_show',
//                                '=',
//                                'true'
//                            )
//                        )
//                    ),
//                    array(
//                        'title' => esc_html__( 'متن و لینک سفارشی دکمه افرودن به سبد خرید', 'shokrino' ),
//                        'desc' => esc_html__( 'با فعال سازی این گزینه میتوانید متن و لینک دکمه افزودن به سبد خرید را سفارشی سازی کنید. (درحالت پیشفرض دکمه محصول را به سبد خرید اضافه میکند)', 'shokrino' ),
//                        'id'   => 'post_product_offer_button_type',
//                        'type' => 'button_set',
//                        'options' => array(
//                            'true' => 'سفارشی سازی',
//                            'false' => 'پیشفرض',
//                        ),
//                        'default' => 'false',
//                        'required' => array(
//                            array(
//                                'post_product_offer_show',
//                                '=',
//                                'true'
//                            )
//                        )
//                    ),
//                    array(
//                        'title' => esc_html__( 'متن دکمه', 'shokrino' ),
//                        'id'     => 'post_product_offer_addtocart_text',
//                        'type'     => 'text',
//                        'required' => array(
//                            array(
//                                'post_product_offer_show',
//                                '=',
//                                'true'
//                            ),
//                            array(
//                                'post_product_offer_button_type',
//                                '=',
//                                'true'
//                            )
//                        )
//                    ),
//                    array(
//                        'title' => esc_html__( 'لینک دکمه', 'shokrino' ),
//                        'id'     => 'post_product_offer_addtocart_link',
//                        'type'     => 'text',
//                        'required' => array(
//                            array(
//                                'post_product_offer_show',
//                                '=',
//                                'true'
//                            ),
//                            array(
//                                'post_product_offer_button_type',
//                                '=',
//                                'true'
//                            )
//                        )
//                    ),
//				),
//			),
			array(
			'title'  => esc_html__( 'دکمه دانلود' , 'shokrino' ),
			'id'     => 'post_inner_btn_metabox',
			'icon'   => 'el-icon-cogs',
			'fields' => array(
				array(
					'title' => esc_html__( 'کلید دانلود فایل', 'shokrino' ),
					'desc' => esc_html__( 'با فعال کردن این گزینه و وارد کردن لینک دانلود میتوانید یک فایل را برای دانلود کاربران در انتهای پست قرار دهید', 'shokrino' ),
					'id'   => 'download_btn_show',
					'type' => 'button_set',
					'options' => array(
						'true' => 'نمایش', 
						'false' => 'مخفی',
					),
					'default' => 'false',
				),
				array(
					'title' => esc_html__( 'متن دکمه دانلود', 'shokrino' ),
					'desc' => esc_html__( 'مثلا: فایل افزونه استفاده شده یا فایل pdf برنامه ریزی', 'shokrino' ),
					'id'   => 'download_btn_text',
					'type' => 'text',
					'default' => '',
					'required' => array(
						array(
							'download_btn_show',
							'=',
							'true'
						)
					)
				),
				array(
					'title' => esc_html__( 'لینک دکمه دانلود', 'shokrino' ),
					'desc' => esc_html__( 'لینک با https:// شروع شود', 'shokrino' ),
					'id'   => 'download_btn_link',
					'type' => 'text',
					'default' => '',
					'required' => array(
						array(
							'download_btn_show',
							'=',
							'true'
							)
						)
					),
				),
			),
            array(
                'title'  => esc_html__( 'تنظیمات خاص این نوشته' , 'shokrino' ),
                'id'     => 'options_this_post_metabox',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'title' => esc_html__( 'آیا سایدبار فقط این نوشته مخفی شود؟', 'shokrino' ),
                        'desc' => esc_html__( 'اگر نیاز دارید که سایدبار فقط در این نوشته مخفی شود این گزینه را روی مخفی بگذارید', 'shokrino' ),
                        'id'   => 'post_sidebar_show',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'نمایش',
                            'false' => 'مخفی',
                        ),
                        'default' => 'true',
                    ),
                    array(
                        'title' => esc_html__( 'آیا فهرست مطالب این نوشته مخفی شود؟', 'shokrino' ),
                        'desc' => esc_html__( 'اگر نیاز دارید که فهرست مطالب (آنچه در این پست میخوانید) فقط در این نوشته مخفی شود این گزینه را روی مخفی بگذارید', 'shokrino' ),
                        'id'   => 'post_content_table_show',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'نمایش',
                            'false' => 'مخفی',
                        ),
                        'default' => 'true',
                    ),
                    array(
                        'title' => esc_html__( 'آیا هدر این نوشته نمایش داده شود؟', 'shokrino' ),
                        'desc' => esc_html__( 'اگر نیاز دارید که هدر در این نوشته مخفی شود این گزینه را روی مخفی بگذارید', 'shokrino' ),
                        'id'   => 'post_header_show',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'نمایش',
                            'false' => 'مخفی',
                        ),
                        'default' => 'true',
                    ),
                    array(
                        'title' => esc_html__( 'آیا فوتر این نوشته نمایش داده شود؟', 'shokrino' ),
                        'desc' => esc_html__( 'اگر نیاز دارید که فوتر در این نوشته مخفی شود این گزینه را روی مخفی بگذارید', 'shokrino' ),
                        'id'   => 'post_footer_show',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'نمایش',
                            'false' => 'مخفی',
                        ),
                        'default' => 'true',
                    ),
                    array(
                        'title' => esc_html__( 'آیا مسیر کاربر (breadcrumb) این نوشته نمایش داده شود؟', 'shokrino' ),
                        'desc' => esc_html__( 'اگر نیاز دارید که بخش مسیر بالای برگه در این نوشته مخفی شود این گزینه را روی مخفی بگذارید', 'shokrino' ),
                        'id'   => 'post_breadcrumb_show',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'نمایش',
                            'false' => 'مخفی',
                        ),
                        'default' => 'true',
                    ),
                ),
            ),
		)
	)
);


Redux_Metaboxes::set_box(
    $opt_name,
    array(
        'id'         => 'podcast_metabox',
        'title'      => esc_html__( 'گزینه های صفحه پادکست', 'shokrino' ),
        'post_types' => array( 'podcast' ),
        'position'   => 'normal', // normal, advanced, side.
        'priority'   => 'high', // high, core, default, low.
        'sections'   => array(
            array(
                'title'  => esc_html__( 'آپلود فایل صوتی' , 'shokrino' ),
                'id'     => 'podcast_inner_metabox',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'id' => 'type_of_audio_podcast',
                        'title' => esc_html__( 'نوع آپلود پادکست را مشخص کنید', 'shokrino' ),
                        'type' => 'button_set',
                        'default' => 'from_uploads',
                        'options' => array(
                            'from_uploads' => esc_html__('آپلود روی وردپرس', 'shokrino'),
                            'from_url' => esc_html__('آپلود از لینک', 'shokrino'),
                            'shortcode' => esc_html__('جایگذاری شورتکد', 'shokrino'),
                        )
                    ),
                    array(
                        'title'   => esc_html__('آپلود فایل صوتی', 'shokrino' ),
                        'desc'    => esc_html__('آپلود فایل صوتی شما ( فرمت قابل قبول MP3 است )', 'shokrino' ),
                        'id'      => 'shokrino_audio_podcast',
                        'type'    => 'media',
                        'library_filter' => array('mp3'),
                        'mode' => false,
                        'url' => true,
                        'preview' => true,
                        'preview_size' => 'large',
                        'required' => array(
                            array(
                                'type_of_audio_podcast',
                                '=',
                                'from_uploads'
                            )
                        ),
                    ),
                    array(
                        'id' => 'shokrino_audio_podcast_url',
                        'type' => 'text',
                        'title' => esc_html__('لینک پادکست', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                        'required' => array(
                            array(
                                'type_of_audio_podcast',
                                '=',
                                'from_url'
                            )
                        ),
                    ),
                    array(
                        'id' => 'shokrino_audio_shortcode',
                        'type' => 'text',
                        'title' => esc_html__('شورتکد پادکست', 'shokrino'),
                        'subtitle' => esc_html__('میتوانید از افزونه های پلیر صوتی که شورتکد تولید میکنند استفاده کنید', 'shokrino'),
                        'required' => array(
                            array(
                                'type_of_audio_podcast',
                                '=',
                                'shortcode'
                            )
                        ),
                    ),
                ),
            ),
        ),
    )
);

Redux_Metaboxes::set_box(
	$opt_name,
	array(
		'id'         => 'page-metaboxes',
		'title'      => esc_html__( 'گزینه های برگه', 'shokrino' ),
		'post_types' => array( 'page' ),
		'position'   => 'normal', // normal, advanced, side.
		'priority'   => 'high', // high, core, default, low.
		'sections'   => array(
			array(
			'title'  => esc_html__( 'مخفی سازی قسمت های برگه' , 'shokrino' ),
			'id'     => 'page_inner_btn_metabox',
			'icon'   => 'el-icon-cogs',
			'fields' => array(
				array(
					'title' => esc_html__( 'آیا هدر این برگه نمایش داده شود؟', 'shokrino' ),
					'desc' => esc_html__( 'اگر نیاز دارید که هدر در این برگه مخفی شود این گزینه را روی مخفی بگذارید', 'shokrino' ),
					'id'   => 'page_header_show',
					'type' => 'button_set',
					'options' => array(
						'true' => 'نمایش',
						'false' => 'مخفی',
					),
					'default' => 'true',
				),
                array(
					'title' => esc_html__( 'آیا فوتر این برگه نمایش داده شود؟', 'shokrino' ),
					'desc' => esc_html__( 'اگر نیاز دارید که فوتر در این برگه مخفی شود این گزینه را روی مخفی بگذارید', 'shokrino' ),
					'id'   => 'page_footer_show',
					'type' => 'button_set',
					'options' => array(
						'true' => 'نمایش',
						'false' => 'مخفی',
					),
					'default' => 'true',
				),
                array(
					'title' => esc_html__( 'آیا مسیر کاربر (breadcrumb) این برگه نمایش داده شود؟', 'shokrino' ),
					'desc' => esc_html__( 'اگر نیاز دارید که بخش مسیر بالای برگه در این برگه مخفی شود این گزینه را روی مخفی بگذارید', 'shokrino' ),
					'id'   => 'page_breadcrumb_show',
					'type' => 'button_set',
					'options' => array(
						'true' => 'نمایش',
						'false' => 'مخفی',
					),
					'default' => 'true',
				),
			),
		)
	)
));

Redux_Metaboxes::set_box(
	$opt_name,
	array(
		'id'         => 'tv_metabox',
		'title'      => esc_html__( 'گزینه های صفحه ویدیو', 'shokrino' ),
		'post_types' => array( 'tv' ),
		'position'   => 'normal', // normal, advanced, side.
		'priority'   => 'high', // high, core, default, low.
		'sections'   => array(
			array(
				'title'  => esc_html__( 'آپلود ویدیو' , 'shokrino' ),
				'id'     => 'tv_inner_metabox',
				'icon'   => 'el-icon-cogs',
				'fields' => array(
                    array(
                        'id' => 'type_of_video_tv',
                        'title' => esc_html__( 'نوع آپلود ویدیو را مشخص کنید', 'shokrino' ),
                        'type' => 'button_set',
                        'default' => 'from_uploads',
                        'options' => array(
                            'from_uploads' => esc_html__('آپلود روی وردپرس', 'shokrino'),
                            'from_url' => esc_html__('آپلود از لینک', 'shokrino'),
                            'shortcode' => esc_html__('جایگذاری شورتکد', 'shokrino'),
                            'aparat' => esc_html__('ویدیو آپارات', 'shokrino'),
                        )
                    ),
					array(
						'title'   => esc_html__('آپلود ویدیو', 'shokrino' ),
						'desc'    => esc_html__('آپلود فایل ویدیویی شما ( فرمت قابل قبول MP4 است )', 'shokrino' ),
						'id'      => 'shokrino_video_tv',
						'type'    => 'media',
						'library_filter' => array('mp4'),
						'mode' => false,
						'url' => true,
						'preview' => true,
						'preview_size' => 'large',
                        'required' => array(
                            array(
                                'type_of_video_tv',
                                '=',
                                'from_uploads'
                            )
                        ),
					),
                    array(
                        'id' => 'shokrino_video_tv_url',
                        'type' => 'text',
                        'title' => esc_html__('لینک ویدیو', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                        'required' => array(
                            array(
                                'type_of_video_tv',
                                '=',
                                'from_url'
                            )
                        ),
                    ),
                    array(
                        'id' => 'shokrino_video_shortcode',
                        'type' => 'text',
                        'title' => esc_html__('شورتکد ویدیو', 'shokrino'),
                        'subtitle' => esc_html__('میتوانید از افزونه های پلیر ویدیو که شورتکد تولید میکنند استفاده کنید', 'shokrino'),
                        'required' => array(
                            array(
                                'type_of_video_tv',
                                '=',
                                'shortcode'
                            )
                        ),
                    ),
                    array(
                        'id' => 'shokrino_video_aparat_tv',
                        'type' => 'text',
                        'title' => esc_html__('کد ویدیو آپارات را قرار دهید', 'shokrino'),
                        'subtitle' => esc_html__('شناسه ویدئوی آپارات را درج کنید : به‌عنوان مثال شناسه ویدئوی https://www.aparat.com/v/8OHwC عبارت است از : 8OHwC', 'shokrino'),
                        'required' => array(
                            array(
                                'type_of_video_tv',
                                '=',
                                'aparat'
                            )
                        ),
                    ),
				),
			),
            array(
                'title'  => esc_html__( 'شرایط نمایش ویدیو' , 'shokrino' ),
                'id'     => 'tv_show_conditions',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'id' => 'access_to_video',
                        'title' => esc_html__( 'نوع دسترسی کاربر به ویدیو', 'shokrino' ),
                        'type' => 'button_set',
                        'default' => 'free',
                        'options' => array(
                            'free' => esc_html__('رایگان برای همه', 'shokrino'),
                            'login_required' => esc_html__('نیازمند شرایط مشاهده', 'shokrino'),
                        )
                    ),
                    array(
                        'id' => 'condition_of_video',
                        'title' => esc_html__(  'شرط نمایش دادن ویدیو', 'shokrino' ),
                        'type' => 'button_set',
                        'default' => 'login',
                        'options' => array(
                            'login' => esc_html__('نیازمند ورود به اکانت', 'shokrino'),
                            'bought_product' => esc_html__('نیازمند خرید محصول خاص', 'shokrino'),
                        ),
                        'required' => array(
                            array(
                                'access_to_video',
                                '=',
                                'login_required'
                            )
                        ),
                    ),
                    array(
                        'id' => 'condition_product_id',
                        'title' => esc_html__( 'شناسه محصول مورد نظر را وارد کنید', 'shokrino' ),
                        'desc' => esc_html__( 'این شناسه همان آی دی یا ID محصول ووکامرس است که در صفحه همه محصولات میتوانید آن را بیابید.', 'shokrino' ),
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'condition_of_video',
                                '=',
                                'bought_product'
                            )
                        ),
                    ),
                    array(
                        'id' => 'title_required_text',
                        'title' => esc_html__( 'تیتر عدم دسترسی ویدیو', 'shokrino' ),
                        'type' => 'text',
                        'default' => 'شما اجازه مشاهده ویدیو را ندارید، لطفا از کلید زیر اقدام کنید',
                        'required' => array(
                            array(
                                'access_to_video',
                                '=',
                                'login_required'
                            )
                        ),
                    ),
                    array(
                        'id' => 'btn_required_url',
                        'title' => esc_html__( 'لینک دکمه جایگزین ویدیو', 'shokrino' ),
                        'desc' => esc_html__( 'این دکمه زمانی که کاربر وارد اکانت خود نشده است یا شرایط مشاهده را ندارد نمایش داده میشود', 'shokrino' ),
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'access_to_video',
                                '=',
                                'login_required'
                            )
                        ),
                    ),
                    array(
                        'id' => 'btn_required_text',
                        'title' => esc_html__( 'متن دکمه جایگزین ویدیو', 'shokrino' ),
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'access_to_video',
                                '=',
                                'login_required'
                            )
                        ),
                    ),
                ),
            ),
            array(
                'title'  => esc_html__( 'اطلاعات ویدیو' , 'shokrino' ),
                'id'     => 'tv_inner_time_metabox',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'id' => 'show_course_info_tv',
                        'title' => esc_html__(  'نمایش اطلاعات ویدیو مشابه دوره', 'shokrino' ),
                        'desc' => esc_html__(  'با فعال کردن این گزینه میتوانید نام مدرس و... را نمایش دهید', 'shokrino' ),
                        'type' => 'button_set',
                        'default' => 'false',
                        'options' => array(
                            'false' => esc_html__('غیرفعال', 'shokrino'),
                            'true' => esc_html__('فعال', 'shokrino'),
                        ),
                    ),
                    array(
                        'title' => esc_html__(  'زمان ویدیو', 'shokrino' ),
                        'desc'    => esc_html__('تایم ویدیو را وارد کنید مثلا 12:48', 'shokrino' ),
                        'id'      => 'shokrino_video_tv_time',
                        'type'    => 'text',
                    ),
                    array(
                        'title' => esc_html__(  'شماره جلسه', 'shokrino' ),
                        'id'      => 'shokrino_video_tv_session',
                        'type'    => 'text',
                        'required' => array(
                            array(
                                'show_course_info_tv',
                                '=',
                                'true'
                            )
                        ),
                    ),
                    array(
                        'title' => esc_html__(  'عنوان مدرس', 'shokrino' ),
                        'id'      => 'shokrino_video_tv_teacher',
                        'type'    => 'text',
                        'required' => array(
                            array(
                                'show_course_info_tv',
                                '=',
                                'true'
                            )
                        ),
                    ),
                ),
            ),
		),
	)
);

Redux_Metaboxes::set_box(
    $opt_name,
    array(
        'id'         => 'teacher_metabox',
        'title'      => esc_html__( 'گزینه های صفحه مدرس', 'shokrino' ),
        'post_types' => array( 'teacher' ),
        'position'   => 'normal', // normal, advanced, side.
        'priority'   => 'high', // high, core, default, low.
        'sections'   => array(
            array(
                'title'  => esc_html__( 'اطلاعات مدرس' , 'shokrino' ),
                'id'     => 'teacher_inner_metabox',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'title' => esc_html__( 'حوزه فعالیت', 'shokrino' ),
                        'desc' => esc_html__( 'مثلا: متخصص بک اند و php', 'shokrino' ),
                        'id'   => 'teacher_carrier',
                        'type' => 'text',
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__( 'فعالسازی تیک آبی', 'shokrino' ),
                        'desc' => esc_html__( 'با فعال سازی این گزینه تیک آبی کنار نام مدرس نمایش داده میشود', 'shokrino' ),
                        'id'   => 'teacher_blue_tik',
                        'type'     => 'checkbox',
                        'default'  => '0'// 1 = on | 0 = off
                    ),
                    array(
                        'title' => esc_html__( 'متن کوتاه درباره مدرس', 'shokrino' ),
                        'desc' => esc_html__( 'این متن در صفحه دوره های این مدرس در کنار نام نمایش داده میشود', 'shokrino' ),
                        'id'   => 'teacher_excerpt',
                        'type' => 'editor',
                        'default' => '',
                    ),
                ),
            ),
            array(
                'title'  => esc_html__( 'شبکه های اجتماعی' , 'shokrino' ),
                'id'     => 'teacher_social_metabox',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'id' => 'instagram_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('اینستاگرام', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'telegram_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('تلگرام', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'whatsapp_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('واتس اپ', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'linkedin_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('لینکداین', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'youtube_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('یوتیوب', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'twitter_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('توییتر', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'facebook_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('فیس بوک', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'aparat_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('آپارات', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'eita_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('پیامرسان ایرانی ایتا', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'igap_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('پیامرسان ایرانی آیگپ', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                    array(
                        'id' => 'sorush_btn_link_t',
                        'type' => 'text',
                        'title' => esc_html__('پیامرسان ایرانی سروش', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                    ),
                ),
            ),
        ),
    )
);

function get_all_teachers() {

    $teachers = array(
        '0' => '',
    );

    $teachers_args = array(
        'post_type'     => 'teacher',
        'post_status'   => 'publish',
        'posts_per_page'=> -1,
    );

    $teachers_query = new WP_Query( $teachers_args );

    foreach( $teachers_query->posts as $teacher ){
        $teachers[$teacher->ID] = $teacher->post_title;
    }

    return $teachers;
}

$all_teachers = get_all_teachers();

Redux_Metaboxes::set_box(
	$opt_name,
	array(
		'id'         => 'product_advice_metabox',
		'title'      => esc_html__( 'گزینه های صفحه محصول', 'shokrino' ),
		'post_types' => array( 'product' ),
		'position'   => 'normal', // normal, advanced, side.
		'priority'   => 'high', // high, core, default, low.
		'sections'   => array(
            array(
                'title'  => esc_html__( 'نوع نمایش محصول' , 'shokrino' ),
                'desc'  => esc_html__( 'اگر محصول شما دوره نیست و فایل دانلودی است در این قسمت تنظیمات مخصوص این محصول قرار دارد' , 'shokrino' ),
                'id'     => 'product_downloady_metabox',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'title' => esc_html__( 'نوع محصول را مشخص کنید', 'shokrino' ),
                        'id'   => 'downloady_product_check',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'دانلودی است',
                            'false' => 'دوره آموزشی است',
                        ),
                        'default' => 'true',
                    ),
                    array(
                        'title' => esc_html__( 'نمایش باکس راهنما (متن دلخواه)', 'shokrino' ),
                        'desc' => esc_html__( 'یک باکس جدید مشابه باکس توضیحات بالای این بخش قرار میگیرد که میتوانید عنوان و محتوای دلخواه برای آن قرار دهید', 'shokrino' ),
                        'id'   => 'downloady_product_extra_box',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'بله',
                            'false' => 'خیر',
                        ),
                        'default' => 'false',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'عنوان دلخواه باکس', 'shokrino' ),
                        'desc' => esc_html__( 'مثلا: راهنمای خرید', 'shokrino' ),
                        'id'   => 'downloady_product_extra_box_txt',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_extra_box',
                                '=',
                                'true'
                            ),
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'محتوای باکس', 'shokrino' ),
                        'desc' => esc_html__( 'متنی که داخل این باکس قرار میگیرد', 'shokrino' ),
                        'id'   => 'downloady_product_extra_box_content',
                        'type' => 'editor',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_extra_box',
                                '=',
                                'true'
                            ),
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'نمایش دکمه اضافه', 'shokrino' ),
                        'desc' => esc_html__( 'یک دکمه دیگر زیر دکمه سبد خرید اضافه میشود با لینک و متن دلخواه', 'shokrino' ),
                        'id'   => 'downloady_product_extra_button',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'بله',
                            'false' => 'خیر',
                        ),
                        'default' => 'false',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'متن دکمه', 'shokrino' ),
                        'desc' => esc_html__( 'مثلا: فایل دمو قسمتی از محصول', 'shokrino' ),
                        'id'   => 'downloady_product_extra_btn_txt',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_extra_button',
                                '=',
                                'true'
                            ),
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'لینک دکمه', 'shokrino' ),
                        'desc' => esc_html__( 'لینک با https:// شروع شود', 'shokrino' ),
                        'id'   => 'downloady_product_extra_btn_url',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_extra_button',
                                '=',
                                'true'
                            ),
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'تاریخ انتشار محصول', 'shokrino' ),
                        'desc' => esc_html__( 'درصورت خالی بودن غیرفعال میشود', 'shokrino' ),
                        'id'   => 'downloady_product_date_of_build',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'آخرین بروزرسانی', 'shokrino' ),
                        'desc' => esc_html__( 'درصورت خالی بودن غیرفعال میشود', 'shokrino' ),
                        'id'   => 'downloady_product_date_of_update',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'شماره نسخه (ورژن)', 'shokrino' ),
                        'desc' => esc_html__( 'درصورت خالی بودن غیرفعال میشود', 'shokrino' ),
                        'id'   => 'downloady_product_version',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'پشتیبانی', 'shokrino' ),
                        'desc' => esc_html__( 'درصورت خالی بودن غیرفعال میشود', 'shokrino' ),
                        'id'   => 'downloady_product_support',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'ویژگی دلخواه 1', 'shokrino' ),
                        'desc' => esc_html__( 'میتوانید یک ویژگی دلخواه اضافه کنید (درصورت خالی بودن غیرفعال میشود)', 'shokrino' ),
                        'id'   => 'downloady_product_custom1',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'مقدار دلخواه 1', 'shokrino' ),
                        'desc' => esc_html__( 'میتوانید  پاسخ ویژگی ویژگی دلخواه اول را اینجا اضافه کنید (درصورت خالی بودن غیرفعال میشود)', 'shokrino' ),
                        'id'   => 'downloady_product_custom1_answer',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'ویژگی دلخواه 2', 'shokrino' ),
                        'desc' => esc_html__( 'میتوانید یک ویژگی دلخواه اضافه کنید (درصورت خالی بودن غیرفعال میشود)', 'shokrino' ),
                        'id'   => 'downloady_product_custom2',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'مقدار دلخواه 2', 'shokrino' ),
                        'desc' => esc_html__( 'میتوانید  پاسخ ویژگی ویژگی دلخواه دوم را اینجا اضافه کنید (درصورت خالی بودن غیرفعال میشود)', 'shokrino' ),
                        'id'   => 'downloady_product_custom2_answer',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'ویژگی دلخواه 3', 'shokrino' ),
                        'desc' => esc_html__( 'میتوانید یک ویژگی دلخواه اضافه کنید (درصورت خالی بودن غیرفعال میشود)', 'shokrino' ),
                        'id'   => 'downloady_product_custom3',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'مقدار دلخواه 3', 'shokrino' ),
                        'desc' => esc_html__( 'میتوانید  پاسخ ویژگی ویژگی دلخواه سوم را اینجا اضافه کنید (درصورت خالی بودن غیرفعال میشود)', 'shokrino' ),
                        'id'   => 'downloady_product_custom3_answer',
                        'type' => 'text',
                        'default' => '',
                        'required' => array(
                            array(
                                'downloady_product_check',
                                '=',
                                'true'
                            )
                        )
                    ),
                ),
            ),
            array(
                'title'  => esc_html__( 'دکمه افزودن به سبد' , 'shokrino' ),
                'id'     => 'product_addtocart_metabox',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'id'       => 'course_add_to_cart_text',
                        'type'     => 'text',
                        'title' => esc_html__( 'متن سفارشی دکمه افزودن به سبد خرید (ثبت نام)', 'shokrino' ),
                        'desc' => esc_html__( 'می توانید متن دکمه خرید را سفارشی وارد کنید "حداکثر مجاز 27 کاراکتر است" . (در حالت پیشفرض "ثبت نام در دوره یا افزودن به سبد خرید است" تنظیم شده است)', 'shokrino' ),
                        'default' => '',
                    ),
                ),
            ),
            array(
                'title'  => esc_html__( 'تنظیم توقف فروش' , 'shokrino' ),
                'id'     => 'product_endoffsell_metabox',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'title' => esc_html__( 'قابلیت توقف فروش', 'shokrino' ),
                        'desc' => esc_html__( 'با فعال کردن این قابلیت ، فروش این محصول پس از یک تعداد مشخض یا همین الان دیگر امکان خرید توسط کاربر را ندارد', 'shokrino' ),
                        'id'   => 'endofsell_show',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'فعال',
                            'false' => 'غیرفعال',
                        ),
                        'default' => 'false',
                    ),
                    array(
                        'id'       => 'endofsell_text',
                        'type'     => 'text',
                        'title' => esc_html__( 'متن سفارشی دکمه توقف فروش', 'shokrino' ),
                        'desc' => esc_html__( 'می توانید متن دکمه توقف فروش را سفارشی وارد کنید "حداکثر مجاز 27 کاراکتر است" . (در حالت پیشفرض "امکان خرید وجود ندارد" تنظیم شده است)', 'shokrino' ),
                        'default' => 'امکان خرید وجود ندارد',
                        'required' => array(
                            array(
                                'endofsell_show',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'title' => esc_html__( 'فروش این محصول چه زمانی متوقف شود؟', 'shokrino' ),
                        'desc' => esc_html__( 'میتوانید تنظیم کنید که فروش از همین لحظه متوقف شود یا وفتی به تعداد مشخصی رسید توقف شود', 'shokrino' ),
                        'id'   => 'endofsell_type',
                        'type' => 'button_set',
                        'options' => array(
                            'fromnow' => 'از الان',
                            'numberofsell' => 'تعداد فروش مشخص',
                        ),
                        'default' => 'fromnow',
                        'required' => array(
                            array(
                                'endofsell_show',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
                        'id'       => 'endofsell_numberofsell',
                        'title' => esc_html__( 'تعداد حداکثر فروش', 'shokrino' ),
                        'desc' => esc_html__( 'عدد را در این بخش وارد کنید تا بیش از این تعداد امکان خرید توسط کاربران بسته شود', 'shokrino' ),
                        'type'     => 'slider',
                        'default'  => '',
                        'min'      => '1',
                        'step'     => '1',
                        'max'      => '100000',
                        'required' => array(
                            array(
                                'endofsell_type',
                                '=',
                                'numberofsell'
                            ),
                            array(
                                'endofsell_show',
                                '=',
                                'true'
                            )
                        )
                    ),
                ),
            ),

			array(
				'title'  => esc_html__( 'آپلود ویدیو معرفی دوره' , 'shokrino' ),
				'desc'  => esc_html__( 'در صفحه دوره قسمتی برای نمایش ویدیوی معرفی دوره ساخته شده است و این ویدیو در آن قسمت پخش خواهد شد' , 'shokrino' ),
				'id'     => 'product_video_metabox',
				'icon'   => 'el-icon-cogs',
				'fields' => array(
                    array(
                        'id' => 'type_of_video_course',
                        'title' => esc_html__( 'نوع آپلود ویدیو را مشخص کنید', 'shokrino' ),
                        'type' => 'button_set',
                        'default' => 'from_uploads',
                        'options' => array(
                            'disable' => esc_html__('غیرفعال', 'shokrino'),
                            'from_uploads' => esc_html__('آپلود روی وردپرس', 'shokrino'),
                            'from_url' => esc_html__('آپلود از لینک', 'shokrino'),
                        )
                    ),
                    array(
                        'id' => 'video_post_url_course',
                        'type' => 'text',
                        'title' => esc_html__('لینک ویدیو', 'shokrino'),
                        'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
                        'required' => array(
                            array(
                                'type_of_video_course',
                                '=',
                                'from_url'
                            )
                        ),
                    ),
					array(
						'title'    => esc_html__('آپلود ویدیو', 'shokrino' ),
						'desc'    => esc_html__('اگر نیازی به نمایش ویدیو ندارید ، میتوانید این قسمت را خالی بگذارید تا بخش ویدیو در صفحه دوره مخفی شود ( فرمت قابل قبول MP4 است )', 'shokrino' ),
						'id'      => 'shokrino_video_Woo',
						'type'    => 'media',
						'library_filter' => array('mp4'),
						'mode' => false,
						'url' => true,
						'preview' => true,
						'preview_size' => 'large',
                        'required' => array(
                            array(
                                'type_of_video_course',
                                '=',
                                'from_uploads'
                            )
                        ),
					),
					array(
						'title'    => esc_html__('پوستر ویدیو (کاور)', 'shokrino' ),
						'desc' => esc_html__('نسبت ابعاد پیشنهادی تصویر 16 به 9 (افقی) است . (تنها امکان انتخاب تصاویر با فرمت های jpg و png وجود دارد)', 'shokrino' ),
						'id'      => 'shokrino_video_Woo_poster',
						'type'    => 'media',
						'mode' => 'image',
						'url' => true,
						'preview' => true,
						'preview_size' => 'large',
					),
				),
			),
			array(
				'title'  => esc_html__( 'درباره مدرس دوره' , 'shokrino' ),
				'id'     => 'product_teacher_metabox',
				'icon'   => 'el-icon-cogs',
				'fields' => array(
					array(
						'title'    => esc_html__('انتخاب مدرسان', 'shokrino' ),
						'desc' => esc_html__('برای اضافه کردن مدرس ، از منوی پیشخوان گزینه "مدرسان" را انتخاب کنید و یک مدرس جدید تعریف کنید و سپس به این بخش بازگشته و ایشان را انتخاب نمایید', 'shokrino' ),
						'id'      => 'shokrino_teacher_raw',
						'type'    => 'raw'
					),
                    array(
                        'id'       => 'teacher_choose',
                        'type'     => 'select',
                        'title'    => esc_html__('مدرس دوره:', 'shokrino'),
                        'subtitle'    => esc_html__('مدرس دوره را انتخاب نمایید', 'shokrino'),
                        'options'  => $all_teachers,
                        'default'  => '0',
					),
                    array(
                        'id'       => 'teacher2_choose',
                        'type'     => 'select',
                        'title'    => esc_html__('مدرس دوم دوره:', 'shokrino'),
                        'subtitle'    => esc_html__('مدرس دوم دوره را انتخاب نمایید (اختیاری)', 'shokrino'),
                        'options'  => $all_teachers,
                        'default'  => '0',
                        'required' => array(
                            array(
                                'teacher_choose',
                                '!=',
                                ''
                            )
                        )
                    ),
                    array(
                        'id'       => 'teacher3_choose',
                        'type'     => 'select',
                        'title'    => esc_html__('مدرس سوم دوره:', 'shokrino'),
                        'subtitle'    => esc_html__('مدرس سوم دوره را انتخاب نمایید (اختیاری)', 'shokrino'),
                        'options'  => $all_teachers,
                        'default'  => '0',
                        'required' => array(
                            array(
                                'teacher_choose',
                                '!=',
                                ''
                            )
                        )
                    ),
				),
			),
			array(
				'title'  => esc_html__( 'ویژگی های دوره' , 'shokrino' ),
				'desc'  => esc_html__( 'تمامی موارد زیر اختیاری هستند و اگر نیازی به یک ویژگی ندارید ، میتوانید آن قسمت را خالی بگذارید' , 'shokrino' ),
				'id'     => 'product_metacourse_metabox',
				'icon'   => 'el-icon-cogs',
				'fields' => array(
                    array(
                        'title' => esc_html__( 'محصولات مرتبط', 'shokrino' ),
                        'id'   => 'related_products_show',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'نمایش',
                            'false' => 'مخفی',
                        ),
                        'default' => 'false',
                    ),
                    array(
                        'title' => esc_html__( 'نوار پیشرفت دوره', 'shokrino' ),
                        'desc' => esc_html__( 'با فعال کردن این گزینه یک نوار بالای توضیحات دوره نمایش داده میشود که میتوانید درصد پیشرفت دوره را در آن وارد کنید', 'shokrino' ),
                        'id'   => 'progress_btn_show',
                        'type' => 'button_set',
                        'options' => array(
                            'true' => 'نمایش',
                            'false' => 'مخفی',
                        ),
                        'default' => 'false',
                    ),
                    array(
                        'id'       => 'progress_percent',
                        'type'     => 'slider',
                        'title' => __('درصد پیشرفت را وارد کنید', 'shokrino' ),
                        'desc' => __('عدد میتواند بین 1 تا 100 باشد', 'shokrino' ),
                        'default'  => '0',
                        'min'      => '0',
                        'step'     => '1',
                        'max'      => '100',
                        'required' => array(
                            array(
                                'progress_btn_show',
                                '=',
                                'true'
                            )
                        )
                    ),
                    array(
						'title' => esc_html__( 'عنوان نوار پیشرفت', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: درصد پیشرفت دوره', 'shokrino' ),
						'id'   => 'progress_percent_text',
						'type' => 'text',
						'default' => '',
                        'required' => array(
                            array(
                                'progress_btn_show',
                                '=',
                                'true'
                            )
                        )
					),
                    array(
						'title' => esc_html__( 'زبان دوره', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: فارسی', 'shokrino' ),
						'id'   => 'course_language',
						'type' => 'text',
						'default' => ''
					),
					array(
						'title' => esc_html__( 'مدت زمان دوره', 'shokrino' ),
						'desc' => esc_html__( 'مثلا : 7 ساعت', 'shokrino' ),
						'default' => '',
						'id'   => 'course_duration',
						'type' => 'text',
					),
					array(
						'title' => esc_html__( 'نوع دوره', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: دوره حضوری  (یا دوره غیر حضوری)', 'shokrino' ),
						'default' => '',
						'id'   => 'course_type',
						'type' => 'text',
					),
					array(
						'title' => esc_html__( 'پیش نیاز دوره', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: آشنایی با HTML CSS', 'shokrino' ),
						'default' => '',
						'id'   => 'course_prerequisite',
						'type' => 'text',
					),
					array(
						'title' => esc_html__( 'تاریخ شروع دوره', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: 18 دی 1400', 'shokrino' ),
						'default' => '',
						'id'   => 'course_start_date',
						'type' => 'text',
					),
					array(
						'title' => esc_html__( 'تاریخ آخرین بروزرسانی دوره', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: 3 بهمن 1400', 'shokrino' ),
						'default' => '',
						'id'   => 'course_update_date',
						'type' => 'text',
					),
					array(
						'title' => esc_html__( 'حجم کل دوره', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: 450 مگابایت', 'shokrino' ),
						'default' => '',
						'id'   => 'course_file_size',
						'type' => 'text',
					),
					array(
						'title' => esc_html__( 'تعداد سرفصل ها', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: 5 فصل', 'shokrino' ),
						'default' => '',
						'id'   => 'course_lesseons',
						'type' => 'text',
					),
					array(
						'title' => esc_html__( 'روش پشتیبانی', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: از طریق واتس اپ یا ارسال تیکت', 'shokrino' ),
						'id'   => 'course_support',
						'type' => 'text',
						'default' => ''
					),
					array(
						'title' => esc_html__( 'روش دریافت', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: دانلود فایل های آموزشی', 'shokrino' ),
						'id'   => 'course_receive_type',
						'type' => 'text',
						'default' => ''
					),
					array(
						'title' => esc_html__( 'سطح دوره', 'shokrino' ),
						'desc' => esc_html__( 'مثلا: پیشرفته یا مقدماتی', 'shokrino' ),
						'id'   => 'course_level',
						'type' => 'text',
						'default' => ''
					),
					array(
						'title' => esc_html__( 'گواهینامه یا مدرک اعطایی', 'shokrino' ),
						//'desc' => esc_html__( 'مثلا: پیشرفته یا مقدماتی', 'shokrino' ),
						'id'   => 'course_certificate',
						'type' => 'text',
						'default' => ''
					),

				),
			),
            array(
                'title'  => esc_html__( 'باکس مشاوره رایگان دوره' ),
                'desc'  => esc_html__( 'اگر نیازی به باکس مشاوره ندارید ، میتوانید این قسمت را خالی بگذارید تا کل این بخش در صفحه دوره مخفی شود' ),
                'id'     => 'title_of_box',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'id'       => 'shokrino_advice_text',
                        'type'     => 'text',
                        'title' => __('متنی که بالای فرم تماس نوشته میشود'),
                        'desc' => 'برای استفاده از متن پیشفرض این قسمت را خالی بگذارید',
                    ),
                    array(
                        'id'       => 'shokrino_advice',
                        'type'     => 'text',
                        'title' => __('کد کوتاه فرم تماس'),
                        'desc' => 'یک فرم تماس از یکی از افزونه های فرم ساز بسازید و کد کوتاه آن را اینجا قرار دهید . برای مثال [contact-form-7 id="4658" title="فرم درخواست مشاوره دوره"]',
                    ),
                ),
            ),
			array(
				'title'  => esc_html__( 'تغییر تعداد دانشجویان' , 'shokrino' ),
                'desc' => esc_html__('دقت داشته باشید که با وارد کردن این عدد تعداد دانشجویان همچنان با خرید بصورت خودکار افزایش پیدا میکند اما مقداری که به کاربران نمایش داده میشود با این عدد جمع خواهد شد', 'shokrino' ),
				'id'     => 'student_metabox',
				'icon'   => 'el-icon-cogs',
				'fields' => array(
					array(
						'id'       => 'add-to-students-number',
						'type'     => 'slider',
						'title' => __('چه تعدادی به عدد پیشفرض دانشجویان دوره اضافه شود؟', 'shokrino' ),
						'desc' => __('عدد میتواند بین 1 تا 100000 باشد', 'shokrino' ),
                        'default'  => '0',
                        'min'      => '0',
                        'step'     => '1',
                        'max'      => '100000',
                    ),
				),
			),
		),
	)
);


Redux_Metaboxes::set_box(
    $opt_name,
    array(
        'id'         => 'notification_metabox',
        'title'      => esc_html__( 'گزینه های اعلان', 'shokrino' ),
        'post_types' => array( 'notification' ),
        'position'   => 'normal', // normal, advanced, side.
        'priority'   => 'high', // high, core, default, low.
        'sections'   => array(
            array(
                'title'  => esc_html__( 'تنظیمات اصلی اعلان' , 'shokrino' ),
                'id'     => 'notif_inner_metabox',
                'icon'   => 'el-icon-cogs',
                'fields' => array(
                    array(
                        'title' => esc_html__( 'نوع اعلان', 'shokrino' ),
                        'id'   => 'notif_type',
                        'type' => 'button_set',
                        'options' => array(
                            'public' => 'عمومی',
                            'private' => 'خصوصی',
                        ),
                        'default' => 'public',
                    ),
                    array(
                        'id'       => 'courses-multi-select',
                        'type'     => 'select',
                        'multi'    => true,
                        'title'    => esc_html__( 'ارسال به خریدارن محصول خاص', 'shokrino' ),
                        'options'  => shkrn_get_all_products(),
                        'required' => array(
                            array(
                                'notif_type',
                                '=',
                                'private'
                            )
                        ),
                    )
                ),
            ),
        ),
    )
);