<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 16:18
 */
class HeaderMenu extends dynamicMenu
{

	function __construct() {
	}

	public function getHeaderMenu(){
		$html = '<div id="headerMenu" class="header_menu fix">'. "\r\n" .'<ul class="menu">'. "\r\n";
		$nbOfItems = count($this->links);
		$itemCount = 1;
		foreach($this->links as $item){
			$html .= $this->getLink($item);
			if($itemCount != $nbOfItems){
				$html .= '<span class="separator"> </span>';
			}
			$itemCount++;
		}
		$html .= '</ul></div></div>'. "\r\n";
		echo $html;
	}

	private function getLink($item){
		return '<div class="element ' . ($item['active'] ? 'active"' : '"')
					.'><a href="javascript: void(0)"'
					. ($item['url'] ? 'onClick='. "'redirect(\"". $item['url'] ."\")'" : '')
					. ($item['action'] ? 'onClick='. "'". $item['action'] ."'" : '')
					.'>'. $item['title'] .'</a>
				</div>';
	}
}
?>