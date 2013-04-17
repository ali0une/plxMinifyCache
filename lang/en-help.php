<?php if(!defined('PLX_ROOT')) exit; ?>

<h2>Help</h2>
<p>plxMinifyCache plugin help file</p>

<p>&nbsp;</p>
<h3>Installation</h3>
<p>Activate plugin.<br/>
Edit file index.php modify :</p>
<pre>
# Hook Plugins
eval($plxMotor->plxPlugins->callHook(&#039;IndexEnd&#039;));
</pre>
<p>in :</p>
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
<h3>Usage</h3>
<p>
	Plugin minifie and cache source.
</p>

<p>&nbsp;</p>
<h3>Configuration</h3>
<p>
	In the configuration part of the plugin (Paramètres > plugins > plxMinifyCache configuration), you can change cache duration (in seconds).
</p>
