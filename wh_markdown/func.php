<?php
if (!defined('FORUM')) die();
include_once dirname(__FILE__) . '/Markdown/Markdown.inc.php';

function wh_csspack($css) {
	$css = preg_replace_callback('#\s*\{([^\}]*)\}\s*#m', function($m) {
		$h = preg_replace('/[\s\r\n\t]+/m', ' ', trim($m[1]));
		$h = preg_replace('/\s*([:;])\s*/', '$1', $h);
		$h = preg_replace('/;$/', '', $h);
		$h = preg_replace('/:0(em|px|pt)$/', ':0', $h);
		return '{' . $h . '}';
	}, $css);
	$css = preg_replace('/\/\*((?:.|[\s\t\r\n])*?)\*\//m', '', $css);
	$css = preg_replace('/[\r\n]+/', "", $css);
	return trim($css);
}

function wh_LoadCSS() {
	global $ext_info;
        $csstext = "";
	$cssfile = $ext_info['path'].'/wh_markdown.css';
	if (file_exists($cssfile)) {
		$csstext = file_get_contents($cssfile);
		$csstext = wh_csspack($csstext);
		$csstext = str_replace('(img/', '('.$ext_info['path'].'/', $csstext);
	}
	return $csstext;
}

// Hide markdown block into the [code]...[/code] and make safe [code] in
function wh_MDHide($text) {
	return preg_replace_callback('#(\[markdown(?:`[^`]*`|[\s\S])+?\[\/markdown])#s', function ($matches) {
			return '[code]MD!'.base64_encode($matches[0]).'!MD[/code]';
		}, $text);
}

// Unhide markdown block from [code]...[/code]
function wh_MDUnhide($text) {
	$text = str_replace(array('</p><div class="codebox"><pre><code>MD!', '!MD</code></pre></div><p>'), array('[code]MD!', '!MD[/code]'), $text);
	$text = preg_replace_callback('#\[code]MD!(.*?)!MD\[\/code]#s', function ($matches) {
			return base64_decode($matches[1]);
		}, $text);
	return $text;
}

// Parse madrkdown text to html
function wh_parse($mdtext, $mdtype='') {
	if      ($mdtype=="basic") $MDown = new \cebe\markdown\Markdown();
	else if ($mdtype=="extra") $MDown = new \cebe\markdown\MarkdownExtra();
	else                       $MDown = new \cebe\markdown\GithubMarkdown();
	$mdtext = '</p><div class="wh_markdown"><div class="markdown-body">'.$MDown->parse($mdtext).'</div></div><p>';
	if (strpos($mdtext, '[spoiler') !== false) {
		$mdtext = wh_HideCode($mdtext);
		$mdtext = wh_MD_ParseSpoiler($mdtext);
		$mdtext = wh_UnhideCode($mdtext);
	}
	return wh_MD_ParseLinks($mdtext);
}

// Parse madrkdown code to html
function wh_MD_Parse($input) {
	if (is_array($input)) {
		$mdtype = 'github'; // default type
		if (preg_match('#\[markdown=(\w+)#', $input[0], $matchType))
			$mdtype = $matchType[1];
		$input = html_entity_decode($input[1], ENT_QUOTES);
		return wh_parse($input, $mdtype);
	}
	return preg_replace_callback('#\[markdown=?\w*]((?:`[^`]*`|[\s\S])+?)\[\/markdown]#s', 'wh_MD_Parse', $input);
}


// Make links with opening in new window
function wh_MD_ParseLinks($text) {
	if (preg_match_all('#<a[^>]+href=[^>]+>#s', $text, $matches)) {
		$links = $matches[0];
		foreach ($links as $link) {
			if (strpos('target=', $link)==false)
				$text = str_replace($link, str_replace('>', ' target="_blank">', $link), $text);
		}
	}
	return $text;
}


// Make spoilers bbcode tag use as fancy_spoiler
function wh_MD_ParseSpoiler($text) {
	global $forum_loader;
	$text = preg_replace_callback('#`[^`]*`|\[spoiler=?([^]]*)]((?:`[^`]*`|[\s\S])+?)\[\/spoiler]#s', function ($matches) {
			$hlink = ""; $hename=""; $header="";
			if ($matches[1]) {
				$header = $matches[1];
				$hedel  = array(',','!','?','#','@','%','&',';','*','^',':','(',')','+','-','/','\\','[',']','"','\'');
				$hename = str_replace(' ', '_', str_replace($hedel, '', $header));
				$hlink  = $header.'<a href="#'.$hename.'" name="'.$hename.'">&nbsp;&nbsp;</a>';
			}
			if ($matches[2])
				return '</p><div class="fancy_spoiler_switcher"><div class="fancy_spoiler_switcher_header"><strong>+</strong>&nbsp;'.$hlink.'</div><div name="'.$hename.'" class="fancy_spoiler"><p>'.$matches[2].'</p></div></div><p>';
			else
				return $matches[0];
		}, $text);
	$forum_loader->add_js('document.getElementsByName(window.location.hash.substring(1))[1].style.display = \'block\';', array('acync'=>true, 'type' => 'inline'));
	return $text;
}
	
// Hide code blocks
function wh_HideCode($text) {
	return preg_replace_callback('#(<code[\s\S]+?<\/code>)#s', function ($matches) {
			return '<hiddencode>'.base64_encode($matches[0]).'</hiddencode>';
		}, $text);
}

// Unhide code blocks
function wh_UnhideCode($text) {
	$text = preg_replace_callback('#<hiddencode>([\s\S]*?)<\/hiddencode>#s', function ($matches) {
			return base64_decode($matches[1]);
		}, $text);
	return $text;
}
