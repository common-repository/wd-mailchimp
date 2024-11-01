<?php
/**
 * Plugin Name: MailChimp by 10Web
 * Plugin URI: https://10web.io/wordpress-mailchimp-wd/?utm_source=mwd&utm_medium=free_plugin
 * Description: Mailchimp by 10Web is a functional plugin developed to create MailChimp subscribe/unsubscribe forms and manage lists from your WordPress site.
 * Version: 1.1.4
 * Author: 10Web
 * Author URI: https://10web.io/?utm_source=mwd&utm_medium=free_plugin
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

define('MWD_DIR', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
define('MWD_URL', plugins_url(plugin_basename(dirname(__FILE__))));
define('MWD_MAIN_FILE', plugin_basename(__FILE__));
$upload_dir = wp_upload_dir();
$MWD_UPLOAD_DIR = str_replace(ABSPATH, '', $upload_dir['basedir']) . plugin_basename(dirname(__FILE__));

function mwd_options_panel() {
	$parent_slug = null;
	if( get_option( "mwd_subscribe_done" ) == 1){
		$parent_slug = "manage_mwd";
		add_menu_page('Mailchimp', 'Mailchimp', 'manage_options', $parent_slug, 'mailchimp_wd', MWD_URL . '/images/mailchimp_wd.png');
	}
	
	
	$manage_page = add_submenu_page($parent_slug, 'Mailchimp', 'Mailchimp', 'manage_options', 'manage_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $manage_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $manage_page, 'mwd_manage_scripts');

	$manage_lists = add_submenu_page($parent_slug, 'Lists', 'Lists', 'manage_options', 'manage_lists', 'mailchimp_wd');
	add_action('admin_print_styles-' . $manage_lists, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $manage_lists, 'mwd_manage_scripts');

	$manage_forms = add_submenu_page($parent_slug, 'Forms', 'Forms', 'manage_options', 'manage_forms', 'mailchimp_wd');
	add_action('admin_print_styles-' . $manage_forms, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $manage_forms, 'mwd_manage_scripts');

	$submissions_page = add_submenu_page($parent_slug, 'Submissions', 'Submissions', 'manage_options', 'submissions_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $submissions_page, 'mwd_submissions_styles');
	add_action('admin_print_scripts-' . $submissions_page, 'mwd_submissions_scripts');

	$themes_page = add_submenu_page($parent_slug, 'Themes', 'Themes', 'manage_options', 'themes_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $themes_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $themes_page, 'mwd_manage_scripts');

	$global_options_page = add_submenu_page($parent_slug, 'Global Options', 'Global Options', 'manage_options', 'goptions_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $global_options_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $global_options_page, 'mwd_manage_scripts');

	$blocked_ips_page = add_submenu_page($parent_slug, 'Blocked IPs', 'Blocked IPs', 'manage_options', 'blocked_ips', 'mailchimp_wd');
	add_action('admin_print_styles-' . $blocked_ips_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $blocked_ips_page, 'mwd_manage_scripts');

	$uninstall_page = add_submenu_page($parent_slug, 'Uninstall', 'Uninstall', 'manage_options', 'uninstall_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $uninstall_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $uninstall_page, 'mwd_manage_scripts');

}
add_action('admin_menu', 'mwd_options_panel');
add_action('init', 'mc_init');
function mc_init(){
	if( !class_exists("TenWebLib") ){
		require_once(MWD_DIR . '/wd/start.php');
	}

	global $mwd_options;
	$mwd_options = array (
		"prefix" => "mwd",
		"wd_plugin_id" => 164,
		"plugin_id" => 163,
		"plugin_title" => "Mailchimp by 10Web", 
		"plugin_wordpress_slug" => "wd-mailchimp", 
		"plugin_dir" => MWD_DIR,
		"plugin_main_file" => __FILE__,
		"description" => __('Mailchimp by 10Web is a functional plugin developed to create MailChimp subscribe/unsubscribe forms and manage lists from your WordPress site.', 'mwd-text'), 
		 // from 10Web.io
		 "plugin_features" => array(
			0 => array(
				"title" => __("Simple Set-up", "mwd-text"),
				"description" => __("Activate the plugin and simply grab the API key from your MailChimp account. Quickly create multiple Subscribe/Unsubscribe forms, connect to corresponding MailChimp lists. Manage subscriptions in an easy-to-use admin.
	", "mwd-text"),
			),
			1 => array(
				"title" => __("Customizable", "mwd-text"),
				"description" => __("Make the forms look and feel exactly as you want. The WordPress plugin allows to customize almost every aspect of the forms. Choose a theme that best fits your website, add an image, choose an image animation, add new fields and more.", "mwd-text"),
			),
			2 => array(
				"title" => __("Drag & Drop", "mwd-text"),
				"description" => __("Use the user-friendly drag and drop function to move the fields around, change the order of fields and create columns within the form.", "mwd-text"),
			),
			3 => array(
				"title" => __("Form Display Options", "mwd-text"),
				"description" => __("You can display the forms in 4 different ways - Embedded, Popup, Top bar and Scroll box. Each of the views has its customization options, including animation effect for pop-up, display pages, categories, frequency and more.", "mwd-text"),
			), 
			4 => array(
				"title" => __("Custom Messages", "mwd-text"),
				"description" => __("Mailchimp by 10Web WordPress plugin allows you to use notifications from MailChimp or set-up custom subscribe/u1nsubscribe emails for each form. Display customized messages after the user has submitted the form and the data has been successfully sent to MailChimp. You can also customize error messages, invalid email notes and other messages.", "mwd-text"),
			),   
			5 => array(
				"title" => __("Captcha", "mwd-text"),
				"description" => __("The more subscribers you have the better. But don't forget about the quality. Add captcha to your opt-in/opt-out forms to avoid spammy subscriptions. Choose Simple, Arithmetic Captchas or Recaptcha. Customize the field position, size, additional specs.", "mwd-text"),
			),    
			6 => array(
				"title" => __("Conditional Fields", "mwd-text"),
				"description" => __("Build smarter and more complex forms. Set conditions for forms to automatically show or hide fields in the form a certain condition is met.", "mwd-text"),
			), 
		
			7 => array(
				"title" => __("Themes", "mwd-text"),
				"description" => __("The Mailchimp plugin for WordPress comes with 13 pre-built themes you can choose from. If none of the themes suit your website, you can add a new theme or customize the existing themes. Change fonts, borders, margins, colors and much more. View changes you make instantly in the dashboard preview. If you wish to have it with the styles of your website theme, choose Inherit From Theme option.", "mwd-text"),
			) 		
		 ),
		 // user guide from 10Web.io
		"user_guide" => array(
			0 => array(
				"main_title" => __("Installation", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360018134371-Introduction",
				"titles" => array(
				)
			),
			1 => array(
				"main_title" => __("Introduction", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360018134371-Introduction",
				"titles" => array()
			),
			2 => array(
				"main_title" => __("Configuring API", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360017853692-Configuring-API-Key",
				"titles" => array()
			),
			3 => array(
				"main_title" => __("Creating a form", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360017853812-Creating-a-Form",
				"titles" => array(
					array(
						"title" => __("Form fields", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360017853852-Form-Fields",
					)
				)
			),
			4 => array(
				"main_title" => __("Display Options and Publishing", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360017854052-General-Description",
				"titles" => array(
					array(
						"title" => __("Embedded", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360017854052-General-Description",
					),
					array(
						"title" => __("Popup", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360017854052-General-Description",
					),
					array(
						"title" => __("Topbar", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360017854052-General-Description",
					),
					array(
						"title" => __("Scrollbox", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360017854052-General-Description",
					),
				)
			), 
			5 => array(
				"main_title" => __("Custom Fields", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360018135351-Custom-Fields",
				"titles" => array()
			), 
			6 => array(
				"main_title" => __("Form Options", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360018135371-General-Options",				
				"titles" => array(
					array(
						"title" => __("MailChimp Options", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360017854692-MailChimp-Options",
					),
					array(
						"title" => __("Email Options", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360018135411-Email-Options",
					),
					array(
						"title" => __("Email to User", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360018135411-Email-Options",
					),
					array(
						"title" => __("Email to Administrator", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360018135411-Email-Options",
					),
					array(
						"title" => __("Custom Messages", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360018135451-Custom-Messages",
					),
					array(
						"title" => __("Actions after submission", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360018135491-Actions-after-submission",
					),
					array(
						"title" => __("Payment Options", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360017854812-Payment-Options",
					),
					array(
						"title" => __("JavaScript", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360018135571-Javascript",
					),
					array(
						"title" => __("Conditional Fields", "mwd-text"),
						"url" => "https://help.10web.io/hc/en-us/articles/360017854932-Conditional-Fields",
					),
				)
			),
			7 => array(
				"main_title" => __("Themes", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360017854992-Themes",				
				"titles" => array(
				)
			),
			8 => array(
				"main_title" => __("Managing Lists", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360017855532-Managing-Lists",				
				"titles" => array(
				)
			),
			9 => array(
				"main_title" => __("Submissions", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360018136231-Submissions",				
				"titles" => array(
				)
			),	
			10 => array(
				"main_title" => __("Blocking IPs", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/sections/360002514911",				
				"titles" => array(
				)
			), 
			11 => array(
				"main_title" => __("Publishing as a Widget", "mwd-text"),
				"url" => "https://help.10web.io/hc/en-us/articles/360017855572-Publishing-as-a-Widget",				
				"titles" => array(
				)
			),                     
		 ), 
		 "video_youtube_id" => null,  // e.g. https://www.youtube.com/watch?v=acaexefeP7o youtube id is the acaexefeP7o
		 "overview_welcome_image" => null,
		 "plugin_wd_url" => "https://10web.io/plugins/wordpress-mailchimp/?utm_source=mwd&utm_medium=free_plugin", 
		 "plugin_wd_demo_link" => "https://demo.10web.io/mailchimp/", 
		 "plugin_wd_addons_link" => "", 
		 "plugin_wizard_link" => null, 
		 "plugin_menu_title" => "Mailchimp by 10Web", 
		 "plugin_menu_icon" => MWD_URL . '/images/mailchimp_wd.png', 
		 "deactivate" => true, 
		 "subscribe" => true,
		 "custom_post" => "manage_mwd",  // if true => edit.php?post_type=contact
		 "menu_capability" => "manage_options",  
		 "menu_position" => null,  	
		 "after_subscribe" => "admin.php?page=manage_mwd", // this can be plagin overview page or set up page
		 "display_overview" => false,
		);

	ten_web_lib_init( $mwd_options );
}


function mailchimp_wd() {
	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) {
			die('Access Denied');
		}
	}
	else {
		die('Access Denied');
	}
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('page');

	if (($page != '') && (($page == 'manage_mwd') || ($page == 'goptions_mwd') || ($page == 'manage_lists') || ($page == 'manage_forms') || ($page == 'submissions_mwd') || ($page == 'themes_mwd') || ($page == 'uninstall_mwd') || ($page == 'Formswindow') || ($page == 'blocked_ips'))) {
		require_once (MWD_DIR . '/admin/controllers/MWDController' . ucfirst(strtolower($page)) . '.php');
		$controller_class = 'MWDController' . ucfirst(strtolower($page));
		$controller = new $controller_class();
		$controller->execute();
	}
}

function updates_mwd() {
	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) {
			die('Access Denied');
		}
	}
	else {
		die('Access Denied');
	}
	require_once(MWD_DIR . '/featured/updates.php');
}


add_action('wp_ajax_manage_mwd', 'mwd_ajax');
add_action('wp_ajax_helper', 'mwd_ajax'); //Mailchimp params
add_action('wp_ajax_ListsGenerete_csv', 'mwd_ajax');
add_action('wp_ajax_conditions', 'mwd_ajax');  //conditions

add_action('wp_ajax_get_stats', 'mailchimp_wd'); //Show statistics
add_action('wp_ajax_view_submits', 'mailchimp_wd'); //Show statistics
add_action('wp_ajax_FormsGenerete_csv', 'mwd_ajax'); // Export csv.
add_action('wp_ajax_FormsSubmits', 'mwd_ajax'); // Export csv.
add_action('wp_ajax_FormsGenerete_xml', 'mwd_ajax'); // Export xml.
add_action('wp_ajax_FormsPreview', 'mwd_ajax');
add_action('wp_ajax_Formswdcaptcha', 'mwd_ajax'); // Generete captcha image and save it code in session.
add_action('wp_ajax_nopriv_Formswdcaptcha', 'mwd_ajax'); // Generete captcha image and save it code in session for all users.
add_action('wp_ajax_Formswdmathcaptcha', 'mwd_ajax'); // Generete math captcha image and save it code in session.
add_action('wp_ajax_nopriv_Formswdmathcaptcha', 'mwd_ajax'); // Generete math captcha image and save it code in session for all users.
add_action('wp_ajax_mwdpaypal_info', 'mwd_ajax'); // Paypal info in submissions page.
add_action('wp_ajax_formeditcountry', 'mwd_ajax'); // Open country list.
add_action('wp_ajax_product_option', 'mwd_ajax'); // Open product options on add paypal field.
add_action('wp_ajax_show_matrix', 'mwd_ajax'); // Edit matrix in submissions.

add_action('wp_ajax_mwdcheckpaypal', 'mwd_ajax'); // Notify url from Paypal Sandbox.
add_action('wp_ajax_nopriv_mwdcheckpaypal', 'mwd_ajax'); // Notify url from Paypal Sandbox for all users.
add_action('wp_ajax_select_interest_groups', 'mwd_ajax'); // select data from db.
add_action('wp_ajax_Formswindow', 'mwd_ajax');

// Elementor widget.
add_action('elementor/widgets/widgets_registered', 'mwd_register_elementor_widget');
add_action('elementor/elements/categories_registered', 'mwd_register_elementor_widget_category', 1, 1);
add_filter('tw_get_elementor_assets', 'mwd_register_elementor_assets');
add_action('elementor/editor/after_enqueue_styles', 'enqueue_elementor_widget_styles');
add_action('elementor/editor/after_enqueue_scripts', 'enqueue_elementor_widget_scripts');


/**
 * Register widget for Elementor builder.
 */
