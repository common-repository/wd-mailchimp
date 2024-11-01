<?php

class MWDModelManage_mwd {

	public function __construct() {
	}

	public function mwd_lists() {
    $api_key = get_option('mwd_api_key', '');
    require_once MWD_DIR . "/admin/models/MWDModelHelper.php";
    $model = new MWDModelHelper();
    $model->mwd_validate_api($api_key, '/ping/');
    if (get_option('mwd_api_validation') == 'invalid_apikey') {
      $mchlists = null;
      return;
    }
    $mchlists = (array) MWD_Library::mailchimp_curl_connect( '/lists/', 'GET', $api_key);

    $lists = array();
		if ( is_array($mchlists) && $mchlists["total_items"]) {
			foreach ( $mchlists['lists'] as $list ) {
				$lists["{$list->id}"] = (object) array(
					'id' => $list->id,
					'name' => $list->name,
					'member_count' => $list->stats->member_count,
					'merge_vars' => array(),
					'interest_groups' => array()
				);
				
				try {
          $merge_variables = MWD_Library::mailchimp_curl_connect( '/lists/'.$list->id.'/merge-fields', 'GET', $api_key );
				} catch (Exception $e) {
					return;
				}
				$merge_vars["{$list->id}"] = $merge_variables->merge_fields;
				
				$interest_groups = array();
				try {
          $interest_groups = MWD_Library::mailchimp_curl_connect('/lists/' . $list->id . '/interest-categories', 'GET', $api_key);
          if ( isset($interest_groups) && !empty($interest_groups->categories) ) {
            foreach ( $interest_groups->categories as $interest_group ) {
              $group_id = $interest_group->id;
              $interest_groups_items = MWD_Library::mailchimp_curl_connect('/lists/' . $list->id . '/interest-categories/'.$group_id.'/interests', 'GET', $api_key);
              $interest_groups->categories[0]->groups = $interest_groups_items->interests;

            }
          }
        } catch( Exception $e ) {
				}	
				
				$lists["{$list->id}"]->interest_groups = $interest_groups;
			}

			if($merge_vars)
				foreach ($merge_vars as $list_id => $merge_var) {
					$lists["{$list_id}"]->merge_vars = array_map( array( $this, 'merge_vars' ), $merge_var );
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

		if ( isset( $merge_var->choices ) ) {
			$array['choices'] = $merge_var['choices'];
		}

		return (object) $array;
	}	

	public function mwd_account_details() {
    $api_key = get_option( 'mwd_api_key', '' );
    try {
      $account_details = $merge_variables = MWD_Library::mailchimp_curl_connect( '/', 'GET', $api_key );
		} catch (Exception $e) {
			return;
		}
		return $account_details; 
	}
	
}