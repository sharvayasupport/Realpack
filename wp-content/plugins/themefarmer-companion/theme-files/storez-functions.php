<?php
function themefarmer_companion_storez_customize_register($wp_customize) {

	$wp_customize->add_section('themefarmer_home_slider_section', array(
		'title'      => esc_html__('Slider', 'themefarmer-companion'),
		'panel'      => 'themefarmer_fontpage_panel',
		'capability' => 'edit_theme_options',
	));

	$wp_customize->add_section('themefarmer_home_brands_section', array(
		'title' => esc_html__('Brands', 'themefarmer-companion'),
		'panel' => 'themefarmer_fontpage_panel',
	));

/*Slider start*/
	$wp_customize->add_setting('themefarmer_home_slider', array(
		'sanitize_callback' => 'themefarmer_field_repeater_sanitize',
		'transport'         => 'postMessage',
		'default'           => array(
			array(
					'heading'     => esc_attr__('Slide 1 Heading', 'storez'),
					'description' => esc_attr__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'storez'),
					'image'       => esc_url(THEMEFARMER_COMPANION_URI . 'theme-files/demos/storez/images/slide1-left-img.jpg'),
					'button_text' => esc_attr__('View Details', 'storez'),
					'button_url'  => '#',
				),
				array(
					'heading'     => esc_attr__('Slide 2 Heading', 'storez'),
					'description' => esc_attr__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'storez'),
					'image'       => esc_url(THEMEFARMER_COMPANION_URI . 'theme-files/demos/storez/images/slide2-left-img.jpg'),
					'button_text' => esc_attr__('View Details', 'storez'),
					'button_url'  => '#',
				),

				array(
					'heading'     => esc_attr__('Slide 3 Heading', 'storez'),
					'description' => esc_attr__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'storez'),
					'image'       => esc_url(THEMEFARMER_COMPANION_URI . 'theme-files/demos/storez/images/slide3-left-img.jpg'),
					'button_text' => esc_attr__('View Details', 'storez'),
					'button_url'  => '#',
				)
		)
	));

	$wp_customize->add_control(new ThemeFarmer_Field_Repeater($wp_customize, 'themefarmer_home_slider', array(
		'label'     => esc_html__('Slide', 'themefarmer-companion'),
		'section'   => 'themefarmer_home_slider_section',
		'priority'  => 5,
		'row_label' => esc_html__('Slide', 'themefarmer-companion'),
		'max_items' => 3,
		'fields'    => array(
			'heading'     => array(
				'type'    => 'text',
				'label'   => esc_attr__('Title', 'themefarmer-companion'),
				'default' => esc_attr('Slide Heading', 'themefarmer-companion'),
			),
			'description' => array(
				'type'    => 'textarea',
				'label'   => esc_attr__('Description', 'themefarmer-companion'),
				'default' => esc_attr('Awesome Slide Description', 'themefarmer-companion'),
			),
			'image'       => array(
				'type'    => 'image',
				'label'   => esc_attr__('Image', 'themefarmer-companion'),
				'default' => esc_url(THEMEFARMER_COMPANION_URI . 'theme-files/demos/storez/images/slide1-left-img.jpg'),
			),
			'button_text' => array(
				'type'    => 'text',
				'label'   => esc_attr__('Button Text', 'themefarmer-companion'),
				'default' => esc_attr__('Learn More', 'themefarmer-companion'),
			),
			'button_url'  => array(
				'type'    => 'text',
				'label'   => esc_attr__('Button URL', 'themefarmer-companion'),
				'default' => esc_url('#'),
			),
		),
	)));

	$wp_customize->selective_refresh->add_partial('themefarmer_home_slider', array(
		'selector'         => '.section-slider .carousel-caption',
		'fallback_refresh' => false,
	));