function mwd_register_elementor_widget() {
  if ( defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base') ) {
    require_once (MWD_DIR . '/includes/elementorWidget.php');
  }
}

/**
 * Register 10Web category for Elementor widget if 10Web builder doesn't installed.
 *
 * @param $elements_manager
 */
function register_elementor_widget_category( $elements_manager ) {
  $elements_manager->add_category('tenweb-plugins-widgets', array(
    'title' => __('10WEB Plugins', 'twbb'),
    'icon' => 'fa fa-plug',
  ));
}

function mwd_register_elementor_assets($assets) {
  $version = '2.0.0';
  if (!isset($assets['version']) || version_compare($assets['version'], $version) === -1) {
    $assets['version'] = $version;
    $assets['css_path'] = MWD_URL . '/css/elementor_style.css';
  }

  return $assets;
}

function enqueue_elementor_widget_styles() {
  $key = 'twbb-editor-styles';
  wp_deregister_style( $key );
  $assets = apply_filters('tw_get_elementor_assets', array());
  wp_enqueue_style($key, $assets['css_path'], array(), $assets['version']);
}
function enqueue_elementor_widget_scripts(){
  wp_enqueue_script('mwd_elementor_widget_js', plugins_url('js/mwd_elementor_widget.js', __FILE__), array('jquery'));
}






