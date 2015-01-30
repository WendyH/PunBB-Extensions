<?php

if (!defined('FORUM')) die();

require_once FORUM_ROOT.'lang/'.$forum_user['language'].'/login.php';

$lUser = isset($_POST['req_username']) ? forum_htmlencode($_POST['req_username']) : '';
$lPass = isset($_POST['req_password']) ? forum_htmlencode($_POST['req_password']) : '';

$searchFrm = '<div id="whfrmsearch" class="whtopform"><form method="get" accept-charset="utf-8" action="'.forum_link($forum_url['search']).'">
<div class="hidden">
	<input type="hidden" name="action" value="search">
</div>
<input id="qsearch" type="text" name="keywords" size="20" maxlength="100" required="">
<input type="submit" name="search" value="">
</form></div>';
$loginFrm = '<div id="whfrmlogin" class="whtopform"><form method="post" accept-charset="utf-8" action="'.forum_link($forum_url['login']).'">
<div class="hidden">
	<input type="hidden" name="form_sent" value="1" />
	<input type="hidden" name="redirect_url" value="'.forum_htmlencode($_SERVER['REQUEST_URI']).'" />
	<input type="hidden" name="csrf_token" value="'.generate_form_token(forum_link($forum_url['login'])).'" />
</div>
<label for="fld1">'.$lang_login['Username'].'</label>
<input class="qlogin" type="text" id="fld1" name="req_username" value="'.$lUser.'" size="10" maxlength="25" required spellcheck="false" />
<label for="fld2">'.$lang_login['Password'].'</label>
<input class="qlogin" type="password" id="fld2" name="req_password" value="'.$lPass.'" size="10" required />
<input type="submit" class="fld-input2" name="login" value="'.$lang_login['Login'].'" />
</form></div>';

$showwhLogin = ($forum_user['is_guest'] and !preg_match('/login|register/', $_SERVER['REQUEST_URI']));

if (!$showwhLogin) {
	$searchFrm = str_replace('id="whfrmsearch"', 'id="whfrmsearch" style="left: 50%"', $searchFrm);
	$searchFrm = str_replace('<form', '<form style="margin-left: -50%"', $searchFrm);
}

$wh_topline = '<div id="whtopline">';
$wh_topline.= $searchFrm;
$wh_topline.= $showwhLogin ? $loginFrm : '';
$wh_topline.= '</div>';

$gen_elements['<!-- wh_topline -->' ] = $wh_topline;
