<?php
// Bilibili设定
//是否允许代码弹幕(高级弹幕)
$BiliEnableSA = TRUE;

$PlayerSet->add('bi20111013', new Player('bi20111013.swf', 'bilibili播放器(20111013)', 950, 482))
		  ->addDefault('bi20111013');

$VideoSourceSet->add('yk', new YouKuSource());

//弹幕权限表
$BilibiliAuthLevel = new DefinedEnum( array
(
    'DefaultLevel' => '10000,1001',
	'Guest'	=> '0',
	'User'	=> '10000,1001',
	'Danmakuer' => '20000,1001'
));