if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( 'includes/mwd_admin_class.php' );
	add_action( 'plugins_loaded', array( 'MWD_Admin', 'get_instance' ) );
}

function mwd_ajax() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('action');
	if ($page != 'Formswdcaptcha' && $page != 'Formswdmathcaptcha' && $page != 'mwdcheckpaypal') {
		if (function_exists('current_user_can')) {
			if (!current_user_can('manage_options')) {
				die('Access Denied');
			}
		}
		else {
			die('Access Denied');
		}
	}

	if ( $page != '' ) {
	    if( $page == 'mwdcheckpaypal' ) {
            $page = 'checkpaypal';
        } elseif ( $page == 'mwdpaypal_info' ) {
            $page = 'paypal_info';
        }
		require_once (MWD_DIR . '/admin/controllers/MWDController' . ucfirst($page) . '.php');
		$controller_class = 'MWDController' . ucfirst($page);
		$controller = new $controller_class();
		$controller->execute();
	}
}

function mwd_ajax_frontend() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('action');
	$task = MWD_Library::get('task');

	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) {
			die('Access Denied');
		}
	}
	else {
		die('Access Denied');
	}

	if ($page != '') {
		require_once (MWD_DIR . '/frontend/controllers/MWDController' . ucfirst($page) . '.php');
		$controller_class = 'MWDController' . ucfirst($page);
		$controller = new $controller_class();
		$controller->$task();
	}
}

