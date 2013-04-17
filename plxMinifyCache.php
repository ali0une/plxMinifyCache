<?php
/**
 * Plugin plxMinifyCache
 *
 * @package	PLX
 * @version	1.2
 * @date	18/04/2013
 * @author	i M@N, Stephane F.
 **/
class plxMinifyCache extends plxPlugin {

	/**
	 * Constructeur de la classe plxMinifyCache
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @authors	i M@N, Stephane F.
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accéder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		# Autorisation d'accès à l'administration du plugin
		$this->setAdminProfil(PROFIL_ADMIN);

		# Déclarations des hooks
		if($_SERVER['QUERY_STRING'] != 'preview') # pas de gestion du cache si on est en mode preview
			$this->addHook('IndexEnd', 'IndexEnd');
	}

	/**
	 * Méthode appelée dans l'administration pour calculer la taille du cache
	 *
	 * @return	human readable size
	 * @author	Rommel Santor : http://rommelsantor.com/
	 **/
	public function size_readable($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}

	/**
	 * Méthode appelée dans l'administration pour lister le cache
	 *
	 * @return	null
	 * @author	i M@N, Stephane F.
	 **/
	public function plxMinifyCacheList() {
		$cache_size = 0;
		/* list cache dir */
		if (extension_loaded('glob')) {
		$cached = glob(PLX_ROOT."cache/*.html");
		foreach ($cached as $file) {
		$cache_size += filesize(PLX_ROOT."cache/".$file);
		echo(basename($file)).'<br>';
		}
		echo 'total : '.$this->size_readable($cache_size, $decimals = 2);
		unset($cached);
		}
		else {
			if($cached = opendir(PLX_ROOT."cache/")) {
				while(($file = readdir($cached))!== false) {
					if( $file == '.' || $file == '..' )
					continue;
					if(strtolower(strrchr($file,'.')==".html")) {
						$cache_size += filesize(PLX_ROOT."cache/".$file);
						echo(basename(PLX_ROOT."cache/".$file)).'<br>';
					}
				}
				echo 'total : '.$this->size_readable($cache_size, $decimals = 2);
				closedir($cached);
			}
		}
	}

	/**
	 * Méthode appelée dans l'administration pour nettoyer le cache
	 *
	 * @return	null
	 * @author	i M@N, Stephane F.
	 **/
	public function plxMinifyCacheClean() {
		/* clean cache dir */
		if (extension_loaded('glob')) {
		$cached = glob(PLX_ROOT."cache/*.html");
		foreach ($cached as $file) {
		unlink($file);
		}
		unset($cached);
		}
		else {
			if($cached = opendir(PLX_ROOT."cache/")) {
				while(($file = readdir($cached))!== false) {
					if( $file == '.' || $file == '..' )
					continue;
					if(strtolower(strrchr($file,'.')==".html")) {
						unlink(PLX_ROOT."cache/".$file);
					}
				}
				closedir($cached);
			}
		}
		return plxMsg::Info($this->getLang('L_CACHE_CLEANED'));
	}

	/**
	 * Méthode appelée quand on active le plugin : pour créer le répertoire de cache
	 *
	 * @return	null
	 * @author	i M@N
	 **/
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

	/**
	 * Méthode appelée quand on désactive le plugin : pour nettoyer le cache
	 *
	 * @return	null
	 * @author	i M@N, Stephane F.
	 **/
	public function OnDeactivate() {
		/* clean cache dir */
		if (extension_loaded('glob')) {
		$cached = glob(PLX_ROOT."cache/*.html");
		foreach ($cached as $file) {
		unlink($file);
		}
		unset($cached);
		}
		else {
			if($cached = opendir(PLX_ROOT."cache/")) {
				while(($file = readdir($cached))!== false) {
					if( $file == '.' || $file == '..' )
					continue;
					if(strtolower(strrchr($file,'.')==".html")) {
						unlink(PLX_ROOT."cache/".$file);
					}
				}
				closedir($cached);
			}
		}
	}

	/**
	 * Méthode qui gère le cache de la sortie écran
	 *
	 * @return	stdio
	 * @author	i M@N, Stephane F.
	 **/
	public function IndexEnd() {

		$string = '
		include_once(PLX_PLUGINS."plxMinifyCache/lib/HTML.php");
#		include_once(PLX_PLUGINS."plxMinifyCache/lib/CSS.php");
		include_once(PLX_PLUGINS."plxMinifyCache/lib/JSMin.php");
		include_once(PLX_PLUGINS."plxMinifyCache/lib/CommentPreserver.php");

		$output = preg_replace("@ {2,}|\t+@is", "", $output);
		$output = preg_replace("@<br />@is", "<br>", $output);
		$output = preg_replace("@<hr />@is", "<hr>", $output);

		$output = Minify_HTML::minify($output, array(
#				"cssMinifier" => array("Minify_CSS", "minify"),
				"jsMinifier" => array("JSMin", "minify"),
				"jsCleanComments",
				"xhtml" => true
			)
		);

		$output = preg_replace("@\n@is", " ", $output); # suppression de retour chariot

		$delay = "'.$this->getParam("delay").'";
		$cache = PLX_ROOT."cache/cache_".md5($_SERVER["QUERY_STRING"]).".html";
		$expire = time() - $delay; // 3600 (1 hr)
		if(@is_file($cache) AND filemtime($cache) > $expire) {
			$expire_offset = $delay;
			header("Expires: ".gmdate("D, d M Y H:i:s", time() + $expire_offset)." GMT");
			header("Cache-Control: private, must-revalidate, proxy-revalidate, post-check=10, pre-check=60, max-age=".$expire_offset);
			header("Pragma: no-cache");
			$gzip = substr_count($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") ? "ob_gzhandler" : "";
			ob_start($gzip);
			readfile($cache);
			echo "<!-- cached ".$_SERVER["QUERY_STRING"]." ".date("Y-m-d H:i:s",filemtime($cache))." -->";
#			ob_end_flush();
			exit;
		} else {
			$output .= "<!-- minified ".date("Y-m-d h:i:s")." -->";
			file_put_contents($cache, $output);
		}
		';
		echo '<?php '.$string.' ?>';
	}
}
?>
