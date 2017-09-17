<?php if(!defined('PLX_ROOT')) exit; ?>
<h2 class="hide">Help</h2>
<p class="in-action-bar">plxMinifyCache plugin help file</p>
<h3>Install</h3>
<p>Activate plugin.</p>
<p>If poor interface (big images) :<br />Delete the le code inside field Css file content administrator of plugin
(<a href="parametres_plugincss.php?p=plxMinifyCache"&gt;Parameters &gt; plugins &gt; plxMinifyCache css code</a>) and save TWICE ;)
Need to reload browser cache after that ;)</p>
<h3>Usage</h3>
<p>Plugin minifie and cache source.</p>
<h3>Settings</h3>
<p>In the configuration part of the plugin
(<a href="parametres_plugin.php?p=plxMinifyCache">Parameters &gt; plugins &gt; plxMinifyCache configuration</a>),
you can change cache duration (in seconds),
exclude pages article and search from being cached and minify inline scripts and styles.
Pages issued from POST are not cached.<br />
With list, it's possible to exclude other mode of PluXml motor in addition to those by default.<br />
Write PluXml mode in sources and in console. *<br />
Write chronos in source code and in console. *<br />
Freeze cache lock the update of file cached, The site become static for authorized modes.
<br /><br />
<i>* Use this parameters for development or test, not in production.<br />
(little more slowly, str_replace() insert info before end body tag and for chrono dynamic include() replace readfile())</i>
</p>
<h3>Administer</h3>
<p>in the administration part of the plugin (plxMinifyCache), you can clean cache and files one by one, create zip backup of entire folder.
<br />
/!\ if zip is present on server, display download & confirm box for delete zip appear after 3s all the time, click on refresh icon (of plugin) to stop this effect.
</p>
<!-- currieux l'aide en français s'affiche, alors que dans le profil lui est en anglais, par contre le site est en Français, si le site est en anglis, c'est bon -->