function mwd_add_button($buttons) {
	array_push($buttons, "MWD_mce");
	return $buttons;
}

function mwd_register($plugin_array) {
	$url = MWD_URL . '/js/mwd_editor_button.js';
	$plugin_array["MWD_mce"] = $url;
	return $plugin_array;
}

add_filter('mce_external_plugins', 'mwd_register');
add_filter('mce_buttons', 'mwd_add_button', 0);
function mwd_admin_ajax() { ?>
	<script>
		var forms_admin_ajax = '<?php echo add_query_arg(array('action' => 'Formswindow'), admin_url('admin-ajax.php')); ?>';
		var plugin_url = '<?php echo MWD_URL; ?>';
		var content_url = '<?php echo content_url() ?>';
		var admin_url = '<?php echo admin_url('admin.php'); ?>';
		var nonce_mwd = '<?php echo wp_create_nonce('nonce_mwd') ?>';
	</script>
	<?php
}
add_action('admin_head', 'mwd_admin_ajax');

function mwd_output_buffer() {
	ob_start();
}
add_action('init', 'mwd_output_buffer');


add_shortcode('mwd-mailchimp', 'mwd_shortcode');
function mwd_shortcode($attrs) {
	ob_start();
	MWD_load_forms($attrs, 'embedded');
	return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());
}

