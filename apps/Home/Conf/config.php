<?php
return array(
    'URL_MODEL'                 => 2, //设置URL模式

    /*======== 语言配置 ========*/
    'LANG_SWITCH_ON'            => true,   // 开启语言包功能
    'DEFAULT_LANG'              => 'zh-cn', // 允许切换的语言列表 用逗号分隔

    'VIEW_PATH'                 => './template/',
    'DEFAULT_THEME'             => 'web',
    'THEME_LIST'                => 'web,mobile',
    'TMPL_ACTION_ERROR'         => 'public:message',
    'TMPL_ACTION_SUCCESS'       => 'public:message',
);