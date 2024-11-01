<?php

class MWDViewFormsPreview {
	////////////////////////////////////////////////////////////////////////////////////////
	// Events                                                                             //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constants                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Variables                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	private $model;


	////////////////////////////////////////////////////////////////////////////////////////
	// Constructor & Destructor                                                           //
	////////////////////////////////////////////////////////////////////////////////////////
	public function __construct($model) {
	$this->model = $model;
	}
	////////////////////////////////////////////////////////////////////////////////////////
	// Public Methods                                                                     //
	////////////////////////////////////////////////////////////////////////////////////////
	public function display() {
		require_once(MWD_DIR . '/includes/mwd_library.php');
		$form_id = ((isset($_GET['form_id'])) ? (int)$_GET['form_id'] : 0);
		$form = (($form_id) ? $this->model->get_form($form_id) : '');
		$google_fonts = MWD_Library::mwd_get_google_fonts();
		$fonts = implode("|", str_replace(' ', '+', $google_fonts));
		wp_print_scripts('jquery');
		wp_print_scripts('jquery-ui-widget');
		wp_print_scripts('jquery-ui-slider');
		wp_print_scripts('jquery-ui-spinner');
		$ver = rand();
		wp_register_style('mwd_jquery_ui', MWD_URL . '/css/jquery-ui-1.10.3.custom.css', array(), $ver);
		wp_register_style('mwd_jquery_spinner', MWD_URL . '/css/jquery-ui-spinner.css', array(), $ver);
		wp_register_style('mwd_frontend', MWD_URL . '/css/frontend/mwd-mailchimp-frontend.css', array(), $ver);
		wp_register_style('mwd_frontend-' . $form_id, MWD_URL . '/css/frontend/mwd-style-'.$form_id.'.css', array(), $ver);
		wp_register_style('mwd_google_fonts', 'https://fonts.googleapis.com/css?family=' . $fonts . '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic', array(), $ver);
		wp_print_styles(array('mwd_jquery_ui', 'mwd_jquery_spinner', 'mwd_frontend', 'mwd_frontend-' . $form_id, 'mwd_google_fonts'));
		?>
		<style>
			body{
				margin: 0 !important;
			}
		</style>
		<?php
		wp_print_scripts('jquery-effects-shake');
		wp_register_script('mwd_main_frontend', MWD_URL . '/js/mwd_main_frontend.js', array(), get_option("mwd_version"));
		require_once (MWD_DIR . '/frontend/controllers/MWDControllerForms.php');
		$controller = new MWDControllerForms();
		echo $controller->execute($form_id);
			
		die();
	}
  
  public function save_preview() {
  $id = ((isset($_GET['form_id'])) ? (int)$_GET['form_id'] : 0);
  $preview_img_url = MWD_Library::get('preview_img_url', '');
  if ($preview_img_url) {
      $filteredData = substr($preview_img_url, strpos($preview_img_url, ",") + 1);
      $unencodedData = base64_decode($filteredData);
      $upload_dir = wp_upload_dir();
      $file_path = $upload_dir['basedir'] . '/wd-mailchimp';
      if(!file_exists($file_path))
        mkdir($file_path , 0777);
      $fp = fopen( $file_path.'/form-preview-'.$id.'.png', 'wb' );
      fwrite( $fp, $unencodedData);
      fclose( $fp );
    }
    die();
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
