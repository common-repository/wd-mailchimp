<?php

class MWDControllerWidget extends WP_Widget {
	////////////////////////////////////////////////////////////////////////////////////////
	// Events                                                                             //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constants                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Variables                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	private $view;
	private $model;
	////////////////////////////////////////////////////////////////////////////////////////
	// Constructor & Destructor                                                           //
	////////////////////////////////////////////////////////////////////////////////////////
	public function __construct() {
		$widget_ops = array(
			'classname' => 'mwd_mailchimp_widget',
			'description' => 'Add Mailchimp by 10Web widget.'
		);
		// Widget Control Settings.
		$control_ops = array('id_base' => 'mwd_mailchimp_widget');
		// Create the widget.
		parent::__construct('mwd_mailchimp_widget', 'Mailchimp by 10Web', $widget_ops, $control_ops);
		require_once MWD_DIR . "/admin/models/MWDModelWidget.php";
		$this->model = new MWDModelWidget();

		require_once MWD_DIR . "/admin/views/MWDViewWidget.php";
		$this->view = new MWDViewWidget($this->model);
	}
	////////////////////////////////////////////////////////////////////////////////////////
	// Public Methods                                                                     //
	////////////////////////////////////////////////////////////////////////////////////////

	public function widget($args, $instance) {
		$this->view->widget($args, $instance);
	}

 	public function form( $instance ) {
		$this->view->form($instance, parent::get_field_id('title'), parent::get_field_name('title'), parent::get_field_id('form_id'), parent::get_field_name('form_id'));    
	}

	// Update Settings.
	public function update($new_instance, $old_instance) {
		$instance['title'] = $new_instance['title'];
		$instance['form_id'] = $new_instance['form_id'];
		return $instance;
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