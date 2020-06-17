<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 16:18
 */
class Sidebar extends dynamicMenu
{
	function __construct() {
	}

	public function getBar(){
		$html = '<div class="sidebar">'. "\r\n" .'<div class="menu">'. "\r\n";
		foreach($this->links as $item){
			$html .= $this->getLink($item);
		}
		$html .= '</div></div>'. "\r\n";
		echo $html;
	}

	private function getLink($item){
		$html = "\t" . '<div class="element">'. "\r\n" . ''
			. "\t\t" .'<a href="javascript: void(0)" ' . ($item['id'] ? 'id="Option' . $item['id'] .'"' : '');
		if($item['action']){
			$html .= ($item['action'] ? 'onClick='. "'openModule(". $item['action'] .",\"". $item['url'] ."\")'" : '');
		}else{
			$html .= ($item['url'] ? 'onClick='. "'redirect(\"". $item['url'] ."\")'" : '');
		}
		$html .=($item['active'] ? 'class="active"' : '')
			.'>'. $item['title'] .'</a>'. "\r\n".
			"\t" .'</div>'. "\r\n";
		return $html;
	}
}
?>