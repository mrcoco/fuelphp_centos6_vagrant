<?php
return array(
	'_root_'  => 'welcome/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
	
	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),

	/**
	 * ページネーションの URL 設定
	 * 'admin/controller_name/(:num)'=> 'admin/controller_name/index/$1',
	 */
);