if (!is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
	add_action('wp_footer', 'MWD_load_forms');
	add_action('wp_enqueue_scripts', 'mwd_front_end_scripts');
}

function MWD_load_forms($params = array(), $type = '') {
	$form_id = isset($params['id']) ? (int)$params['id'] : 0;
	require_once(MWD_DIR . '/includes/mwd_library.php');
	require_once (MWD_DIR . '/frontend/controllers/MWDControllerForms.php');
	$controller = new MWDControllerForms();
	$form = $controller->execute($form_id, $type);
	echo $form;
	return;
}

add_shortcode('mwd_optin_confirmation', 'mwd_optin_confirmation');
function mwd_optin_confirmation() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	require_once(MWD_DIR . '/frontend/controllers/MWDControllerCustom.php');
    $controller_class = 'MWDControllerCustom';
    $controller = new $controller_class();
    $controller->execute('optin_confirmation');
}

add_shortcode('mwd_unsubscribe', 'mwd_unsubscribe_shortcode');
function mwd_unsubscribe_shortcode() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	require_once(MWD_DIR . '/frontend/controllers/MWDControllerCustom.php');
    $controller_class = 'MWDControllerCustom';
    $controller = new $controller_class();
    $controller->execute('unsubscribe');
}

if (class_exists('WP_Widget')) {
  add_action('widgets_init', 'mwd_register_widgets');
}

