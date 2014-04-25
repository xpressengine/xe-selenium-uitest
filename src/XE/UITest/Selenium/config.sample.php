<?php
/**
  * copy to config.php
  */

date_default_timezone_set('ASIA/SEOUL');

return array(
	'DATABASE' => array(
		'type' => 'mysql',	// only support mysql
		'host' => 'mysql database host',
		'userid' => 'your id',
		'password' => 'your password',
		'port' => '3306',
		'database' => 'database prefix name',
		'table_prefix' => 'xe',	// XE table prefix name
		'database_type' => 'db_type_mysqli_innodb',	// XE database type
	),

	'SELENIUM' => array(
		'host' => 'selenium server host',
		'port' => '4444',
		'path' => '/wd/hub',
		'browser' => 'firefox',	//firefox, chrome, ie, internet explorer, opera, htmlunit, htmlunitjs, iphone, ipad, android
	),

	'TARGET_SERVER' => array(
		'url' => 'http://XE.install.host:port/',	// http://example.com:port
		'document_root' => '/path/to/DOCUMENT_ROOT/',
		'ssh_userid' => 'your id ',
		'ssh_password' => 'your password',
		'ssh_port' => "22",
	),

	'XE_INFO' => array(
		'version' => '1.7.4',	// specific version name
	),

	'INSTALL' => array(
		'prefix' => 'DATE',		// add Text to XE install Folder and Database DATE( mdHi ), NULL
		'overwrite'	=> true,	// if prefix value NULL then excute TEST_END_REMOVE commend and drop database
		'reset' => true,	// uninstall all XE, remove all XE installed Folder and Database by Controller::currentConfigPath
		'test_remove' => true,	// remove tested resource
	),

	'SOURCE_CMD' => array(
		'git clone -b develop https://github.com/xpressengine/xe-core.git ./',
		'mkdir files',
		'chmod 707 files',
	),

	'XE_ADMIN' => array(
		'email' => 'admin@admin.com',
		'password' => 'pass1234',
		'nickname' => 'admin',
		'id' => 'admin',
	),

	'XE_BOARD' => array(
		'mid' => 'testboard',
		'menu_name' => '테스트 게시판',
		'document' => array(
			'title' => 'test document title',
			'content' => 'test document content',
		),
		'comment' => array(
			'content' => 'test comment content',
		),
	),

	'XE_MEMBER' => array(
		array(
			'email' => 'test1@test.com',
			'password' => 'test1234',
			'id' => 'test1',
			'name' => 'test1name',
			'nickname' => 'test1nickname',
			'find_question' => '1',
			'find_answer' => '11',
		),
		array(
			'email' => 'test2@test.com',
			'password' => 'test1234',
			'id' => 'test2',
			'name' => 'test2name',
			'nickname' => 'test2nickname',
			'find_question' => '1',
			'find_answer' => '11',
		),
		array(
			'email' => 'test3@test.com',
			'password' => 'test1234',
			'id' => 'test3',
			'name' => 'test3name',
			'nickname' => 'test3nickname',
			'find_question' => '1',
			'find_answer' => '11',
		),
		array(
			'email' => 'test4@test.com',
			'password' => 'test1234',
			'id' => 'test4',
			'name' => 'test4name',
			'nickname' => 'test4nickname',
			'find_question' => '1',
			'find_answer' => '11',
		),
		array(
			'email' => 'test5@test.com',
			'password' => 'test1234',
			'id' => 'test5',
			'name' => 'test5name',
			'nickname' => 'test5nickname',
			'find_question' => '1',
			'find_answer' => '11',
		),
	),

);

