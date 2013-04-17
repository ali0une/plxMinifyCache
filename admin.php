<?php if(!defined('PLX_ROOT')) exit; 

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST) && isset($_POST['update'])) {
	$plxPlugin->plxMinifyCacheClean($_POST);
	header('Location: plugin.php?p=plxMinifyCache');
	exit;
}

?>

<h2><?php $plxPlugin->lang('L_TITLE') ?></h2>
<h3><?php $plxPlugin->lang('L_DESCRIPTION') ?></h3>
<br />
<p>Liste du cache</p>
<pre class="brush:bash"><?php $plxPlugin->plxMinifyCacheList(); ?></pre>
<form action="plugin.php?p=plxMinifyCache" method="post" id="clean_cache">
	<p class="center">
	<?php echo plxToken::getTokenPostMethod() ?>
	<input class="button update" type="submit" name="update" value="<?php $plxPlugin->lang('L_CLEAN_CACHE'); ?>" />
	</p>
</form>