/**
 * Register widgets.
 */
function mwd_register_widgets() {
  require_once(MWD_DIR . '/admin/controllers/MWDControllerWidget.php');
  register_widget("MWDControllerWidget");
}


function mwd_activate() {
	$version = get_option("mwd_version");
	$new_version = '1.1.4';
	if (!$version) {
		require_once MWD_DIR . "/includes/mwd_insert.php";
		mwd_insert();
		add_option('mwd_version', $new_version);
		add_option('mwd_pro', 'no');
		add_option('mwd_api_key', '');
		add_option('mwd_api_validation', 'invalid_api');
		add_option('mwd_settings', array('public_key' => '', 'private_key' => ''));
	}
	else {
		if (version_compare(substr($version,2), substr($new_version,2), '<')) {
			require_once MWD_DIR . "/includes/mwd_update.php";
			mwd_update($version);
		}	
		
		update_option('mwd_version', $new_version);
		update_option('mwd_pro', 'no');
	}
}

function mwd_del_trans() {
	delete_transient('mwd_update_check');
}
register_activation_hook(__FILE__, 'mwd_activate');
register_activation_hook(__FILE__, 'mwd_del_trans');

if (!isset($_GET['action']) || $_GET['action'] != 'deactivate') {
	add_action('admin_init', 'mwd_activate');
}

function mwd_deactivate() {
	/* delete_option('mwd_api_key');
	update_option('mwd_api_validation', 'invalid_api'); */
}
register_deactivation_hook(__FILE__, 'mwd_deactivate');


/* back-end styles */
function mwd_manage_styles() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('page');
	wp_admin_css('thickbox');
	wp_enqueue_style('mwd-mailchimp', MWD_URL . '/css/mwd-mailchimp.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-forms', MWD_URL . '/css/mwd-forms.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-bootstrap', MWD_URL . '/css/mwd-bootstrap.css', array(), get_option("mwd_version"));
	wp_enqueue_style('jquery-ui', MWD_URL . '/css/jquery-ui-1.10.3.custom.css');
	wp_enqueue_style('jquery-ui-spinner', MWD_URL . '/css/jquery-ui-spinner.css');
	wp_enqueue_style('mwd-style', MWD_URL . '/css/style.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-colorpicker', MWD_URL . '/css/spectrum.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-font-awesome', MWD_URL . '/css/frontend/font-awesome/font-awesome.css', array(), get_option("mwd_version"));
	if($page == "uninstall_mwd") {
		wp_enqueue_style('mwd_deactivate-css',  MWD_URL . '/wd/assets/css/deactivate_popup.css', array(), get_option("mwd_version"));
	}	
}

