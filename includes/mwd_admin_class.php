<?php

class MWD_Admin {
	public static $instance = null;
	protected $version = '1.7.79';
	public $update_path = 'http://api.web-dorado.com/v1/_id_/allversions';
	public $updates = array();
	public $mwd_plugins = array();
	public $prefix = "mwd_";
	protected $notices = null;
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
}

?>