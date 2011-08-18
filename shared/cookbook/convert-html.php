<?php if (!defined('PmWiki')) exit();

/*	=== ConvertHTML ===
 *	Copyright 2008 Eemeli Aro <eemeli@gmail.com>
 *
 *	A set of replace-on-edit rules for automatically converting
 *	HTML to PmWiki markup
 *
 *	Developed and tested using the PmWiki 2.2.0-beta series.
 *
 *	To install, add the following line to your configuration file :
		include_once("$FarmD/cookbook/convert-html.php");
 *
 *	For more information, please see the online documentation at
 *		http://www.pmwiki.org/wiki/Cookbook/ConvertHTML
 *
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License,
 *	Version 2, as published by the Free Software Foundation.
 *	http://www.gnu.org/copyleft/gpl.html
 */

$RecipeInfo['ConvertHTML']['Version'] = '2008-10-07';

SDVA( $ROEPatterns, array(
	'#<title>\s*(.*?)\*s</title>\n*#is' => "(: title $1:)\n",
	'#<meta name=([\'"])(keywords|description)\1 content=([\'"])(.*?)\3 */>\n*#i' => "(:$2 $4:)\n",
	'#<!--(.*?)-->#' => "%comment% $1 %%",
	'#<(ul|ol|dl)\s+([^>]+)>\s*(<(li|dt)\b[^>]*>)#i' => '<$1>$3%apply=list $2% ',
	'#<(li|dt)\s+([^>]+)>#i' => "<$1>%apply=item $2% ",
	'#\s*(<(ul|ol)\s*>\s*<li.*</\2>)\n?#ise' => 'ConvertHtmlList(stripmagic("$1"))',
	'#\s*<dt\s*>(.*?)(?:</dt>)?\s*<dd\s*>\s*(.*?)(?:</dd>)?\n#is' => "\n:$1:$2\n",
	'#\s*</?dl\s*>#i' => '',
	'#\s*<table( [^>]*)?>(.*?)\s*</table>#is' => "\n(:table$1:)$2\n(:tableend:)",
	'#\s*<td( [^>]*)?>(.*?)</td>#is' => "\n(:cell$1:)$2",
	'#\s*<tr( [^>]*)?>\s*\(:cell\b(.*?)</tr>#is' => "\n(:cellnr$1$2",
	'#<(p|h\d)\s+([^>]+)>#i' => "<$1>%block $2% ",
	'#\s*<h(\d)\s*>(.*?)</h\1>\n*#ise' => "\"\n\n\".str_repeat('!','$1').' '.stripmagic('$2').\"\n\"",
	'#\s*<p\s*>\s*(.*?)</p>\n?#is' => "\n\n$1\n",
	'#\s*<p\s*>\s*#is' => "\n\n",
	'#\s*<div( [^>]*)?>\s*(.*?)\n?</div>\n*#is' => "\n(:div$1:)\n$2\n(:divend:)\n",
	'#<span\b\s*([^>]*)>(.*?)</span>#ise' => '"%".ConvertHtmlSpan(stripmagic("$1"))."% $2 %%"',
	'#\s*<blockquote>\s*(.*?)</blockquote>\n*#is' => "\n->$1\n",
	'#<br\s+clear=[\'"]?(all|left|right)[\'"]?\s*/?>\n*#i' => "[[<<]]\n",
	'#<br */?>\n*#i' => "\\\\\\\n",
	'#\s*<hr */?>\n*#i' => "\n----\n",
	'#</?(i|em)>#i' => "''",
	'#</?(b|strong)>#i' => "'''",
	'#</?(code|tt)>#i' => "@@",
	'#<pre>(.*?)</pre>#is' => "[@$1@]",
	'#<big>(.*?)</big>#is' => "'+$1+'",
	'#<small>(.*?)</small>#is' => "'-$1-'",
	'#<sup>(.*?)</sup>#is' => "'^$1^'",
	'#<sub>(.*?)</sub>#is' => "'_$1_'",
	'#<ins>(.*?)</ins>#is' => "{+$1+}",
	'#<del>(.*?)</del>#is' => "{-$1-}",
	'#(<(?:a|img)\b[^>]+\b(href|src)=)([\'"])([./][^\'"]*?)\3#i' => "$1$3Path:$4$3",
	'#(<(?:a|img)\b[^>]+\b(href|src)=)([\'"])([^/:\'"]+?)\3#i' => "$1$3Attach:$4$3",
	'#<a\s[^>]*\bname=([\'"])([^\'"]*?)\1[^>]*>(.*?)</a>#ise' => '"[[#".preg_replace("/\s+/","_",PSS("$2")).\']] $3\'',
	'#<a\s[^>]*\bhref=([\'"])([^\'"]*?)\1[^>]*>(.*?)</a>#is' => "[[$2|$3]]",
	'#<img\s([^>]*)\bsrc=([\'"])([^\'"]*?)\2\s([^>]*?)\s*(?:/?|></img)>\n?#i' => "%apply=img $1$4%$3%%\n",
	'#(.*%apply=img\b[^%]+)\balign=([\'"]?)(l|r)(?:eft|ight)\2([^%]*%[^%]+)%%#i' => '%$3float% $1$4%%',
	'#(%apply=img\b[^%]+)\b(?:alt|title)=([\'"])([^\'"]+?)\2([^%]*%[^%]+)%%#i' => '$1$4"$3"%%',
	'#%apply=img\b(?:\s+(?:alt|title)=([\'"])\s*\1)*\s*%([^%]+)%%#' => '$2',
));

function ConvertHtmlSpan($param) {
	return preg_replace(
		array( '/%/', '/(?:class|style)=([\'"])(.*?)\1/' ),
		array( 'pct', '$2' ),
		$param );
}

function ConvertHtmlList($html) {
	$out = '';
	$lit = array();
	$strip = FALSE;
	$html = preg_replace('#(</?(?:ol|ul|li))\b([^>]+)>#i','$1>',$html);
	$lia = preg_split( '#\s*(</?(?:ol|ul|li)\s*>)\s*#i', $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
	foreach( $lia as $n ) switch($n) {
		case '<ul>': case '<UL>':
			$lit[] = "\n".str_repeat( '*', count($lit)+1 ).' ';
			break;
		case '<ol>': case '<OL>':
			$lit[] = "\n".str_repeat( '#', count($lit)+1 ).' ';
			break;
		case '</ul>': case '</UL>':
		case '</ol>': case '</OL>':
			array_pop($lit);
			$strip = FALSE;
			break;
		case '<li>': case '<LI>':
			if($lit) $out .= end($lit);
			$strip = TRUE;
			break;
		case '</li>': case '</LI>':
			$strip = FALSE;
			break;
		default:
			if ($strip) {
				$out .= preg_replace('/\s+/',' ',$n);
				$strip = FALSE;
			} else $out .= $n;
	}
	return $out;
}