/* back-end scripts */
function mwd_manage_scripts() {
	wp_enqueue_script('thickbox');
	global $wp_scripts;
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('page');	
	if (isset($wp_scripts->registered['jquery'])) {
		$jquery = $wp_scripts->registered['jquery'];
		if (!isset($jquery->ver) OR version_compare($jquery->ver, '1.8.2', '<')) {
			wp_deregister_script('jquery');
			wp_register_script('jquery', FALSE, array('jquery-core', 'jquery-migrate'), '1.10.2' );
		}
	}

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-spinner');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('jquery-effects-shake');
	wp_enqueue_script('mwd-colorpicker', MWD_URL . '/js/spectrum.js', array(), get_option("mwd_version"));

	wp_enqueue_script('mwd_mailchimp', MWD_URL . '/js/mwd_mailchimp.js', array(), get_option("mwd_version"));
	wp_enqueue_script('forms_admin', MWD_URL . '/js/forms_admin.js', array(), get_option("mwd_version"));
	wp_enqueue_script('forms_manage', MWD_URL . '/js/forms_manage.js', array(), get_option("mwd_version"));
	wp_enqueue_media();
	
	wp_register_script('mwd-ng-js', MWD_URL . '/js/angular-1.5.0.min.js', array(), '1.5.0');
	
	if($page == "uninstall_mwd") {
		 wp_enqueue_script('mwd-deactivate-popup', MWD_URL.'/wd/assets/js/deactivate_popup.js', array(), get_option("mwd_version"), true );
		
		$admin_data = wp_get_current_user();
		wp_localize_script( 'mwd-deactivate-popup', 'mwdWDDeactivateVars', array(
			"prefix" => "mwd" ,
			"deactivate_class" =>  'mwd_deactivate_link',
			"email" => $admin_data->data->user_email,
			"plugin_wd_url" => "https://10web.io/plugins/wordpress-mailchimp/?utm_source=mwd&utm_medium=free_plugin",
		));	
	}		
}

function mwd_submissions_styles() {
	wp_admin_css('thickbox');
	wp_enqueue_style('mwd-forms', MWD_URL . '/css/mwd-forms.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-mailchimp', MWD_URL . '/css/mwd-mailchimp.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-bootstrap', MWD_URL . '/css/mwd-bootstrap.css', array(), get_option("mwd_version"));
	wp_enqueue_style('jquery-ui', MWD_URL . '/css/jquery-ui-1.10.3.custom.css', array(), '1.10.3');
	wp_enqueue_style('jquery-ui-spinner', MWD_URL . '/css/jquery-ui-spinner.css', array(), '1.10.3');
	wp_enqueue_style('jquery.fancybox', MWD_URL . '/js/fancybox/jquery.fancybox.css', array(), '2.1.5');
	wp_enqueue_style('mwd-style', MWD_URL . '/css/style.css', array(), get_option("mwd_version"));
}

function mwd_submissions_scripts() {
	wp_enqueue_script('thickbox');
	global $wp_scripts;
	if (isset($wp_scripts->registered['jquery'])) {
		$jquery = $wp_scripts->registered['jquery'];
		if (!isset($jquery->ver) OR version_compare($jquery->ver, '1.8.2', '<')) {
			wp_deregister_script('jquery');
			wp_register_script('jquery', FALSE, array('jquery-core', 'jquery-migrate'), '1.10.2' );
		}
	}
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'jquery-ui-progressbar' );
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-spinner');
	wp_enqueue_script('jquery-ui-mouse');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker');

	wp_enqueue_script('forms_admin', MWD_URL . '/js/forms_admin.js', array(), get_option("mwd_version"));
	wp_enqueue_script('forms_manage', MWD_URL . '/js/forms_manage.js', array(), get_option("mwd_version"));
	wp_enqueue_script('mwd_submissions', MWD_URL . '/js/mwd_submissions.js', array(), get_option("mwd_version"));

	wp_enqueue_script('mwd_main_frontend', MWD_URL . '/js/mwd_main_frontend.js', array(), get_option("mwd_version"));
	wp_localize_script('mwd_main_frontend', 'mwd_objectL10n', array('plugin_url' => MWD_URL));
	wp_enqueue_script('jquery.fancybox.pack', MWD_URL . '/js/fancybox/jquery.fancybox.pack.js', array(), '2.1.5');

}