/*Slider end*/
/*Brands start*/
	$wp_customize->add_setting('themefarmer_home_brands_heading', array(
		'default'           => esc_html__('Brands', 'themefarmer-companion'),
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	));

	$wp_customize->add_control('themefarmer_home_brands_heading', array(
		'type'    => 'text',
		'label'   => esc_html__('Heading', 'themefarmer-companion'),
		'section' => 'themefarmer_home_brands_section',
	));

	$wp_customize->add_setting('themefarmer_home_brands_desc', array(
		'default'           => esc_html__('Brands Description', 'themefarmer-companion'),
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	));

	$wp_customize->add_control('themefarmer_home_brands_desc', array(
		'type'    => 'textarea',
		'label'   => esc_html__('Description', 'themefarmer-companion'),
		'section' => 'themefarmer_home_brands_section',
	));

	$wp_customize->add_setting('themefarmer_home_brands', array(
		'sanitize_callback' => 'themefarmer_field_repeater_sanitize',
		'transport'         => 'postMessage',
		'default'           => array(
			array(
				'image' => get_template_directory_uri() . '/images/brand-logo.png',
			),
			array(
				'image' => get_template_directory_uri() . '/images/brand-logo.png',
			),
			array(
				'image' => get_template_directory_uri() . '/images/brand-logo.png',
			),
			array(
				'image' => get_template_directory_uri() . '/images/brand-logo.png',
			),
			array(
				'image' => get_template_directory_uri() . '/images/brand-logo.png',
			),
		),
	));

	$wp_customize->add_control(new ThemeFarmer_Field_Repeater($wp_customize, 'themefarmer_home_brands', array(
		'label'     => esc_html__('Brands', 'themefarmer-companion'),
		'section'   => 'themefarmer_home_brands_section',
		'priority'  => 30,
		'max_items' => 5,
		'row_label' => esc_html__('Brand', 'themefarmer-companion'),
		'fields'    => array(
			'image'      => array(
				'type'  => 'image',
				'label' => esc_attr__('Image', 'themefarmer-companion'),
			),
			'brand_link' => array(
				'type'  => 'text',
				'label' => esc_attr__('Brand URL', 'themefarmer-companion'),
			),
		),
	)));
/*Brands end*/

/*Social Links*/
	// if (apply_filters('themefarmer_is_theme_using_social_logins', false)) {

	$wp_customize->add_setting('themefarmer_socials', array(
		'sanitize_callback' => 'themefarmer_field_repeater_sanitize',
		'transport'         => 'postMessage',
		'default'           => array(
			array(
				'icon' => 'fa-facebook',
				'link' => '#',
			),
			array(
				'icon' => 'fa-youtube',
				'link' => '#',
			),
			array(
				'icon' => 'fa-instagram',
				'link' => '#',
			),
			array(
				'icon' => 'fa-google-plus',
				'link' => '#',
			),
			array(
				'icon' => 'fa-linkedin',
				'link' => '#',
			),
		),
	));

	$wp_customize->add_control(new ThemeFarmer_Field_Repeater($wp_customize, 'themefarmer_socials', array(
		'label'     => esc_html__('Social Links', 'themefarmer-companion'),
		'section'   => 'storez_topbar_section',
		'priority'  => 300,
		'max_items' => 5,
		'row_label' => esc_html__('Social Link', 'themefarmer-companion'),
		'fields'    => array(
			'icon' => array(
				'type'    => 'icon',
				'label'   => esc_attr__('Icon', 'themefarmer-companion'),
				'default' => 'fa-star',
			),
			'link' => array(
				'type'  => 'text',
				'label' => esc_attr__('Social Link', 'themefarmer-companion'),
			),
		),
	)));
	// }

/*social Links*/

