<?php if (!defined('PmWiki')) exit();

/**	=== Bloge-Tags ===
 *	Copyright 2009 Eemeli Aro <eemeli@gmail.com>
 *
 *	Use page keywords and categories as tags
 *
 *	Developed and tested using PmWiki 2.2.x
 *
 *	To use, add the following to a configuration file:

		include_once("$FarmD/cookbook/bloge-tags.php");

 *	This is a part of the Bloge bundle of recipes, but may be used by itself.
 *	For more information, please see the online documentation at
 *		http://www.pmwiki.org/wiki/Cookbook/Bloge-Tags and at
 *		http://www.pmwiki.org/wiki/Cookbook/Bloge
 *
 *	This program is free software; you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation; either version 2 of the License, or
 *	(at your option) any later version.
 */

$RecipeInfo['Bloge-Tags']['Version'] = '2009-10-13';

## {$TagList} gives a list of links to tags, {$TagCount} the number or tags
$FmtPV['$TagList'] = 'BlogeTagList($pn)';
$FmtPV['$TagCount'] = 'BlogeTagList($pn, TRUE)';

$SaveAttrPatterns['/\\{[^{]*\\$TagList\\}/'] = ' ';

## overload (:keywords:) directive to add links to $page['targets']
Markup('keywords', 'directives', "/\\(:keywords?\\s+(.+?):\\)/ei",
	"PZZ(BlogeTagKeywords(\$pagename, PSS('$1')))");
function BlogeTagKeywords($pagename, $kwlist) {
	global $CategoryGroup, $BlogeTag, $LinkTargets;

	$kwlist = SetProperty($pagename, 'keywords', $kwlist, ', ');

	SDV($BlogeTag['group'], $CategoryGroup);
	if (empty($BlogeTag['group'])) return;

	$kwa = preg_split('/\s*,\s*/', $kwlist, -1, PREG_SPLIT_NO_EMPTY);
	if (!empty($kwa)) foreach ( $kwa as $kw ) {
		$kwp = MakePageName($pagename, "{$BlogeTag['group']}/$kw");
		if ( !$kwp || !empty($LinkTargets[$kwp]) ) continue;
		$LinkTargets[$kwp] = 1;
	}
}

function BlogeTagList($pagename, $count=FALSE) {
	global $PCache, $CategoryGroup, $BlogeTag;

	SDVA($BlogeTag, array(
		'group' => $CategoryGroup,
		'linkfmt' => '[[!$TagName]]',
		'prefix' => '$[Tags]: ',
		'separator' => ', '
	));
	if (empty($BlogeTag['group'])) return '';

	preg_match_all("/\b{$BlogeTag['group']}\.([^,]+)/", @$PCache[$pagename]['targets'], $m, PREG_SET_ORDER);
	if (!$m) return '';
	if ($count) return count($m);

	$ta = array();
	if (strpos($BlogeTag['linkfmt'],'<') !== FALSE)
		foreach( $m as $tag ) $ta[] = Keep(MakeLink($pagename, $tag[0], $tag[1],'', $BlogeTag['linkfmt']),'L');
	else
		foreach( $m as $tag ) $ta[] = preg_replace(array('/\$TagFullName\b/','/\$TagName\b/'), $tag, $BlogeTag['linkfmt']);
	return $BlogeTag['prefix'].implode($BlogeTag['separator'], $ta);
}

SDV($FPLFormatOpt['tags'], array('fn' => 'BlogeTagFPL'));
function BlogeTagFPL($pagename, &$matches, $opt) {
	global $CategoryGroup, $BlogeTag;

	SDVA($BlogeTag, array(
		'group' => $CategoryGroup,
		'min-size' => 80,
		'max-size' => 160,
		'listfmt' => '%font-size=$sizepct%[[!$tag]]%% ',
		'tagfilter' => array()
	));
	if (empty($BlogeTag['group'])) return;

	$tagorder = @$opt['order'];
	unset($opt['order']);
	$matches = MakePageList($pagename, $opt, 1);
	if (empty($matches)) return '';

	$tags = array();
	foreach($matches as $page) {
		preg_match_all("/\b{$BlogeTag['group']}\.([^,]+)/", @$page['targets'], $tm);
		if ($tm) foreach( $tm[1] as $t ) {
			if (empty($tags[$t])) $tags[$t] = 1;
			else ++$tags[$t];
		}
	}
	if (empty($tags)) return '';

	$filter = (array)$BlogeTag['tagfilter'];
	if (!empty($opt['tagfilter'])) $filter = array_merge($filter, preg_split('/[,\s]+/', $opt['tagfilter'], PREG_SPLIT_NO_EMPTY));
	if (!empty($filter)) {
		$tags_ok = array_flip(MatchPageNames(array_keys($tags), $filter));
		foreach($tags as $t => $c) if (!isset($tags_ok[$t])) unset($tags[$t]);
	}
	if (empty($tags)) return '';

	switch($tagorder) {
		case 'name':  ksort($tags);  break;
		case '-name': krsort($tags); break;
		case 'freq':  asort($tags);  break;
		case '-freq': arsort($tags); break;
		default:      ksort($tags);
	}

	##  extract tag subset according to 'count=' parameter
	if (!empty($opt['count'])) {
		list($r0, $r1) = CalcRange($opt['count'], count($tags));
		$tags = ($r1 < $r0)
			? array_reverse(array_slice($tags, $r1-1, $r0-$r1+1))
			: array_slice($tags, $r0-1, $r1-$r0+1);
	}
	if (empty($tags)) return '';

	$out = '';
	$mult = ($BlogeTag['max-size']-$BlogeTag['min-size']) / (max($tags)-0.9999);
	foreach($tags as $tag => $count)
		$out .= str_replace(
			array('$tag', '$count', '$group',           '$size'),
			array( $tag,   $count,  $BlogeTag['group'], round($BlogeTag['min-size'] + ($count-1) * $mult)),
			$BlogeTag['listfmt']);
	return $out;
}