function mwd_front_end_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-spinner');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('jquery-effects-shake');

	wp_enqueue_style('jquery-ui', MWD_URL . '/css/jquery-ui-1.10.3.custom.css');
	wp_enqueue_style('jquery-ui-spinner', MWD_URL . '/css/jquery-ui-spinner.css');
	wp_enqueue_style('mwd-mailchimp-frontend', MWD_URL . '/css/frontend/mwd-mailchimp-frontend.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-font-awesome', MWD_URL . '/css/frontend/font-awesome/font-awesome.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-animate', MWD_URL . '/css/frontend/mwd-animate.css', array(), get_option("mwd_version"));
	wp_enqueue_script('file-upload-frontend', MWD_URL . '/js/file-upload-frontend.js');

	wp_enqueue_script('mwd_main_frontend', MWD_URL . '/js/mwd_main_frontend.js', array(), get_option("mwd_version"));
	wp_localize_script('mwd_main_frontend', 'mwd_objectL10n', array('plugin_url' => MWD_URL));

	require_once(MWD_DIR . '/includes/mwd_library.php');
	$google_fonts = MWD_Library::mwd_get_google_fonts();
	$fonts = implode("|", str_replace(' ', '+', $google_fonts));
	wp_enqueue_style('mwd_googlefonts', 'https://fonts.googleapis.com/css?family=' . $fonts . '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic', null, null);
}

function mwd_language_load() {
	load_plugin_textdomain('mwd-text', FALSE, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'mwd_language_load');


add_filter('tw_get_plugin_blocks', 'mwd_register_plugin_block');
function mwd_register_plugin_block($blocks) {
  require_once(MWD_DIR . '/includes/mwd_library.php');
  $data = MWD_Library::get_shortcode_data();
  $plugin_name = 'WD Mailchimp';
  $blocks['tw/wd-mailchimp'] = array(
    'title' => "WD Mailchimp",
    'titleSelect' => sprintf(__('Select %s', 'mwd-text'), $plugin_name),
    'iconUrl' => MWD_URL . '/images/tw-gb/icon.svg',
    'iconSvg' => array('width' => 20, 'height' => 20, 'src' => MWD_URL . '/images/tw-gb/icon.svg'),
    'isPopup' => false,
    'data' => $data,
  );
  return $blocks;
}

// Enqueue block editor assets for Gutenberg.
function mwd_register_block_editor_assets($assets) {
  $version = '2.0.3';
  $js_path = MWD_URL . '/js/tw-gb/block.js';
  $css_path = MWD_URL . '/css/tw-gb/block.css';
  if (!isset($assets['version']) || version_compare($assets['version'], $version) === -1) {
    $assets['version'] = $version;
    $assets['js_path'] = $js_path;
    $assets['css_path'] = $css_path;
  }
  return $assets;
}
add_filter('tw_get_block_editor_assets', 'mwd_register_block_editor_assets');

function mvd_enqueue_block_editor_assets() {

  // Remove previously registered or enqueued versions
  $wp_scripts = wp_scripts();
  foreach ( $wp_scripts->registered as $key => $value ) {
    // Check for an older versions with prefix.
    if (strpos($key, 'tw-gb-block') > 0) {
      wp_deregister_script( $key );
      wp_deregister_style( $key );
    }
  }
  // Get the last version from all 10Web plugins.
  $assets = apply_filters('tw_get_block_editor_assets', array());
  $blocks = apply_filters('tw_get_plugin_blocks', array());
  // Not performing unregister or unenqueue as in old versions all are with prefixes.
  wp_enqueue_script('tw-gb-block', $assets['js_path'], array( 'wp-blocks', 'wp-element' ), $assets['version']);
  wp_localize_script('tw-gb-block', 'tw_obj_translate', array(
    'nothing_selected' => __('Nothing selected.', 'mwd-text'),
    'empty_item' => __('- Select -', 'mwd-text'),
    'blocks' => json_encode($blocks)
  ));
  wp_enqueue_style('tw-gb-block', $assets['css_path'], array( 'wp-edit-blocks' ), $assets['version']);
}

add_action('enqueue_block_editor_assets', 'mvd_enqueue_block_editor_assets');
?>
