<?php

class MWDControllerSubmissions_mwd {
	public function execute() {
    $apikey = get_option('mwd_api_key', '');
    require_once MWD_DIR . "/admin/models/MWDModelHelper.php";
    $model = new MWDModelHelper();
    $model->mwd_validate_api($apikey, '/ping/');
		$task = MWD_Library::get('task', '');
		$id = (int) MWD_Library::get('current_id', 0);
		$form_id = (int) MWD_Library::get('form_id', 0);

		if (method_exists($this, $task)) {
			if($task != 'show_stats' && $task != 'view_submit')
				check_admin_referer('nonce_mwd', 'nonce_mwd');
			else
				check_ajax_referer('nonce_mwd_ajax', 'nonce_mwd_ajax');
			
			$this->$task($id); 
		}
		else {
			$this->display($form_id); 
		}
	}
  

	public function display() {
    $form_id = (int) MWD_Library::get('form_id', 0);
		require_once MWD_DIR . "/admin/models/MWDModelSubmissions_mwd.php";
		$model = new MWDModelSubmissions_mwd();

		require_once MWD_DIR . "/admin/views/MWDViewSubmissions_mwd.php";
		$view = new MWDViewSubmissions_mwd($model);
		$view->display($form_id);
	}
	
	public function view_submit($form_id) {
		$form_id = ((isset($_GET['form_id']) && $_GET['form_id'] != '') ? (int)($_GET['form_id']) : 0);
		$group_id = ((isset($_GET['group_id']) && $_GET['group_id'] != '') ? (int)($_GET['group_id']) : 0);

		require_once MWD_DIR . "/admin/models/MWDModelSubmissions_mwd.php";
		$model = new MWDModelSubmissions_mwd();

		require_once MWD_DIR . "/admin/views/MWDViewSubmissions_mwd.php";
		$view = new MWDViewSubmissions_mwd($model);
		$view->view_submit($form_id, $group_id);
		die();
	}

	public function show_stats() {
    $form_id = (int) MWD_Library::get('form_id', 0);
		require_once MWD_DIR . "/admin/models/MWDModelSubmissions_mwd.php";
		$model = new MWDModelSubmissions_mwd();

		require_once MWD_DIR . "/admin/views/MWDViewSubmissions_mwd.php";
		$view = new MWDViewSubmissions_mwd($model);
		$view->show_stats($form_id);
	}


	public function delete($id) {
		global $wpdb;
    $form_id = (int) MWD_Library::get('form_id', 0);
		$query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_submits WHERE group_id="%d"', $id);

		if ($wpdb->query($query)) {
			echo MWD_Library::message('Item Succesfully Deleted.', 'updated');
		}
		else {
			echo MWD_Library::message('Error. Please install plugin again.', 'error');
		}    
		$this->display($form_id);
	}

  public function parse_int( &$value ) {
    $value = (int) $value;
  }

	public function delete_all() {
		global $wpdb;
    $form_id = (int) MWD_Library::get('form_id', 0);
		$cid = MWD_Library::get('post', NULL);
		if (is_array($cid) && count($cid)) {
      array_walk($cid, array( $this, 'parse_int' ));
			$cids = implode(',', $cid);
			$query = 'DELETE FROM ' . $wpdb->prefix . 'mwd_forms_submits WHERE group_id IN ( ' . $cids . ' )';
     
			if ($wpdb->query($query)) {
				echo MWD_Library::message('Items Succesfully Deleted.', 'updated');
			}
			else {
				echo MWD_Library::message('Error. Please install plugin again.', 'error');
			}
		}
		else {
			echo MWD_Library::message('You must select at least one item.', 'error');
		}
		$this->display($form_id);
	}

	public function block_ip() {
		global $wpdb;
		$flag = FALSE;
    $form_id = (int) MWD_Library::get('form_id', 0);
		$cid = ((isset($_POST['post']) && $_POST['post'] != '') ? $_POST['post'] : NULL);
		if (count($cid)) {
      array_walk($cid, array( $this, 'parse_int' ));
			$cids = implode(',', $cid);
			$query = 'SELECT * FROM ' . $wpdb->prefix . 'mwd_forms_submits WHERE group_id IN ( '. $cids .' )';
			$rows = $wpdb->get_results($query);
			foreach ($rows as $row) {
				$ips = $wpdb->get_var($wpdb->prepare('SELECT ip FROM ' . $wpdb->prefix . 'mwd_forms_blocked WHERE ip="%s"', $row->ip));
				$flag = TRUE;
				if (!$ips) {
					$save = $wpdb->insert($wpdb->prefix . 'mwd_forms_blocked', array(
						'ip' => $row->ip,
					), array(
						'%s',
					));
				}
			}
		}
		if ($flag) {
			echo MWD_Library::message('IPs Succesfully Blocked.', 'updated');
		}
		else {
			echo MWD_Library::message('You must select at least one item.', 'error');
		}
		$this->display($form_id);
	}

	public function unblock_ip() {
		global $wpdb;
		$flag = FALSE;
    $form_id = (int) MWD_Library::get('form_id', 0);
    $cid = MWD_Library::get('post', NULL);
		if (is_array($cid) && count($cid)) {
      array_walk($cid, array( $this, 'parse_int' ));
			$cids = implode(',', $cid);
			$query = 'SELECT * FROM ' . $wpdb->prefix . 'mwd_forms_submits WHERE group_id IN ( '. $cids .' )';
			$rows = $wpdb->get_results($query);
			foreach ($rows as $row) {
				$flag = TRUE;
				$ips = $wpdb->get_var($wpdb->prepare('SELECT ip FROM ' . $wpdb->prefix . 'mwd_forms_blocked WHERE ip="%s"', $row->ip));
				if ($ips) {
					$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_blocked WHERE ip="%s"', $ips));
				}
			}
		}
		if ($flag) {
			echo MWD_Library::message('IPs Succesfully Unblocked.', 'updated');
		}
		else {
			echo MWD_Library::message('You must select at least one item.', 'error');
		}
		$this->display($form_id);
	}

	////////////////////////////////////////////////////////////////////////////////////////
	// Getters & Setters                                                                  //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Private Methods                                                                    //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}