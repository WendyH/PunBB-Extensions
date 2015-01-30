<?php

if (!defined('FORUM')) die();

$fxClass = 'cl-effect-16';

foreach ($links as $n => $link) {
	if (strpos($link, 'userlist.php')!=false) $link = '';
	if (preg_match('/(^.*?<li[^>]*?)(>.*)/i', $link, $matches)) {
		$p1 = $matches[1];
		$p2 = $matches[2];
		if (preg_match('/class=[\'"](.*?)[\'"]/i', $p1, $matches))
			$link = str_replace($matches[0], 'class="'.$matches[1].' '.$fxClass.'"', $link);
		else
			$link = $p1.' class="'.$fxClass.'"'.$p2;
	}

	if (strpos($link, 'data-hover=')==false) {
		if (preg_match('/(^.*?<a[^>]*?)(>.*)/i', $link, $matches)) {
			$t  = strip_tags($link);
			$p1 = $matches[1];
			$p2 = $matches[2];
			if (preg_match('/data-hover=[\'"](.*?)[\'"]/i', $p1, $matches))
				$link = str_replace($matches[0], 'data-hover="'.$t.'"', $link);
			else
				$link = $p1.' data-hover="'.$t.'"'.$p2;
		}
	}


	$links[$n] = $link;

}
