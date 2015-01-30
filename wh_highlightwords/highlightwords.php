<?php

if (!defined('FORUM')) die();

function wh_checkForNeedHighlightingWords() {
	global $forum_page;

	if (isset($_REQUEST['hl'])) {

		if (isset($forum_page['message']['message']))
			$forum_page['message']['message'] = wh_highlightKeyWords($forum_page['message']['message']);

		elseif (isset($forum_page['message']))
			$forum_page['message'] = wh_highlightKeyWords($forum_page['message']);


		elseif (defined('FORUM_PAGE')) {
			if (FORUM_PAGE=='help') {
				$tpl_temp = ob_get_contents();
				$tpl_temp = wh_highlightKeyWords($tpl_temp);
				ob_end_clean();
				ob_start();
				echo $tpl_temp;
			}
		}


	}
}


function wh_highlightKeyWords($text) {
	$keys = str_replace(" ", '|', $_REQUEST['hl']);
	$text = preg_replace_callback('#(<[^>]*>|'.$keys.')#iu', function($matches) {
			return ($matches[1][0]!='<') ? '<span class="shl">'.$matches[1].'</span>' : $matches[1];
		}, $text);
	return $text;
}
