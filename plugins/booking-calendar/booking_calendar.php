<?php
/**
 * Plugin Name: Booking Calendar WpDevArt
 * Plugin URI: http://wpdevart.com/wordpress-booking-calendar-plugin
 * Author URI: http://wpdevart.com 
 * Description: WordPress Booking Calendar plugin is an awesome tool to create a booking system for your website. Create booking calendars in a few minutes.
 * Version: 2.2.2
 * Author: WpDevArt
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: booking-calendar
 */
 
defined('ABSPATH') || die('Access Denied');
class wpdevart_bc_calendar{
	
	protected $version = "10.3";
	protected $prefix = 'wpdevart';
	public $options;
	public static $booking_count = 1;
	
	
	function __construct(){
		@session_start();
		$this->setup_constants();		//Setup constants
		$this->require_files();
		$this->call_base_filters();		//Function for the main filters (hooks)
		$this->create_admin_menu();		//Function for creating admin menu
		add_shortcode(WPDEVART_PLUGIN_PREFIX."_booking_calendar", array($this,'shortcodes'));
	}
	
	/**
	* Setup constants
	**/
	private function setup_constants() {
		if ( ! defined( 'WPDEVART_PLUGIN_DIR' ) ) {
			define( 'WPDEVART_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		}
		if ( ! defined( 'WPDEVART_PLUGIN_PREFIX' ) ) {
			define( 'WPDEVART_PLUGIN_PREFIX', $this->prefix );
		}
		if(! defined( 'WPDEVART_URL' ) ){
			define ('WPDEVART_URL', trailingslashit( plugins_url('', __FILE__ ) ) );
		}
		if(! defined( 'WPDEVART_VERSION' ) ){
			define ('WPDEVART_VERSION', $this->version);
		}
		if(!defined('WPDEVART_PRO')){
			define('WPDEVART_PRO', false);
		}
		if(!defined('wpdevart_booking_support_url')){
			define('wpdevart_booking_support_url', "https://wordpress.org/support/plugin/booking-calendar");
		}
	}

	/**
	* Require files
	**/
	private function require_files() {
		require_once(WPDEVART_PLUGIN_DIR.'includes/currency_list.php');
		require_once(WPDEVART_PLUGIN_DIR.'includes/wpdevart_lib.php');
		require_once(WPDEVART_PLUGIN_DIR.'includes/booking_class.php');
		require_once(WPDEVART_PLUGIN_DIR.'includes/widgets/widget-booking_calendar.php');
		require_once(WPDEVART_PLUGIN_DIR.'includes/main_class.php');
	}
	
	private function create_admin_menu(){
		// Registration of file that is responsible for admin menu
		require_once(WPDEVART_PLUGIN_DIR.'includes/admin_menu.php');
		// Creation of admin menu object type 
		$wpdevart_admin_menu = new wpdevart_bc_admin_menu(array('menu_name' => 'Booking Calendar'));
		//Hook that will connect admin menu with class
		add_action('admin_menu', array($wpdevart_admin_menu,'create_menu'));
		
	}
	
	public function shortcodes($attr) {
		extract(shortcode_atts(array(
			'id' => null
		), $attr, WPDEVART_PLUGIN_PREFIX));
		if (empty($id)) {
			return;
		}
		$result = $this->wpdevart_booking_calendar($id);
		self::$booking_count += 1;
		return  $result;
	}
	
    /*############  Install database function ################*/		
	
	public function install_databese(){
		$version = get_option("wpdevart_booking_version");
        $new_version = $this->version;
		//registration of file that is responsible for database
		require_once(WPDEVART_PLUGIN_DIR.'includes/install_database.php');
		//Creation of database object type 
		$wpdevart_bc_install_database = new wpdevart_bc_install_database();
		if (!$version) {
			$wpdevart_bc_install_database->install_databese();
			add_option("wpdevart_booking_version", $new_version, '', 'no');
		} 
		elseif (version_compare($version, $new_version, '<')) {
			$wpdevart_bc_install_database->update_databese($version);
			update_option("wpdevart_booking_version", $new_version);
		}
		
	}
	
	public function registr_requeried_scripts(){
		load_plugin_textdomain( 'booking-calendar', FALSE, basename(dirname(__FILE__)).'/languages' );
		wp_enqueue_script( 'jquery-ui-datepicker', array('jquery') ); 
		if(!is_admin()){
			$scheme = is_ssl()? "https" : "http";
			wp_register_script( 'wpdevart-booking-script', WPDEVART_URL.'js/booking.js', array("jquery"),WPDEVART_VERSION);
			wp_localize_script( 'wpdevart-booking-script', WPDEVART_PLUGIN_PREFIX, array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php', $scheme ),
				'ajaxNonce'       => wp_create_nonce( 'wpdevart_ajax_nonce' ),
				'required' => __("is required.",'booking-calendar'),
				'emailValid' => __("Enter the valid email address.",'booking-calendar'),
				'date' => __("Date",'booking-calendar'),
				'hour' => __("Hour",'booking-calendar')
			) );
			wp_enqueue_script( 'wpdevart-booking-script' );
			wp_enqueue_script( 'wpdevart-script', WPDEVART_URL.'js/script.js', array("jquery"),WPDEVART_VERSION );
			wp_enqueue_script("recaptcha", "https://www.google.com/recaptcha/api.js", array("jquery"), WPDEVART_VERSION );
		}
		wp_enqueue_script( 'scrollto', WPDEVART_URL.'js/jquery.scrollTo-min.js', array("jquery"),WPDEVART_VERSION );
		wp_enqueue_style( 'jquery-ui',  WPDEVART_URL.'css/jquery-ui.css',array(),WPDEVART_VERSION);
		wp_enqueue_style( 'wpdevart-font-awesome', WPDEVART_URL . 'css/font-awesome/font-awesome.css',array(),WPDEVART_VERSION);
		wp_enqueue_style( 'wpdevart-style', WPDEVART_URL.'css/style.css',array(),WPDEVART_VERSION);
		wp_enqueue_style( 'wpdevart-effects', WPDEVART_URL.'css/effects.css',array(),WPDEVART_VERSION);
		wp_enqueue_style( 'wpdevartcalendar-style', WPDEVART_URL.'css/booking.css',array(),WPDEVART_VERSION);
	}
	
	public function call_base_filters(){
		add_action( 'init',  array($this,'registr_requeried_scripts') );
		//if (!isset($_GET['action']) || $_GET['action'] != 'deactivate') {
		  add_action('admin_init', array($this,'install_databese'));
		//}
		add_action( 'wp_ajax_nopriv_wpdevart_add_field', array($this,'wpdevart_add_field') );
		add_action( 'wp_ajax_wpdevart_add_field', array($this,'wpdevart_add_field') );
		add_action( 'wp_ajax_nopriv_wpdevart_add_extra_field', array($this,'wpdevart_add_extra_field') );
		add_action( 'wp_ajax_wpdevart_add_extra_field', array($this,'wpdevart_add_extra_field') );
		add_action( 'wp_ajax_nopriv_wpdevart_add_extra_field_item', array($this,'wpdevart_add_extra_field') );
		add_action( 'wp_ajax_wpdevart_add_extra_field_item', array($this,'wpdevart_add_extra_field_item') );
		add_action( 'wp_ajax_nopriv_wpdevart_ajax', array($this,'wpdevart_ajax') );
		add_action( 'wp_ajax_wpdevart_ajax', array($this,'wpdevart_ajax') );
		add_action( 'wp_ajax_nopriv_wpdevart_get_interval_dates', array($this,'wpdevart_get_interval_dates') );
		add_action( 'wp_ajax_wpdevart_get_interval_dates', array($this,'wpdevart_get_interval_dates') );
		add_action( 'wp_ajax_nopriv_wpdevart_form_ajax', array($this,'wpdevart_form_ajax') );
		add_action( 'wp_ajax_wpdevart_form_ajax', array($this,'wpdevart_form_ajax') );
		add_action( 'wp_ajax_nopriv_wpdevart_captcha', array($this,'wpdevart_captcha') );
		add_action( 'wp_ajax_wpdevart_captcha', array($this,'wpdevart_captcha') );
	}
	
	public function wpdevart_captcha() {
		if(!check_ajax_referer('wpdevart_ajax_nonce', 'wpdevart_nonce')) {
			die('Request has failed.');
		}
		$response = isset($_POST['wpdevart_captcha']) ? sanitize_text_field($_POST['wpdevart_captcha']) : "";
        $global_settings = get_option("wpdevartec_settings") === false ? array() :  json_decode(get_option("wpdevartec_settings"), true);
		$secret = isset($global_settings["recaptcha_private_key"]) ? $global_settings["recaptcha_private_key"] : "";
		$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
		$captcha_success = json_decode($verify);
		if ($captcha_success->success == false) {
		  echo 0;
		}
		else if ($captcha_success->success==true) {
		  echo 1;
		}
		wp_die();
	}
	
	public function wpdevart_booking_calendar($id=0, $res_id=0, $date='', $ajax = false, $selected = array(),$data = array(),$submit = "",$widget = false,$hours = array()) {
		$main_class = new wpdevart_Main($id, $widget);
		ob_start();
		echo $main_class->main_booking_calendar($id,$res_id, $date, $ajax, $selected,$data,$submit,$hours);
		return ob_get_clean();
	}
	
	public function wpdevart_booking_calendar_res($id=0, $date='', $ajax = false) {
		$main_class = new wpdevart_Main($id);
		echo $main_class->main_booking_calendar_res($date, $ajax);
	}
	
	public function  wpdevart_get_interval_dates(){
		$main_class = new wpdevart_Main();
		$main_class->wpdevart_get_interval_dates();
	}
	
	public function wpdevart_ajax() {
		if(!check_ajax_referer('wpdevart_ajax_nonce', 'wpdevart_nonce')) {
			die('Request has failed.');
		}
		$id = isset($_POST['wpdevart_id']) ? sanitize_text_field($_POST['wpdevart_id']) : 0;
		$main_class = new wpdevart_Main($id, false);
		$main_class->main_ajax();
		wp_die();
	}
	
	public function wpdevart_form_ajax() {
		if(!check_ajax_referer('wpdevart_ajax_nonce', 'wpdevart_nonce')) {
			die('Request has failed.');
		}
		$id = isset($_POST['wpdevart_id']) ? sanitize_text_field($_POST['wpdevart_id']) : 0;
		$main_class = new wpdevart_Main($id, false);
		$main_class->main_form_ajax();
		wp_die();
	}
	
	
	public function wpdevart_add_field() {
		if ( isset( $_POST['wpdevart_field_type'] ) ) {
			$type = str_replace('_field', '', sanitize_text_field( $_POST['wpdevart_field_type']));
		}
		$max_id = isset( $_POST['wpdevart_field_max'] ) ? sanitize_text_field( $_POST['wpdevart_field_max']) : 0;
		$count =  isset( $_POST['wpdevart_field_count'] )  ? sanitize_text_field( $_POST['wpdevart_field_count']) : 0;
		$args =  array(
			'name'   => 'form_field' . ($max_id + 1 + $count),
			'label' => __( 'New ' . $type . ' Field', 'booking-calendar' ),
			'type' => $type,
			'default' => ''
		);
		$function_name = "wpdevart_form_" . $type;
		wpdevart_bc_Library::$function_name($args, array('label' => __( 'New ' . $type . ' Field', 'booking-calendar' )));
		wp_die();
	}
	
	public function wpdevart_add_extra_field() {
		$max_id =  isset( $_POST['wpdevart_extra_field_max'] )  ? sanitize_text_field( $_POST['wpdevart_extra_field_max']) : 0;
		$count = isset( $_POST['wpdevart_extra_field_count'] )  ? sanitize_text_field( $_POST['wpdevart_extra_field_count']) : 0;
		$args =  array(
			'name'   => 'extra_field' . ($max_id + 1 + $count),
			'label' => __( 'New Extra', 'booking-calendar' ),
			'type' => 'extras_field',
			'items' => array(
				'field_item1' => array('name'=>'field_item1',
									'label' => '1',
									'operation' => '+',
									'price_type' => 'price',
									'price_percent' => '0',
									'order' => '1'
								),
				'field_item2' => array('name'=>'field_item2',
									'label' => '2',
									'operation' => '+',
									'price_type' => 'price',
									'price_percent' => '0',
									'order' => '2'
								),
				'field_item3' => array('name'=>'field_item3',
									'label' => '3',
									'operation' => '+',
									'price_type' => 'price',
									'price_percent' => '0',
									'order' => '3'
								)
			),
			'default' => ''
		);
		wpdevart_bc_Library::wpdevart_extras_field($args,$args);
		wp_die();
	}	
	
	public function wpdevart_add_extra_field_item() {
		$max_id = isset( $_POST['wpdevart_extra_field_item_max'] ) ? sanitize_text_field( $_POST['wpdevart_extra_field_item_max']) : 0;
		if ( isset( $_POST['wpdevart_extra_field'] ) ) {
			$extra_field = sanitize_text_field( $_POST['wpdevart_extra_field']);
		}
		$count = isset( $_POST['wpdevart_extra_field_item_count'] ) ? sanitize_text_field( $_POST['wpdevart_extra_field_item_count']) : 0;
		$args =  array('name'=>'field_item'. ($max_id + 1 + $count),
			'label' => ($max_id + 1),
			'operation' => '+',
			'price_type' => 'price',
			'price_percent' => '0',
			'order' => ($max_id + 1)
		);
		wpdevart_bc_Library::wpdevart_extras_field_item($extra_field,$args);
		wp_die();
	}
	
}
$wpdevart_booking = new wpdevart_bc_calendar(); // Creation of the main object

?>