<?php
return array(
    'URL_MODEL'                 => 2, //设置URL模式

    /*======== 语言配置 ========*/
    'LANG_SWITCH_ON'            => true,   // 开启语言包功能
    'DEFAULT_LANG'              => 'zh-cn', // 允许切换的语言列表 用逗号分隔

    'VIEW_PATH'                 => './template/',
    'TMPL_ACTION_ERROR'         => 'Public:message',
    'TMPL_ACTION_SUCCESS'       => 'Public:message',

    'URL_ROUTER_ON'   		    => true,
	'URL_ROUTE_RULES' 		    =>array(
        '/^article-(\d+)/'        	=> 'article/show?id=:1',
		'/^(\w+)-(\w+)/'			=> ':1/:2'
	),
);