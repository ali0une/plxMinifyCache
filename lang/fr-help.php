<?php if(!defined('PLX_ROOT')) exit; ?>

<h2>Aide</h2>
<p>Fichier d&#039;aide du plugin plxMinifyCache</p>

<p>&nbsp;</p>
<h3>Installation</h3>
<p>Pensez &agrave; activer le plugin.<br/>
Editez le fichier index.php et modifiez :</p>
<pre>
# Hook Plugins
eval($plxMotor->plxPlugins->callHook(&#039;IndexEnd&#039;));
</pre>
<p>en :</p>
<pre>
# Hook Plugins
eval($plxMotor->plxPlugins->callHook(&#039;IndexEnd&#039;));

# Hook plxMinifyCache
if ($plxShow->callHook(&#039;IndexMinifyCache&#039;,$output) != &#039;&#039;) {
$output = $plxShow->callHook(&#039;IndexMinifyCache&#039;,$output);
}
# /Hook plxMinifyCache
</pre>
<p>&nbsp;</p>
<h3>Utilisation</h3>
<p>
	Le plugin minifie et met en cache le source.
</p>

<p>&nbsp;</p>
<h3>Configuration</h3>
<p>
	Dans la configuration du plugin (Paramètres > plugins > plxMinifyCache configuration), vous pouvez changer la durée (en secondes) du cache.
</p>
