<?php
/**
 * Plugin plxMinifyCache
 *
 * @package	PLX
 * @version	1.1
 * @date	17/04/2013
 * @author	i M@N
 **/
class plxMinifyCache extends plxPlugin {

	/**
	 * Constructeur de la classe plxMinifyCache
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	i M@N
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# droits pour accéder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		
		# Déclarations des hooks
		$this->addHook('IndexMinifyCache', 'IndexMinifyCacheOn');
	}

	public function OnActivate() {
		/* cache dir check */
		if (!is_dir(PLX_ROOT."cache/")) {
		mkdir(PLX_ROOT."cache/");
		}
		$plxMotor = plxMotor::getInstance();
		if (version_compare($plxMotor->version, "5.1.7", ">=")) {
			if (!file_exists(PLX_ROOT."data/configuration/plugins/plxMinifyCache.xml")) {
				if (!copy(PLX_PLUGINS."plxMinifyCache/parameters.xml", PLX_ROOT."data/configuration/plugins/plxMinifyCache.xml")) {
					return plxMsg::Error(L_SAVE_ERR.' '.PLX_PLUGINS."plxMinifyCache/parameters.xml");
				}
			}
		}
	}

	public function OnDeactivate() {
		/* clean cache dir */
		$cached = glob(PLX_ROOT."cache/*.html");
		foreach ($cached as $file) {
		unlink($file);
		}
		unset($cached);
	}

	/**
	 * Méthode qui ajoute l'insertion de la fonction plxMinifyCache dans l'index du site
	 *
	 * @return	stdio
	 * @author	i M@N
	 **/	

	public function IndexMinifyCacheOn($param) {

	# récuperation d'une instance de plxMotor
	$plxMotor = plxMotor::getInstance();
	$plxPlugin = $plxMotor->plxPlugins->getInstance('plxMinifyCache');

	/* start minify */
	$param = preg_replace("@ {2,}|\t+@is", "", $param);
	$param = preg_replace("@<br />@is", "<br>", $param);
	$param = preg_replace("@<hr />@is", "<hr>", $param);

	require_once(PLX_PLUGINS."plxMinifyCache/lib/HTML.php");
#	require_once(PLX_PLUGINS."plxMinifyCache/lib/CSS.php");
	require_once(PLX_PLUGINS."plxMinifyCache/lib/JSMin.php");
	require_once(PLX_PLUGINS."plxMinifyCache/lib/CommentPreserver.php");

	$param = Minify_HTML::minify($param, array(
#	'cssMinifier' => array('Minify_CSS', 'minify'),
	'jsMinifier' => array('JSMin', 'minify'),
	'jsCleanComments',
	'xhtml' => true
		)
	);

	/* one line */
	$param = preg_replace("@\n@is", " ", $param);

	/* if not preview mode */
	if ($_SERVER['QUERY_STRING'] != 'preview') {
		/* start cache */
		$delay = $plxPlugin->getParam('delay');
		$cache = PLX_ROOT.'cache/cache_'.md5($_SERVER['QUERY_STRING']).'.html';
		$expire = time() -$delay; // 3600 (1 hr)
		if(@is_file($cache) && filemtime($cache) > $expire) {
		$expire_offset = $delay; // set to a reasonable interval, say 3600 (1 hr)
		header('Expires: '.gmdate('D, d M Y H:i:s', time() + $expire_offset).' GMT');
		header('Cache-Control: private, must-revalidate, proxy-revalidate, post-check=10, pre-check=60, max-age='.$expire_offset.'');
		header('Pragma: no-cache');
		/* gzip */
		if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
		readfile($cache);
		echo '<!-- cached '.$_SERVER['QUERY_STRING'].' '.date("Y-m-d H:i:s",filemtime($cache)).' -->';
		/* end gzip */
#		ob_end_flush();
		exit;
		}
		else {
		file_put_contents($cache, $param);
		}
		/* end cache */
	}
	return $param.'<!-- minified '.date("Y-m-d h:i:s").' -->';
	}
}
?>
