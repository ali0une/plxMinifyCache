<?php #@version 1.5.0 * @date 16/09/2017 Plugin plxMinifyCache * @package PLX * @author i M@N, Stephane F., Thomas I.
if (!defined('PLX_ROOT')) exit;
if (!defined('PLX_CACHE'))define('PLX_CACHE',PLX_ROOT.".cache/");
class plxMinifyCache extends plxPlugin{
	public function __construct($default_lang){
		parent::__construct($default_lang);
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->setAdminProfil(PROFIL_ADMIN);
		$this->addHook('IndexEnd', 'IndexEnd');
	}
	public function size_readable($bytes, $decimals = 2){# Méthode appelée dans l'administration pour calculer la taille du cache * @return	human readable size * @author	Rommel Santor : http://rommelsantor.com/
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
	public function get_info($file,$type='title'){
		if($type=='title')
			return preg_match('~<title>(.*?)</title>~i', file_get_contents(PLX_CACHE.$file), $matches) ? $matches[1] : '';
		if($type=='url')
			return preg_match('~<!--[ cached](.*?) -->$~i', file_get_contents(PLX_CACHE.$file), $matches) ? $matches[1] : '';
	}
	public function get_time($file){
		return filemtime(PLX_CACHE.$file);
	}
	public function cChrono(){# retourne le temps calcul
		return round(getMicrotime()-PLX_MICROTIME,3).'s';
	}
	public function plxMinifyCacheList(){# list cache dir (admin)
		function real($i,$a){$a=explode(' ',$a);return $a[$i];}# found url & more in last comment in cached source
		$cache_size = 0;
		$filesCache = array();
		if (extension_loaded('glob')){
			$cached = glob(PLX_CACHE."*.php");
			foreach ($cached as $file){
				$cache_size += filesize(PLX_CACHE.$file);
				$filesCache [$this->get_time($file)] = array((basename($file)), $this->get_info($file), $this->get_info($file,'url'));
			}
			unset($cached);
		}
		else{
			if($cached = opendir(PLX_CACHE)){
				while(($file = readdir($cached))!== false){
					if( $file == '.' || $file == '..' )
						continue;
					if(strtolower(strrchr($file,'.')==".php")){
						$cache_size += filesize(PLX_CACHE.$file);
						$filesCache [$this->get_time($file)] = array((basename($file)), $this->get_info($file), $this->get_info($file,'url'));
					}
				}
				closedir($cached);
			}
		}
		krsort($filesCache);
		$expire = ($this->getParam("freeze")?0:time() - $this->getParam("delay"));
		echo '<img id="mc_mode" class="icon_pmc" src="'.PLX_PLUGINS.'plxMinifyCache/img/'.($this->getParam("freeze")?'':'un').'lock.png" title="Mode : '.($this->getParam("freeze")?'Frozen':'Normal').'" alt="Freeze Mode" />&nbsp;';
		echo $this->getLang('L_CACHE_LIST').' ('.count($filesCache).') : '.date('Y-m-d H:i:s').' - '.$this->getLang('L_TOT').' : '.$this->size_readable($cache_size, $decimals = 2).'<hr /><pre id="CacheList" class="brush_bash">';
		foreach($filesCache as $ts => $name)#findicons.com free
			echo '<a class="hide" title="'.L_DELETE.' '.$name[0].'" href="javascript:clean(\''.$name[0].'\');"><img class="icon_pmc del_file" src="'.PLX_PLUGINS.'plxMinifyCache/img/del.png" title="'.L_DELETE.'" alt="del" /></a><b style="color:'.($ts < $expire?'red" title="expired">':'green">').' <a title="'.$name[0].PHP_EOL.$name[2].'" target="_blank" style="color:unset;" href="'.PLX_ROOT.'cache/'.$name[0].'">'.date('Y-m-d H:i:s',$ts).'<i class="mc-sml-hide"> : '.$name[0].'</i></a></b> : <a title="'.real(2,$name[2]).PHP_EOL.real(1,$name[2]).'" target="_blank" href="'.PLX_ROOT.real(1,$name[2]).'">'.$name[1].'</a><br />';
		echo '<br /></pre>';
	}
	public function clean($file=false,$zip=false){# Méthode appelée dans l'administration pour nettoyer le cache et créer le zip de sauvegarde * @return	null * @author	i M@N, Stephane F. Thomas I. #clean cache dir or one file, or zip all cached pages
		if($file){
			$file = PLX_CACHE.$file;
			unlink($file);
			return plxMsg::Info($this->getLang('L_FILE_CLEANED').'&nbsp;: '.$file);
		}
		if($zip){
			include(PLX_PLUGINS.'plxMinifyCache/lib/ZipHelper.php');# return text if error
			$rootPath = realpath(PLX_CACHE);
			$zipname = PLX_CACHE.'.plxMinifyCache_'.@$_SERVER['HTTP_HOST'].'_backup.zip';#lost $zip->filename after close() :/
			$zip = new ZipArchive();# Initialize archive object
			if(!$zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE))
				return plxMsg::Info($this->getLang('L_CACHE_ZIP_PB').$this->getLang('L_CACHE_ZIP_PC').' : '.zipStatusString($zip->status));
			$zip->setArchiveComment('plxMinifyCache backup : '.date('Y-m-d H:i'));
		}
		if(extension_loaded('glob')){
			if($zip){
				$zip->addGlob(PLX_CACHE."*.php");
			}else{
				$cached = glob(PLX_CACHE."*.php");
				foreach ($cached as $file) {
					unlink($file);
				}
				unset($cached);
			}
		}
		else{
			if($cached = opendir(PLX_CACHE)){
				while(($file = readdir($cached))!== false){
					if( $file == '.' || $file == '..' )
					continue;
					if(strtolower(strrchr($file,'.')==".php")){
						if($zip){
							$filePath = realpath(PLX_CACHE.$file);#Get real ...
							$relativePath = substr($filePath, strlen($rootPath) + 1);# ... and relative path for current file
							$zip->addFile($filePath, $relativePath);
						}else
							unlink(PLX_CACHE.$file);
					}
				}
				closedir($cached);
			}
		}
		if($zip){
			$zip->close();#Zip archive will be created only after closing object && display real zip status
			if($zip->status != ZIPARCHIVE::ER_OK)
				return plxMsg::Info($this->getLang('L_CACHE_ZIP_PB').$this->getLang('L_CACHE_ZIP_PW').' : '.zipStatusString($zip->status));
			return plxMsg::Info($this->getLang('L_CACHE_ZIPPED'));
		}
		else
			return plxMsg::Info($this->getLang('L_CACHE_CLEANED'));
	}
	public function OnActivate(){# Méthode appelée quand on active le plugin : pour créer le répertoire de cache * @author	i M@N Thomas I.
		if (!is_dir(PLX_CACHE))#cache dir check
			mkdir(PLX_CACHE);
		$plxMotor = plxMotor::getInstance();#if CssNoCashe activated say: "Erreur : cssNoCache est chargé 2 fois". See with an exit after get instance and on Activate plxMinifyCache
		if (isset($plxMotor->aConf['version']) OR (isset($plxMotor->version) && version_compare($plxMotor->version, "5.1.7", ">=")))#$plxMotor->version removed in 5.5
			if (!file_exists(PLX_ROOT."data/configuration/plugins/plxMinifyCache.xml"))
				if (!copy(PLX_PLUGINS."plxMinifyCache/parameters.xml", PLX_ROOT."data/configuration/plugins/plxMinifyCache.xml"))
					return plxMsg::Error(L_SAVE_ERR.' '.PLX_PLUGINS."plxMinifyCache/parameters.xml");
	}
	public function OnDeactivate(){#clean and remove cache dir
		$plxMotor = plxMotor::getInstance();
		if (!$plxMotor->plxPlugins->deleteDir(PLX_CACHE)){
			if (extension_loaded('glob')){
				$cached = glob(PLX_CACHE."*");#*.php
				foreach ($cached as $file)
					unlink($file);
				unset($cached);
			}
			else{
				if($cached = opendir(PLX_CACHE)){
					while(($file = readdir($cached))!== false){
						if( $file == '.' || $file == '..' )
							continue;
						#if(strtolower(strrchr($file,'.')==".php")){
							unlink(PLX_CACHE.$file);
						#}
					}
					closedir($cached);
				}
			}
			rmdir(PLX_CACHE);
		}
	}
	public function IndexEnd(){# Méthode qui gère le cache de la sortie écran * @return	stdio * @author	i M@N, Stephane F., Thomas I. # Cached Minimzd or less... trankil /!\ #bugOrNot, if have 2 space in attribute tag, remove spaces & are unvalid code, be carefull & valid html before... or  maybe update library to solve problem
	echo '<?php '; ?>
	$exclude = explode(",","<?php echo $this->getParam("exclude") ?>");
	$less = explode(",","<?php echo $this->getParam("less") ?>");
	$www = (<?php echo $this->getParam("powa") ?>)?'<?php $this->lang('L_POWERED_BY') ?>':0;
	$lock = <?php echo $this->getParam("freeze") ?>;
	if(!in_array($plxMotor->mode,$less)){
		$getin = <?php echo $this->getParam("get") ?>;
		$delay = "<?php echo $this->getParam("delay") ?>";
		$cache = PLX_CACHE."cache_".md5($_SERVER["QUERY_STRING"]).".php";
		$expire = time() - $delay;# 3600 (1 hr)
		if(file_exists($cache) AND $expire > filemtime($cache))
			if(!$lock)
				unlink($cache);
		if(!file_exists($cache)){
			$minify = explode(",","<?php echo $this->getParam("minify") ?>");
			include_once(PLX_PLUGINS."plxMinifyCache/lib/HTML.php");
			$output = preg_replace("@ {2,}|\t+@is", "", $output);#bugOrNot
			$output = preg_replace("@<br />@is", "<br>", $output);
			$output = preg_replace("@<hr />@is", "<hr>", $output);
			$options = array("xhtml" => true);# set Minify_HTML::minify options
			if(in_array("css",$minify)) {
				include_once(PLX_PLUGINS."plxMinifyCache/lib/CSS.php");
				include_once(PLX_PLUGINS."plxMinifyCache/lib/Compressor.php");
				$options = array("compress" => true, "preserveComments" => false);
			}
			if(in_array("javascript",$minify)) {
				include_once(PLX_PLUGINS."plxMinifyCache/lib/JSMin.php");
				include_once(PLX_PLUGINS."plxMinifyCache/lib/CommentPreserver.php");
				$options["jsMinifier"][0] = "JSMin";
				$options["jsMinifier"][1] = "minify";
				$options[0] = "jsCleanComments";
			}
			$output = Minify_HTML::minify($output, $options);
			if(in_array("css",$minify)){# set cssMinifier::minify options
				$output = Minify_CSS::minify($output, $options);
			}
			$output = preg_replace("@\n@is", " ", $output);# suppression de retour chariot
		}
		else if(@is_file($cache) AND (filemtime($cache) > $expire OR $lock)){
			$expire_offset = $delay;
			header("Expires: ".gmdate("D, d M Y H:i:s", time() + $expire_offset)." GMT");
			header("Cache-Control: private, must-revalidate, proxy-revalidate, post-check=10, pre-check=60, max-age=".$expire_offset);
			header("Pragma: no-cache");
			$gzip = substr_count($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") ? "ob_gzhandler" : "";
			ob_start($gzip);
			if($getin)
				include($cache);
			else
				readfile($cache);
			echo $www?'<!-- read '.$www.'<?php echo $this->cChrono() ?> -->':'';
			exit;
		}
		if($getin)
			$output = str_replace("</body>","<script type=\"text/javascript\">console.warn('#Mode: ".$plxMotor->mode." #chronos:<?php echo ' cache <?php'; echo' echo \$plxMotor->plxPlugins->aPlugins[\"plxMinifyCache\"]->cChrono();'; echo'?>'; ?>, this <?php echo $this->cChrono() ?>, oncreate : ".$plxMotor->plxPlugins->aPlugins['plxMinifyCache']->cChrono()."');</script></body>",$output);
		$output .= $www?'<!-- '.$www.$plxMotor->plxPlugins->aPlugins['plxMinifyCache']->cChrono().' -->':'';
		if ((!in_array($plxMotor->mode,$exclude)) AND ($_SERVER["REQUEST_METHOD"] != "POST")){
			$output .= "<!-- cached ".$_SERVER["QUERY_STRING"]." ".date("Y-m-d H:i:s")." -->";
			file_put_contents($cache, $output);
		} else
			$output .= "<!-- minified ".date("Y-m-d H:i:s")." -->";
	}
<?php echo ' ?>';
	}
}