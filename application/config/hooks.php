<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | Hooks
  | -------------------------------------------------------------------------
  | This file lets you define "hooks" to extend CI without hacking the core
  | files.  Please see the user guide for info:
  |
  |	https://codeigniter.com/user_guide/general/hooks.html
  |
 */
// a hook can be defined in the application/config/hooks.php

$hook['post_controller_constructor'][] = array(
	'class' => '',
	'function' => 'rewrite_base_url',
	'filename' => 'uri.php',
	'filepath' => 'hooks'
	);