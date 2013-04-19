<?php
/**
 * Plugin plxMinifyCache
 *
 * @package	PLX
 * @version	1.4
 * @date	20/04/2013
 * @author	i M@N
 **/
	if(!defined('PLX_ROOT')) exit; 
	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	if (!empty($_POST['delay']) ) {
	$plxPlugin->setParam('delay', $_POST['delay'], 'cdata');
	$plxPlugin->setParam('exclude', implode(',',$_POST['exclude']), 'string');
	$plxPlugin->setParam('minify', implode(',',$_POST['minify']), 'string');
} else {
	$plxPlugin->setParam('delay', '3600', 'cdata');
	$plxPlugin->setParam('exclude', 'article', 'string');
	$plxPlugin->setParam('minify', 'css,javascript', 'string');
}
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxMinifyCache');
	exit;
}
$exclude = $plxPlugin->getParam('exclude')=='' ? '' : $plxPlugin->getParam('exclude');
$minify = $plxPlugin->getParam('minify')=='' ? '' : $plxPlugin->getParam('minify');
?>

<h2><?php $plxPlugin->lang('L_TITLE') ?></h2>
<p><?php $plxPlugin->lang('L_DESCRIPTION') ?></p>

<form action="parametres_plugin.php?p=plxMinifyCache" method="post">
	<?php $plxPlugin->lang('L_DELAY') ?> : 
	&nbsp;
	<?php plxUtils::printInput('delay', plxUtils::strCheck($plxPlugin->getParam('delay')), 'text','2-5'); ?>
	<br />
		<span class="field">
		<label><?php $plxPlugin->lang('L_EXCLUDE') ?>&nbsp;:</label>
		
		<?php
			$nocache = explode(',', $exclude);
			$selected = (is_array($nocache) AND in_array('article', $nocache)) ? ' checked="checked"' : '';
			echo '&nbsp;<input type="checkbox" id="article" name="exclude[]"'.$selected.' value="article" /><label for="article">&nbsp;'.$plxPlugin->lang(L_ARTICLE).'</label>';
			$selected = (is_array($nocache) AND in_array('search', $nocache)) ? ' checked="checked"' : '';
			echo '&nbsp;<input type="checkbox" id="search" name="exclude[]"'.$selected.' value="search" /><label for="search">&nbsp;'.$plxPlugin->lang(L_SEARCH).'</label>';
			$selected = (is_array($nocache) AND in_array('post', $nocache)) ? ' checked="checked"' : '';
			echo '&nbsp;<input type="checkbox" id="post" name="exclude[]"'.$selected.' value="post" /><label for="post">&nbsp;'.$plxPlugin->lang(L_POST).'</label>';
		?>
	<br />
		<label><?php $plxPlugin->lang('L_MINIFY') ?>&nbsp;:</label>
		
		<?php
			$minified = explode(',', $minify);
			$selected = (is_array($minified) AND in_array('javascript', $minified)) ? ' checked="checked"' : '';
			echo '<input type="checkbox" id="javascript" name="minify[]"'.$selected.' value="javascript" /><label for="javascript">&nbsp;JavaScript</label>';
			$selected = (is_array($minified) AND in_array('css', $minified)) ? ' checked="checked"' : '';
			echo '&nbsp;<input type="checkbox" id="css" name="minify[]"'.$selected.' value="css" /><label for="css">&nbsp;CSS</label>';
		?>
		</span>
	<br />
	<strong><?php $plxPlugin->lang('L_DEFAULT') ?></strong>
	<br />
	<?php echo plxToken::getTokenPostMethod() ?>
	<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
</form>
