<?php

class MWDModelManage_lists {

	public function get_lists() {
		$this->clear_mailchimp_api_cache();
    $api_key = get_option('mwd_api_key', '');
		$sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$sort_field_array = array('id', 'name', 'member', 'created');
		$sort_field = isset($_POST['order_by']) && in_array(sanitize_text_field(stripslashes($_POST['order_by'])), $sort_field_array) ? sanitize_text_field(stripslashes($_POST['order_by'])) : 'id';
		$start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1) : 0;
		$data = array(
		  'count' => 20,
		  'offset' => $start,
      'sort_field' => $sort_field,
      'sort_dir' => $sort_dir
    );
    $mwd_lists = MWD_Library::mailchimp_curl_connect( '/lists/', 'GET', $api_key, $data);
		return $mwd_lists;
	}
	
	public function get_lists_search() {
		$this->clear_mailchimp_api_cache();
    $api_key = get_option('mwd_api_key', '');

		$list_name_search = (isset($_POST['search_value']) && (sanitize_text_field($_POST['search_value']) != '')) ? sanitize_text_field($_POST['search_value']) : '';
		$sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
    $sort_field_array = array('id', 'name', 'date_created');
		$sort_field = isset($_POST['order_by']) && in_array(sanitize_text_field(stripslashes($_POST['order_by'])), $sort_field_array) ? sanitize_text_field(stripslashes($_POST['order_by'])) : 'date_created';
		$start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1)  : 0;

    $data = array(
      'fields'=>array('name'=>$list_name_search),
      'count' => 20,
      'offset' => $start,
      'sort_field' => 'date_created',
      'sort_dir' => $sort_dir
    );

    $mwd_lists = MWD_Library::mailchimp_curl_connect( '/lists/', 'GET', $api_key, $data );
    return $mwd_lists;
	}
	
	public function get_list_data( $list_id ) {
		$this->clear_mailchimp_api_cache();
    $api_key = get_option('mwd_api_key', '');

		$all_data = array();
    $mwd_list = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id, 'GET', $api_key );

    $sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
    $start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1) : 0;

    $data = array(
      'offset' => $start,
      'count' => 20,
      'sort_field'=> 'email_address',
      'sort_dir' => $sort_dir
    );
    $mwd_merge_vars = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id.'/merge-fields', 'GET', $api_key, $data );
		$mwd_merge_vars = $mwd_merge_vars->merge_fields;
		
		try {
      $mwd_interest_groups = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id.'/interest-categories', 'GET', $api_key );
      if ( isset($mwd_interest_groups) && !empty($mwd_interest_groups->categories) ) {
        foreach ( $mwd_interest_groups->categories as $interest_group ) {
          $group_id = $interest_group->id;
          $interest_groups_items = MWD_Library::mailchimp_curl_connect('/lists/' . $list_id . '/interest-categories/'.$group_id.'/interests', 'GET', $api_key);
          $mwd_interest_groups->categories[0]->groups = $interest_groups_items->interests;
        }
      }


    } catch( Exception $e ) {
			$mwd_interest_groups = $e->getMessage();
		}
		
		try {
      $mwd_segments = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id.'/segments', 'GET', $api_key );
    } catch( Exception $segment_error ) {
			$mwd_segments = $e->getMessage();
		}
		
		$all_data['list'] = $mwd_list;
		$all_data['merge_vars'] = $mwd_merge_vars;
		$all_data['interest_groups'] = $mwd_interest_groups;
		$all_data['segments'] = $mwd_segments;
		return $all_data;
	}
			
	public function get_subscribers_list( $list_id ) {
		$this->clear_mailchimp_api_cache();
    $api_key = get_option('mwd_api_key', '');
    $sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1) : 0;
		$data = array(
		  'sort_dir' => $sort_dir,
      'count' => 20,
      'offset' => $start
    );
    $subscribers_list = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id.'/members', 'POST', $api_key, $data );
    return $subscribers_list;
	}
	
	public function get_subscribers_list_search( $list_id ) {
		$this->clear_mailchimp_api_cache();
    $api_key = get_option('mwd_api_key', '');
		$email_search = (isset($_POST['search_value']) && (sanitize_text_field($_POST['search_value']) != '')) ? sanitize_text_field($_POST['search_value']) : '';
		$search_list = array();

    if( $email_search ) {
      $data = array(
        'query' => 'email:'.$email_search,
      );
      $search_list = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id.'/search-members', 'GET', $api_key, $data );

			$subscribers_list = array();
			$search_data = $search_list['full_search'];
			$subscribers_list['total'] = $search_data['total'];
			$subscribers_list['data'] = $search_data['members'];
	
		} else {
			$sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
			$sort_field_array = array('email', 'last_update_time');
			$sort_field = isset($_POST['order_by']) && in_array(sanitize_text_field(stripslashes($_POST['order_by'])), $sort_field_array) ? sanitize_text_field(stripslashes($_POST['order_by'])) : 'email';
			$start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1)  : 0;

      $data = array(
        'sort_dir' => $sort_dir,
        'count' => 20,
        'offset' => $start
      );

      $subscribers_list = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id.'/members', 'GET', $api_key, $data );
		}

		return $subscribers_list;
	}
	
	public function get_user_data( $sub_id, $list_id ) {
		$this->clear_mailchimp_api_cache();
    $api_key = get_option('mwd_api_key', '');
		try {
      $user_data = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id.'/members/'.$sub_id, 'GET', $api_key);

      $list_name = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id, 'GET', $api_key);
      $user_data->list_name = $list_name->name;

    } catch (Exception $e) {
			echo '<h4>Error: '. $e->getMessage().'</h4>';
			return;
		}

		return $user_data;
	}
	
	public function unsubscribe() {
    $api_key = get_option('mwd_api_key', '');

		$list_id = MWD_Library::get('list_id', 0);
		$sub_id = MWD_Library::get('email', 0);

		$data = array(
      "status" => "unsubscribed"
    );
		try {
      $unsubscribe_user = MWD_Library::mailchimp_curl_connect( '/lists/'.$list_id.'/members/'.$sub_id, 'PUT', $api_key, $data);
      $this->clear_mailchimp_api_cache();
			MWD_Library::mwd_redirect(add_query_arg(array('list_id' => $list_id, 'message' => '9'), admin_url('admin.php?page=manage_lists&task=view')));
		} catch ( Exception $e ) {
			$error_response = $e->getMessage();
			if ( strpos( $error_response, 'is not subscribed' ) !== false ) {
				MWD_Library::mwd_redirect(add_query_arg(array('list_id' => $list_id, 'message' => '13'), admin_url('admin.php?page=manage_lists&task=view')));
			}	
			else{
				MWD_Library::mwd_redirect(add_query_arg(array('list_id' => $list_id, 'message' => '12'), admin_url('admin.php?page=manage_lists&task=view')));
			}
		}	
		
	}
	
	public function clear_mailchimp_api_cache() {
		delete_transient('mwd-list-info');
		delete_transient('mwd-profile-info');
		delete_transient('mwd-account-details');
		delete_transient('mwd-lists');
		delete_transient('mwd-subscribers-data');
		delete_transient('mwd-list-data');
		delete_transient('mwd-subscriber-data');
	}
	
	public function page_nav( $call_name, $list_id = 0 ) {
		try {
			switch($call_name) {
				case 'lists':
					$data = $this->get_lists_search();
				break;
				case 'subscribers':
					$data = $this->get_subscribers_list_search( $list_id );
				break;
			}
			
		} catch (Exception $e) {
			//echo '<h4>Error: '. $e->getMessage().'</h4>';
			return;
		}
		$page_nav['total'] = $data->total_items;
		if (isset($_POST['page_number']) && $_POST['page_number']) {
			$limit = ((int) $_POST['page_number'] - 1) * 20;
		}
		else {
			$limit = 0;
		}
		$page_nav['limit'] = (int) ($limit / 20 + 1);
		return $page_nav;
	}
}