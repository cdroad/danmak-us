<?php
// Bilibili设定
//是否允许代码弹幕(高级弹幕)
$BiliEnableSA = TRUE;

$PlayerSet->add('bi20110712', new Player('bi20110712.swf', 'bilibili播放器(20110712)', 950, 482))
		  ->add('bi20110807', new Player('bi20110807.swf', 'bilibili播放器(20110807)', 950, 482))
		  ->addDefault(bi20110807);

$VideoSourceSet->add('yk', new YouKuSource());

//弹幕权限表
$BilibiliAuthLevel = new DefinedEnum( array
(
    'DefaultLevel' => '10000,1001',
	'Guest'	=> '0',
	'User'	=> '10000,1001',
	'Danmakuer' => '20000,1001'
));
