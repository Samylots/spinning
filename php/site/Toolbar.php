<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 16:18
 */
class Toolbar extends dynamicMenu
{
	private $title;
	private $class;
	private $customSearch;

	function __construct($class = "toolbar") {
		$this->class = $class;
	}

	/**
	 * @param $title
	 * @param null $link
	 * @param null $action
	 * @param bool $active change to true by default!
	 */
	public function addOption( $title, $link = null, $action = null, $active = true ){
		parent::addOption($title, $link, $action, $active);
	}


	public function setTitle($title){
		$this->title = $title;
	}

	public function getBar(){
		$html = '<div class="'.$this->class.'">' . "\r\n";
		if($this->title){
			$html .= '<div class="title">' . $this->title . '</div>';
		}
		$html .= '<div class="menu">' . "\r\n";
		foreach( $this->links as $item ){
			$html .= $this->getLink($item);
		}
		if($this->customSearch){
			$html .= $this->customSearch;
		}
		$html .= '</div></div>' . "\r\n";
		echo $html;
	}

	private function getLink($item){
		if(!$item['active']){
			return '';
		}
		return "\t" . '<button class="button edit"'. "\r\n" . ''
			. "\t\t" . ($item['id'] ? 'id="Option' . $item['id'] .'"' : '')
			. ($item['url'] ? 'onClick='. "'redirect(\"". $item['url'] ."\")'" : '')
			. ($item['action'] ? 'onClick='. "'". $item['action'] ."'" : '')
			.'>'. $item['title'] ."\r\n".
			"\t" .'</button>'. "\r\n";
	}

	public function setCustomSearch($htmlElement){
		$this->customSearch = $htmlElement;
	}
}
?>