/* banners */

	$wp_customize->add_section('storez_frontpage_banners_section', array(
		'title'    => __('Banners', 'themefarmer-companion'),
		'priority' => 160,
		'panel'    => 'themefarmer_fontpage_panel',
	));

	for ($i = 1; $i < 4; $i++) {
		$wp_customize->add_setting('storez_frontpage_banner_' . $i, array(
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'storez_frontpage_banner_' . $i, array(
			'label'   => sprintf(esc_html__('Banner Image %s', 'themefarmer-companion'), $i),
			'section' => 'storez_frontpage_banners_section',
		)));

		$wp_customize->add_setting('storez_frontpage_banner_text_' . $i, array(
			'sanitize_callback' => 'sanitize_text_field',
		));

		$wp_customize->add_control('storez_frontpage_banner_text_' . $i, array(
			'type'    => 'text',
			'section' => 'storez_frontpage_banners_section',
			'label'   => sprintf(esc_html__('Banner Text %s', 'themefarmer-companion'), $i),
		));

		$wp_customize->add_setting('storez_frontpage_banner_link_' . $i, array(
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control('storez_frontpage_banner_link_' . $i, array(
			'type'    => 'text',
			'section' => 'storez_frontpage_banners_section',
			'label'   => sprintf(esc_html__('Banner Image Link %s', 'themefarmer-companion'), $i),
		));
	}
/* banners */

}
add_action('customize_register', 'themefarmer_companion_storez_customize_register', 99);




function themefarmer_companion_newstore_ocdi_import_files() {
	return array(
		array(
			'import_file_name'             => 'StoreZ',
			'categories'                   => array('Customizer'),
			'local_import_file'            => trailingslashit(THEMEFARMER_COMPANION_DIR) . 'theme-files/demos/storez/storez-demo-data.xml',
			'local_import_widget_file'     => trailingslashit(THEMEFARMER_COMPANION_DIR) . 'theme-files/demos/storez/storez-widget.wie',
			'local_import_customizer_file' => trailingslashit(THEMEFARMER_COMPANION_DIR) . 'theme-files/demos/storez/storez-customizer.dat',
			'import_preview_image_url'     => esc_url(THEMEFARMER_COMPANION_URI . 'theme-files/demos/storez/images/screenshot.jpg'),
			'preview_url'                  => esc_url('https://demo.themefarmer.com/storez/'),
		)
	);
}
add_filter('pt-ocdi/import_files', 'themefarmer_companion_newstore_ocdi_import_files');


function themefarmer_companion_storez_ocdi_after_import($selected_import) {

	$top_menu        = get_term_by('name', 'Top Bar Menu', 'nav_menu');
	$primary_menu    = get_term_by('name', 'Primary Menu', 'nav_menu');
	$catlog_nav_menu = get_term_by('name', 'Catalog Menu', 'nav_menu');

	set_theme_mod('nav_menu_locations', array(
		'top_nav'         => $top_menu->term_id,
		'primary'         => $primary_menu->term_id,
		'product_catalog' => $catlog_nav_menu->term_id,
	));

	$cat_1           = get_term_by('name', 'Accessories', 'product_cat');
	$cat_2           = get_term_by('name', 'Men', 'product_cat');
	$cat_3           = get_term_by('name', 'Decor', 'product_cat');
	$cat_4           = get_term_by('name', 'Jewelry', 'product_cat');
	

	if (absint($cat_1->term_id)) {
		set_theme_mod('storez_frontpage_cat_id_1', absint($cat_1->term_id));
	}

	if (absint($cat_2->term_id)) {
		set_theme_mod('storez_frontpage_cat_id_2', absint($cat_2->term_id));
	}

	if (absint($cat_3->term_id)) {
		set_theme_mod('storez_frontpage_cat_id_3', absint($cat_3->term_id));
	}

	if (absint($cat_4->term_id)) {
		set_theme_mod('storez_frontpage_cat_id_4', absint($cat_4->term_id));
	}

	

	// Assign front page and posts page (blog page).

	$front_page_id = get_page_by_title('Home');

	$blog_page_id = get_page_by_title('Blog');
	update_option('show_on_front', 'page');
	update_option('page_on_front', $front_page_id->ID);
	update_option('page_for_posts', $blog_page_id->ID);
}
add_action('pt-ocdi/after_import', 'themefarmer_companion_storez_ocdi_after_import');

