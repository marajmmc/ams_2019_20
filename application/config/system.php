<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['system_site_short_name']='ams';
$config['offline_controllers']=array('home','sys_site_offline');
$config['external_controllers']=array('home');//user can use them without login
$config['system_max_actions']=8;

$config['system_site_root_folder']='ams_2019_20';
$config['system_upload_image_auth_key']='ams_2019_20';
$config['system_upload_api_url']='http://45.251.59.5/api_file_server/upload';

$config['system_status_yes']='Yes';
$config['system_status_no']='No';
$config['system_status_active']='Active';
$config['system_status_inactive']='In-Active';
$config['system_status_delete']='Deleted';
$config['system_status_closed']='Closed';
$config['system_status_pending']='Pending';
$config['system_status_forwarded']='Forwarded';
$config['system_status_complete']='Complete';
$config['system_status_approved']='Approved';
$config['system_status_delivered']='Delivered';
$config['system_status_received']='Received';
$config['system_status_rejected']='Rejected';
$config['system_status_rollback']='Rollback';

$config['system_base_url_profile_picture']='http://45.251.59.5/login_2018_19/';
$config['system_base_url_picture']='http://45.251.59.5/ams_2019_20/';

// customer type (Outlet)
$config['system_customer_type_outlet_id']=1;
$config['system_customer_type_customer_id']=2;

//System Configuration
    //login
$config['system_purpose_login_max_wrong_password']='login_max_wrong_password';
$config['system_purpose_login_status_mobile_verification']='login_status_mobile_verification';//for all commons
    //bms
$config['system_purpose_bms_menu_odd_color']='ams_menu_odd_color';
$config['system_purpose_bms_menu_even_color']='ams_menu_even_color';

