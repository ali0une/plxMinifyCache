<?php
/**
 * Plugin plxMinifyCache
 *
 * @package	PLX
 * @version	1.0
 * @date	15/04/2013
 * @author	i M@N
 **/
	if(!defined('PLX_ROOT')) exit; 
	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	if (!empty($_POST['delay']) ) {
	$plxPlugin->setParam('delay', $_POST['delay'], 'cdata');
} else {
	$plxPlugin->setParam('delay', '3600', 'cdata');
}
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxMinifyCache');
	exit;
}
?>

<h2><?php $plxPlugin->lang('L_TITLE') ?></h2>
<p><?php $plxPlugin->lang('L_DESCRIPTION') ?></p>

<form action="parametres_plugin.php?p=plxMinifyCache" method="post">
	<?php $plxPlugin->lang('L_DELAY') ?> : 
	<br />
	<?php plxUtils::printInput('delay', plxUtils::strCheck($plxPlugin->getParam('delay')), 'text','2-5'); ?>
	<br />
	<?php echo plxToken::getTokenPostMethod() ?>
	<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
</form>
