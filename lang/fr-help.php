<?php if(!defined('PLX_ROOT')) exit; ?>
<h2 class="hide">Aide</h2>
<p class="in-action-bar">Fichier d&#039;aide du plugin plxMinifyCache</p>
<h3>installer</h3>
<p>Pensez à activer le plugin.</p>
<p>Si l'interface est bizarre, (images disproportionnées par exemple) :<br />
La feuille de style n'est peut-être pas prise en compte ou le cache de votre navigateur est a rechargé...<br />
Tentez de Supprimer le code Contenu fichier CSS administrateur du plugin
(<a href="parametres_plugincss.php?p=plxMinifyCache">Paramètres &gt; plugins &gt; plxMinifyCache code css</a>) et sauvegarder 2 FOIS  de suite ;)
Pensez ensuite a recharger le cache de votre navigateur (Ctrl + F5)</p>
<h3>Utiliser</h3>
<p>Le plugin minifie et met en cache le source.</p>
<h3>Configurer</h3>
<p>Dans la configuration du plugin
(<a href="parametres_plugin.php?p=plxMinifyCache">Paramètres &gt; plugins &gt; plxMinifyCache configuration</a>),
vous pouvez changer la durée (en secondes) du cache,
exclure les pages article et recherche de la mise en cache et minifier les inline scripts et styles.
Les pages issues de POST ne sont pas mises en cache.<br />
Il est possible d’exclure d'autres mode du moteur de PluXml en plus de ceux par défaut grâce a des listes.<br />
D'écrire le mode de PluXml dans les sources et dans la console. *<br />
D'écrire les chronos dans les codes sources et dans la console. *<br />
De Figer le cache pour geler les mises a jour de fichier présent dans le cache, cela rend le site statique pour les modes autorisés.
<br /><br />
<i>*Utiliser ce(s) paramètre(s) a des fins de développement ou de test, non en production.<br />
(un peu plus lent, str_replace() intercale avant la fin de body les info et include() remplace readfile() pour le chrono dynamique)</i>
</p>
<h3>Gérer</h3>
<p>Dans l'administration du plugin (plxMinifyCache), vous pouvez vider le cache, supprimer des fichiers et créer un zip de sauvegarde du cache.
<br />
/!\ Si le zip de sauvegarde est présent dans le serveur, cela enclenche son téléchargement et la boite demandant sa suppression 3 seconde après, et ceci a chaque fois que l'on arrive dans l'admin du plugin, cliquer sur l’icône rafraîchir du plugin (ou supprimer le zip) stoppe cet effet.
</p>
<!-- currieux l'aide en français s'affiche, alors que dans le profil lui est en anglais, par contre le site est en Français -->