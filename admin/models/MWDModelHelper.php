<?php

class MWDModelHelper {

	public function __construct() {
	}
  
	public function mwd_lists() {

    $api_key = get_option('mwd_api_key', '');

    // Query String Perameters are here
    // for more reference please vizit http://developer.mailchimp.com/documentation/mailchimp/reference/lists/
    $data = array(
      'sort_field' => 'date_created',
      'sort_dir' => 'DESC',
    );

    $result = MWD_Library::mailchimp_curl_connect( '/lists/', 'GET', $api_key, $data);
    $lists = array();
    if( !empty($result->lists) &&  $result->total_items ) {

        foreach ( $result->lists as $list ) {
          $lists["{$list->id}"] = array(
            'id' => $list->id,
            'name' => $list->name,
            'member_count' => $list->stats->member_count,
            'merge_vars' => array(),
            'interest_groups' => array(),
            'web_id' => $list->web_id
          );

          $merge_variables = array();
          $merge_vars = array();
          try {
            $merge_variables = MWD_Library::mailchimp_curl_connect( '/lists/'.$list->id.'/merge-fields', 'GET', $api_key );

          } catch (Exception $e) {
            echo '<h4>Error: '. $e->getMessage().'</h4>';
            return;
          }

          if( isset($merge_variables) ){
            $merge_vars["{$list->id}"] = $merge_variables->merge_fields;
          }
          $interest_groups = array();
          try {
            $interest_groups = MWD_Library::mailchimp_curl_connect( '/lists/'.$list->id.'/interest-categories', 'GET', $api_key );
            if ( isset($interest_groups) && !empty($interest_groups->categories) ) {
              foreach ( $interest_groups->categories as $interest_group ) {
                $group_id = $interest_group->id;
                $interest_groups_items = MWD_Library::mailchimp_curl_connect('/lists/' . $list->id . '/interest-categories/'.$group_id.'/interests', 'GET', $api_key);
                $interest_groups->categories[0]->groups = $interest_groups_items->interests;
              }
            }

          } catch( Exception $e ) {
          }
          if( isset($interest_groups) ) {
            $lists["{$list->id}"]["interest_groups"] = $interest_groups->categories;
          }
        }
        if($merge_vars){
          foreach ($merge_vars as $list_id => $merge_var) {
            $lists["{$list_id}"]["merge_vars"] = array_map( array( $this, 'merge_vars' ), $merge_var );
          }
        }
    }
    return $lists;
	}
	
	public function merge_vars( $merge_var ) {
    $merge_var = (array) $merge_var;
		$array = array(
			'name' => $merge_var['name'],
			'field_type' => $merge_var['type'],
			'req' => $merge_var['required'],
			'tag' => $merge_var['tag'],
		);

		if ( isset( $merge_var['choices'] ) ) {
			$array['choices'] = $merge_var['choices'];
		}

		return (object) $array;
	}

	public function mwd_account_details() {
    $api_key = get_option( 'mwd_api_key', '' );
    try {
			$account_details = MWD_Library::mailchimp_curl_connect( '/', 'GET', $api_key );
		} catch (Exception $e) {
			//echo '<h4>Error: '. $e->getMessage().'</h4>';
			return;
		}
		
		return $account_details; 
	}

  function mwd_validate_api( $apikey, $api_url ) {
		try {
			$validate_apikey_response = MWD_Library::mailchimp_curl_connect( $api_url, 'GET', $apikey );
      if( !is_null($validate_apikey_response) ) {
        update_option( 'mwd_api_validation' , 'valid_apikey' );
        update_option('mwd_api_key', $apikey);
      } else {
        update_option('mwd_api_key', $apikey);
        update_option( 'mwd_api_validation' , 'invalid_apikey' );
        return __("Invalid API key", "mwd-text");
      }
		} catch ( Exception $e ) {
			update_option('mwd_api_key', $apikey);
			update_option( 'mwd_api_validation' , 'invalid_apikey' );
			return $e->getMessage();
		}
		return $apikey;
	}
}