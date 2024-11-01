<?php

class MWDElementor extends \Elementor\Widget_Base {
  /**
   * Get widget name.
   *
   * @return string Widget name.
   */
  public function get_name() {
    return 'mwd-elementor';
  }

  /**
   * Get widget title.
   *
   * @return string Widget title.
   */
  public function get_title() {
    return __('Mailchimp', 'mwd-text');
  }

  /**
   * Get widget icon.
   *
   * @return string Widget icon.
   */
  public function get_icon() {
    return 'twbb-mailchimp twbb-widget-icon';
  }

  /**
   * Get widget categories.
   *
   * @return array Widget categories.
   */
  public function get_categories() {
    return ['tenweb-plugins-widgets'];
  }

  /**
   * Register widget controls.
   */
  protected function _register_controls() {
    $this->start_controls_section('section_general',
		[
			'label' => __('Mailchimp', 'mwd-text'),
		]
    );
    $forms = $this->get_forms();
    if($this->get_id() !== null){
          $settings = $this->get_init_settings();
    }
    $mwd_edit_link = add_query_arg(array( 'page' => 'manage_forms' ), admin_url('admin.php'));
   /* if ( isset($settings) && isset($settings["mailchimp_form_id"]) && intval($settings["mailchimp_form_id"]) > 0 ) {
      $mwd_id = intval($settings["mailchimp_form_id"]);
      $mwd_edit_link = add_query_arg(array( 'page' => 'manage_forms', 'task' => 'edit', 'current_id' => $mwd_id ), admin_url('admin.php'));
    }*/

		$this->add_control('mailchimp_form_id',
			[
				'label_block' => TRUE,
				'show_label' => TRUE,
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 0,
				'options' => $forms,
        'description' => __('Select the form to display.', 'mwd-text') .' <a target="_balnk" " href="'.$mwd_edit_link.'">' . __('Edit form', 'mwd-text') . '</a>',
			]
		);
    $this->end_controls_section();
  }




  protected function get_forms() {
    global $wpdb;
    $query = "SELECT `id`, `title`, `published` FROM `" . $wpdb->prefix . "mwd_forms` ORDER BY `title`";
    $rows = $wpdb->get_results($query);

    $forms = array();
    $forms[0] = __('Select a form', 'mwd-text');
    foreach ( $rows as $row ) {
      if( $row->published == 0) {
        $row->title .= ' - ' . __('Unpublished', 'mwd-text');
      }
      $forms[$row->id] = $row->title;
    }
    return $forms;
	}
  /**
   * Render widget output on the frontend.
   */
  protected function render() {
    $settings = $this->get_settings_for_display();
    if ( $settings['mailchimp_form_id'] ) {
      echo mwd_shortcode(array('id' => $settings['mailchimp_form_id']));
    }else {
      echo '<div class="fm-message fm-notice-error">' . __('There is no Form selected or the Form was deleted.', 'ecwd') . '</div>';
    }

  }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MWDElementor() );