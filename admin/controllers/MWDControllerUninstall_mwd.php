<?php
class MWDControllerUninstall_mwd {
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
		global  $mwd_options;
		if(!class_exists("TenWebLibConfig")){
				include_once (MWD_DIR . "/wd/config.php"); 	
		}
		$config = new TenWebLibConfig();

		$config->set_options( $mwd_options );
		
		$deactivate_reasons = new TenWebLibDeactivate($config);
		//$deactivate_reasons->add_deactivation_feedback_dialog_box();	
		$deactivate_reasons->submit_and_deactivate(); 
	}
	////////////////////////////////////////////////////////////////////////////////////////
	// Public Methods                                                                     //
	////////////////////////////////////////////////////////////////////////////////////////
	public function execute() {
		$task = MWD_Library::get('task', '');
		if (method_exists($this, $task)) {
			check_admin_referer('nonce_mwd', 'nonce_mwd');
			$this->$task();
		}
		else {
			$this->display();
		}
	}

	public function display() {
		require_once MWD_DIR . "/admin/models/MWDModelUninstall_mwd.php";
		$model = new MWDModelUninstall_mwd();

		require_once MWD_DIR . "/admin/views/MWDViewUninstall_mwd.php";
		$view = new MWDViewUninstall_mwd($model);
		$view->display();
	}

	public function mwd_uninstall() {
		require_once MWD_DIR . "/admin/models/MWDModelUninstall_mwd.php";
		$model = new MWDModelUninstall_mwd();

		require_once MWD_DIR . "/admin/views/MWDViewUninstall_mwd.php";
		$view = new MWDViewUninstall_mwd($model);
		$view->mwd_uninstall();
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