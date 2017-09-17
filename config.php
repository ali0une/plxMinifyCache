<?php
/**
 * Plugin plxMinifyCache
 *
 * @package	PLX
 * @version	1.5.1
 * @date	17/09/2017
 * @author	i M@N, Thomas I.
 **/
if(!defined('PLX_ROOT')) exit;

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	if (!empty($_POST['delay'])){
	$plxPlugin->setParam('delay', $_POST['delay'], 'cdata');
	$plxPlugin->setParam('exclude', implode(',',$_POST['exclude']).(!empty($_POST['more'])?','.$_POST['more']:''), 'string');
	$plxPlugin->setParam('less', $_POST['less'], 'string');
	$plxPlugin->setParam('minify', implode(',',$_POST['minify']), 'string');
	$plxPlugin->setParam('freeze', $_POST['freeze'], 'numeric');
	$plxPlugin->setParam('get', $_POST['get'], 'numeric');
	$plxPlugin->setParam('powa', $_POST['powa'], 'numeric');
} else {
	$plxPlugin->setParam('delay', '3600', 'cdata');
	$plxPlugin->setParam('exclude', 'article,post', 'string');
	$plxPlugin->setParam('less', '', 'string');
	$plxPlugin->setParam('minify', 'css,javascript', 'string');
	$plxPlugin->setParam('freeze', '0', 'numeric');
	$plxPlugin->setParam('get', '0', 'numeric');
	$plxPlugin->setParam('powa', '0', 'numeric');
}
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxMinifyCache');
	exit;
}
$exclude = $plxPlugin->getParam('exclude');
$more = explode(',',$exclude);
$more = str_replace(array('article','post','search'),'',$more);
$more = implode(',',$more);
$more = ltrim($more,',');
$minify = $plxPlugin->getParam('minify');
?>
<h2 class="hide"><?php $plxPlugin->lang('L_TITLE') ?></h2>
<form id="set" class="inline-form" action="" method="post">
	<p id="action" class="minifycache in-action-bar">
		<a href="plugin.php?p=plxMinifyCache" title="<?php echo L_VIEW.' '.$plxPlugin->getLang('L_CACHE_LIST') ?>"><img id="admin" class="icon_pmc" alt="admin" src="<?php echo PLX_PLUGINS ?>plxMinifyCache/img/admin.png" /></a> 
		<input type="submit" name="submit" onClick="getElementById('set').submit();" value="<?php $plxPlugin->lang('L_SAVE') ?>" /><br class="hide" />
	</p>
	<h4><sub><sup><?php $plxPlugin->lang('L_DESCRIPTION') ?></sup></sub></h4>
	<?php $plxPlugin->lang('L_DELAY') ?><strong>*</strong> : 
	&nbsp;
	<?php plxUtils::printInput('delay', plxUtils::strCheck($plxPlugin->getParam('delay')), 'text','2-5'); ?>
	<br />
	<label><?php $plxPlugin->lang('L_MINIFY') ?>&nbsp;:</label>
<?php
			$minified = explode(',', $minify);
			$selected = (is_array($minified) AND in_array('javascript', $minified)) ? ' checked="checked"' : '';
			echo '&nbsp;<input type="checkbox" id="javascript" name="minify[]"'.$selected.' value="javascript" /><label for="javascript">&nbsp;JavaScript,&nbsp;</label>';
			$selected = (is_array($minified) AND in_array('css', $minified)) ? ' checked="checked"' : '';
			echo '&nbsp;<input type="checkbox" id="css" name="minify[]"'.$selected.' value="css" /><label for="css">&nbsp;CSS</label>';
?>
	<br />
		<span class="field">
		<label><?php $plxPlugin->lang('L_EXCLUDE') ?>&nbsp;(<?php $plxPlugin->lang('L_CACHING') ?>)&nbsp;:</label>
<?php
			$nocache = explode(',', $exclude);
			$selected = (is_array($nocache) AND in_array('article', $nocache)) ? ' checked="checked"' : '';
			echo '&nbsp;<input type="checkbox" id="article" name="exclude[]"'.$selected.' value="article" /><label for="article">&nbsp;'.$plxPlugin->getLang('L_ARTICLE').', </label>';
			$selected = (is_array($nocache) AND in_array('search', $nocache)) ? ' checked="checked"' : '';
			echo '&nbsp;<input type="checkbox" id="search" name="exclude[]"'.$selected.' value="search" /><label for="search">&nbsp;'.$plxPlugin->getLang('L_SEARCH').', </label>';
			echo '&nbsp;<input type="checkbox" id="post" name="exclude[]" checked="checked" value="post" disabled="" /><label for="post">&nbsp;'.$plxPlugin->getLang('L_POST').'</label>';
?>
<br />
<strong><i><?php $plxPlugin->lang('L_DEFAULT') ?></i></strong>
	<hr />
	<label><b title="<?php $plxPlugin->lang('L_LESS') ?>"><?php $plxPlugin->lang('L_EXCLUDE') ?>&nbsp;(<?php $plxPlugin->lang('L_MINIFY') ?>)</b>&nbsp;:</label>
	<?php plxUtils::printInput('less', plxUtils::strCheck($plxPlugin->getParam('less')), 'text','64-255', false, '" placeholder="erreur,static,contact,bugyPlug,***,Pluxml_Modes_unminified&uncached" title="'.$plxPlugin->getLang('L_LESS_HELP')); ?>
	<br />
	<label><b title="<?php $plxPlugin->lang('L_MORE') ?>"><?php $plxPlugin->lang('L_EXCLUDE') ?>&nbsp;(<?php $plxPlugin->lang('L_CACHING') ?>)</b>&nbsp;:</label>
	<?php plxUtils::printInput('more', plxUtils::strCheck($more), 'text','64-255', false, '" placeholder="erreur,static,product,boutique,myPlugMode,***,Pluxml_Modes_uncached" title="'.$plxPlugin->getLang('L_MORE_HELP')); ?>
	<hr />
	<label><b title="<?php $plxPlugin->lang('L_GET_HELP') ?>"><?php $plxPlugin->lang('L_GET') ?></b>&nbsp;:</label>
<?php plxUtils::printSelect('get',array('1'=>L_YES,'0'=>L_NO),$plxPlugin->getParam('get')) ?>
	<br />
	<label><b title="<?php $plxPlugin->lang('L_POWA_HELP') ?>"><?php $plxPlugin->lang('L_POWA') ?></b>&nbsp;:</label>
<?php plxUtils::printSelect('powa',array('1'=>L_YES,'0'=>L_NO),$plxPlugin->getParam('powa')) ?>
	<hr />
	<label><b title="<?php $plxPlugin->lang('L_FREEZE_HELP') ?>"><?php $plxPlugin->lang('L_FREEZE') ?></b>&nbsp;:</label>
<?php plxUtils::printSelect('freeze',array('1'=>L_YES,'0'=>L_NO),$plxPlugin->getParam('freeze')) ?>
		</span>
	<?php echo plxToken::getTokenPostMethod() ?>
</form>
<script type="text/javascript" style="display:none">
	var a = document.querySelectorAll('.inline-form h4');a = a[0];
	var z = document.querySelectorAll('.action-bar');z = z[0];
	var t = z.querySelectorAll('h2');t = t[0];
	t.innerHTML = t.innerHTML + ' : ' + a.innerHTML;
	a.className = 'hide';
	var a = document.getElementById('action')
	a.className = '';/* remove css nojs helper */
	z.appendChild(a);
</script>