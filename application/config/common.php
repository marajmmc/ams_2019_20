<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$configs_path=str_replace('ams_2019_20','login_2018_19',APPPATH).'config/';

require_once($configs_path.'user_group.php');
require_once($configs_path.'table_ams.php');
require_once($configs_path.'table_login.php');
