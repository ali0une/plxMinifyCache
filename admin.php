<?php if(!defined('PLX_ROOT')) exit;
plxToken::validateFormToken($_POST);# Control du token du formulaire
$c=$fresh=$freshb=$imgzip=$delzip=$e='';# init some var
$hf=(count(scandir(PLX_CACHE)) == 2);//is dir empty #have file
$zipname = '.plxMinifyCache_'.@$_SERVER['HTTP_HOST'].'_backup.zip';
if(!empty($_POST)){//var_dump($_POST);exit;
	if (!empty($_POST['clean']))
		if (isset($_POST['delzip']) && $_POST['delzip']=='o')# nojs fallback
			$plxPlugin->clean($zipname);
		elseif ($_POST['clean']=='zip' OR isset($_POST['zip']))#2nf 4 nojs fallback
			$plxPlugin->clean(false,true);# zip all cached files
		elseif ($_POST['clean']=='all')
			$plxPlugin->clean();
		else
			$plxPlugin->clean($_POST['clean']);#one file
	header('Location: plugin.php?p=plxMinifyCache'.(isset($_GET['e'])?'&e=stop':''));# never &amp; in header loc
	exit;
}
if(file_exists(PLX_CACHE.$zipname)){#goto zip 4 download, &confirm del zip (after 3s) & add button 4 noJs fallBack 'and clear after) !!! if zip is present on server, display download & dialog boxes all the time, sorry about that, click on refresh to stop this effect
 $imgzip = '<a href="'.PLX_CACHE.$zipname.'" title="'.$plxPlugin->getLang('L_ZIP_SERVER').' ('.date('Y-m-d H:i',filemtime(PLX_CACHE.$zipname)).')">
 <img id="archive" class="icon_pmc" alt="Backup present" src="'.PLX_PLUGINS.'plxMinifyCache/img/archive.png" />
 </a> ';
 $freshb = '
 <button class="delete orange" type="submit" name="delzip" value="o" onClick="clean(\''.$zipname.'\');return false;" title="'.$plxPlugin->getLang('L_CACHE_ZIPDEL').'">
  <img id="zipdel" class="icon_pmc" alt="Del zip" src="'.PLX_PLUGINS.'plxMinifyCache/img/zipdel.png" /> '.L_DELETE.' zip
 </button>';#2 delzip NoJs & js
 if(!isset($_GET['e'])){
  $e='?p=plxMinifyCache&amp;e=stop';
  $fresh = '<meta HTTP-EQUIV="REFRESH" content="0; url='.PLX_CACHE.$zipname.'">';#go to download
  $delzip='/* After download */
  function cleanZip(){if (confirm("'.$plxPlugin->getLang('L_CACHE_ZIPDEL').'")) clean("'.$zipname.'");}
  window.setTimeout(cleanZip, 3000);';
 }
}
?>
<p class="sml-hide med-show"><!-- plx5.5 compensation --></p>
<h2 class="hide"><?php $plxPlugin->lang('L_TITLE') ?></h2>
<form id="clean_cache" class="inline-form" action="plugin.php?p=plxMinifyCache" method="post">
	<p id="action" class="minifycache in-action-bar"><span id="jeckyl">&nbsp;</span>
		<a href="parametres_plugin.php?p=plxMinifyCache" title="<?php echo L_PLUGINS_CONFIG ?>"><img id="config" class="icon_pmc" alt="config" src="<?php echo PLX_PLUGINS ?>plxMinifyCache/img/settings.png" /></a> 
		<script type="text/javascript" style="display:none">function clean(file){if(file)document.getElementById('clean').value=file;document.getElementById('clean_cache').submit();}</script>
		<a href="<?php echo $e ?>" title="<?php $plxPlugin->lang('L_REFRESH') ?>"><img id="refresh" class="icon_pmc" alt="refresh" src="<?php echo PLX_PLUGINS ?>plxMinifyCache/img/reload.png" /></a> 
		<button class="update green<?php echo($hf?' hide':'')?>" type="submit" name="update" onClick="clean();return false;"><img id="clear" class="icon_pmc" src="<?php echo PLX_PLUGINS ?>plxMinifyCache/img/trash_can_reload.png"> <?php $plxPlugin->lang('L_CLEAN_CACHE'); ?></button>
		<button class="download blue<?php echo($hf?' hide':'')?>" type="submit" name="zip" value="o" onClick="clean('zip');return false;"><img id="zip" class="icon_pmc" src="<?php echo PLX_PLUGINS ?>plxMinifyCache/img/zip.png"> <?php $plxPlugin->lang('L_CACHE_ZIP'); ?></button>
<?php echo $freshb ?>
	</p><!-- #action moved by js in action bar. After this, button not in form tag, noscript fallback with clean input preset with all -->
	<?php echo plxToken::getTokenPostMethod() ?>
	<input type="hidden" name="clean" id="clean" value="all" />
	<h4><sub><sup><?php $plxPlugin->lang('L_DESCRIPTION') ?></sup></sub></h4>
</form>
<?php echo $imgzip; $plxPlugin->plxMinifyCacheList(); ?>
<script type="text/javascript" style="display:none">
<?php echo $delzip ?>
	var a = document.querySelectorAll('a.hide');
	for (i=0; i<a.length; i++)
		a[i].className="";/* unhide js clean file link */
	var a = document.querySelectorAll('.inline-form h4');a = a[0];
	var z = document.querySelectorAll('.action-bar');z = z[0];
	var t = z.querySelectorAll('h2');t = t[0];
	t.innerHTML = t.innerHTML + ' : ' + a.innerHTML;
	a.className = 'hide';
	var a = document.getElementById('action')
	a.className = '';/* remove css nojs helper */
	a.firstChild.className = 'show';/* remove css nojs helper */
	z.appendChild(a);
</script>
<?php echo $fresh;