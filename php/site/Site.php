<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 17:14
 */
class Site
{
	public $sidebar;
	public $toolbar;
	public $user;
	private $cssStyles = array();
	private $scripts = array();
	private $modules = array();

	function __construct() {
		$this->user = new User();
		$this->sidebar = new Sidebar();
		$this->toolbar = new Toolbar("contentTitle");
		$this->addCss('main');
		$this->addCss('spectrum');
		$this->addCss('wickedpicker.min.css');
		$this->addScript('utils/bootstrapModals');
		$this->addScript('utils/smoothHtmlChanger');
		$this->addScript('utils/weekpicker');
		$this->addScript('utils/spectrum');
		$this->addScript('utils/validator');
		$this->addScript('utils/tinycolor');
		$this->addScript('utils/moment');
		$this->addScript('utils/wickedpicker');
		$this->addScript('utils/smoothscroll');
		$this->addScript('utils/bootstrapModals');
		$this->addScript('utils/jquery.ui.autocomplete.html.js');
		$this->addModule('modules/memberSpace');
		DB::getInstance()->testDB();
	}

	public function addCss($cssName){
		if(strpos($cssName,'.') > 0){
			$cssName = substr($cssName,0,-4);
		}
		$existingStyle = search($this->cssStyles, 'name',$cssName);
		if(empty($existingStyle)){
			$this->cssStyles[] = [
				'name' => $cssName,
				'import' => '<link rel="stylesheet" href="css/'. $cssName .'.css">'
			];
		}
	}

	public function addScript($scriptName){
		if(strpos($scriptName,'.') > 0){
			$scriptName = substr($scriptName,0,-3);
		}
		$existingScripts = search($this->scripts, 'name',$scriptName);
		if(empty($existingScripts)){
			$this->scripts[] = [
				'name' => $scriptName,
				'import' => '<script src="js/'. $scriptName .'.js"></script>'
			];
		}
	}

	public function addModule($scriptName){
		if(strpos($scriptName,'.') > 0){
			$scriptName = substr($scriptName,0,-3);
		}
		$existingModules = search($this->modules, 'name',$scriptName);
		if(empty($existingModules)){
			$this->modules[] = [
				'name' => $scriptName,
				'import' => '<script src="js/'. $scriptName .'.js"></script>'
			];
		}
	}

	public function start(){
		echo '<!DOCTYPE html> <html lang="'. Config::$lang .'"> <head> <meta charset="utf-8">
			<title>'. Config::$title .'</title>' .
			'<link rel="shortcut icon" href="img/site/logoIcon2.ico">';
		$this->loadCss();
		$this->loadScripts();
		echo '</head>';
		echo '<body>';
		$this->getHeader();
		$this->getModals();
		echo '<div class="underHeader">'. "\r\n";
		printDatas();
		$this->loadModules();
		$this->sidebar->getBar();
		echo '<div id="innerPage" class="innerPage">'. "\r\n";
		echo '<div id="content" class="content" style="display: none;">'. "\r\n";
		$this->toolbar->getBar();
		echo "\t" . '<div class="innerContent">'. "\r\n";
	}

	private function getHeader(){
		include( 'header.php' );
	}

	private function getModals(){
		include( 'bootstrapModals.php' );
	}

	public function end($defaultAction = null){
		$this->sidebar->openSelectedModule($defaultAction);
		$this->getFooter();
		echo '</body></html>';
	}

	private function getFooter(){
		echo '</div>';//innerContent
		echo '</div>';//content
		$this->getLoading();
		echo '</div>';//innerPage
		include('footer.php');
		echo '</div>';//underHeader
		echo '<div class="contentLogo" style="display: none;">'. "\r\n" .'</div>'. "\r\n";
		echo '<script>
				slideInSidebar();
				if(!window.history.state){
					updateHistory(window.location.href); //init manual history handling
				}
			</script>';
	}

	private function getLoading(){
		echo '<div class="loading">'. "\r\n";
		echo "Chargement en cours...";
		echo '</div>';//loading
	}

	private function loadCss(){
		include( 'styles.php' );
		foreach($this->cssStyles as $css){
			echo $css['import']. "\r\n";
		}
	}
	private function loadScripts(){
		$this->addScript('functions');
		include( 'scripts.php' );
		foreach($this->scripts as $script){
			echo $script['import']. "\r\n";
		}
	}
	private function loadModules(){
		foreach($this->modules as $module){
			echo $module['import']. "\r\n";
		}
	}
}