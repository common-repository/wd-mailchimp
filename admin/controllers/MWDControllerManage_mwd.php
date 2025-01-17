<?php

class MWDControllerManage_mwd {

	public function execute() {
		$message = MWD_Library::get('message');
		echo MWD_Library::message_id($message);
		$task = MWD_Library::get('task');
		if (method_exists($this, $task)) {
			$this->$task();
		}
		else {
			$this->display();
		}
	}

	public function display() {
		require_once MWD_DIR . "/admin/models/MWDModelManage_mwd.php";
		$model = new MWDModelManage_mwd();

		require_once MWD_DIR . "/admin/views/MWDViewManage_mwd.php";
		$view = new MWDViewManage_mwd($model);
		
		$view->display();
	}

	public function clear_mailchimp_api_cache() {
		$nonce_mwd = MWD_Library::get('nonce_mwd');
		if(!wp_verify_nonce($nonce_mwd, 'nonce_mwd')) {
			MWD_Library::mwd_redirect(add_query_arg(array('message' => '10'), admin_url('admin.php?page=manage_mwd')));
		}
		
		$message_id = MWD_Library::get('message_id');
		require_once MWD_DIR . "/admin/models/MWDModelManage_mwd.php";
		$model = new MWDModelManage_mwd();

		require_once MWD_DIR . "/admin/views/MWDViewManage_mwd.php";
		$view = new MWDViewManage_mwd($model);
		
		
		$view->clear_mailchimp_api_cache($message_id);
	}
}