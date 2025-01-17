<?php

class MWDControllerBlocked_ips {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct() {
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function execute() {
    $task = MWD_Library::get('task');
    $id = (int)MWD_Library::get('current_id', 0);
    $message = MWD_Library::get('message');
    echo MWD_Library::message_id($message);
    if (method_exists($this, $task)) {
		check_admin_referer('nonce_mwd', 'nonce_mwd');
		$this->$task($id);
    }
    else {
      $this->display();
    }
  }

  public function display() {
    require_once MWD_DIR . "/admin/models/MWDModelBlocked_ips.php";
    $model = new MWDModelBlocked_ips();

    require_once MWD_DIR . "/admin/views/MWDViewBlocked_ips.php";
    $view = new MWDViewBlocked_ips($model);
    $view->display();
  }

  public function save() {
    $message = $this->save_db();
    // $this->display();
    $page = MWD_Library::get('page');
    MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function save_db() {
    global $wpdb;
    $id = (int) MWD_Library::get('current_id', 0);
    $ip = MWD_Library::get('ip');
    if ($id != 0) {
      $save = $wpdb->update($wpdb->prefix . 'mwd_forms_blocked', array(
        'ip' => $ip,
      ), array('id' => $id));
    }
    else {
      $save = $wpdb->insert($wpdb->prefix . 'mwd_forms_blocked', array(
        'ip' => $ip,
      ), array(
				'%s',
      ));
    }
    if ($save !== FALSE) {
      $message = 1;
    }
    else {
      $message = 2;
    }
  }

  public function save_all() {
    global $wpdb;
    $flag = FALSE;
    $ips_id_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'mwd_forms_blocked');
    foreach ($ips_id_col as $ip_id) {
      if (isset($_POST['ip' . $ip_id])) {
        $ip = MWD_Library::get('ip' . $ip_id);
        if ($ip == '') {
          $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_blocked WHERE id="%d"', $ip_id));
        }
        else {
          $flag = TRUE;
          $wpdb->update($wpdb->prefix . 'mwd_forms_blocked', array(
            'ip' => $ip,
          ), array('id' => $ip_id));
        }
      }
    }
    if ($flag) {
      $message = 1;
    }
    else {
      $message = 0;
    }
    // $this->display();
    $page = MWD_Library::get('page');
    MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function delete($id) {
    global $wpdb;
    $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_blocked WHERE id="%d"', $id);
    if ($wpdb->query($query)) {
      $message = 3;
    }
    else {
      $message = 2;
    }
    // $this->display();
    $page = MWD_Library::get('page');
    MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }
  
  public function delete_all() {
    global $wpdb;
    $flag = FALSE;
    $ips_id_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'mwd_forms_blocked');
    foreach ($ips_id_col as $ip_id) {
      if (isset($_POST['check_' . $ip_id])) {
        $flag = TRUE;
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_blocked WHERE id="%d"', $ip_id));
      }
    }
    if ($flag) {
      $message = 5;
    }
    else {
      $message = 2;
    }
    // $this->display();
    $page = MWD_Library::get('page');
    MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
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