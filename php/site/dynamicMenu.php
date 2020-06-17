<?php

/**
 * Created by PhpStorm.
 * User: 436
 * Date: 2016-03-18
 * Time: 18:45
 */
class dynamicMenu
{
	protected $links = array();
	private $request = null;

	function __construct() {
	}

	public function addOption($title, $link = null, $action = null, $active = false){
		$existingLinks = search($this->links, 'title',$title);
		if(empty($existingLinks)){
			$this->links[] = [
				'id' => count($this->links) +1,
				'title' => $title,
				'url' => $link,
				'action' => $action,
				'active' => $active
			];
		}
	}

	public function replaceOption($id, $title, $link = null, $action = null, $active = false){
		$optionToReplace = search($this->links, 'id',$id);
		if(($key = array_search($optionToReplace[0]['id'], array_column($this->links, 'id'))) !== false) {
			$this->links[$key] = [
				'id' => $id,
				'title' => $title,
				'url' => $link,
				'action' => $action,
				'active' => $active
			];
		}
	}

	public function removeOption($id){
		$isRemoved = false;
		foreach($this->links as $option){
			if($isRemoved){
				$this->links[$option['id']-1]['id'] -=1;
			}else{
				if($option['id'] == $id){
					unset($this->links[$option['id']-1]);
					$isRemoved = true;
				}
			}
		}
	}

	public function openSelectedModule($defaultAction){
		$found = false;
		if(get('m')){
			$module = get('m');
			foreach( $this->links as $link ){
				if ( $link[ 'url' ] == $module ){
					$found = true;
					?>
					<script>
						isLoading = true;
						<?php
						 $action = $link[ 'action' ];
						if(strpos($action,')') > -1){
							echo substr($action,0,strpos($action,')')) . ',';
						}else{
							echo $action . '(';
						}?> function (html) {
							$.when(setContent(html)).done(
								toggleLoadingDown);
						});
					</script>
					<?php
				}
			}
			if(!$found){
				echo ($defaultAction ? '<script>isLoading = true;</script>' . $defaultAction : '<script>askForToggle();</script>');
			}
		}else{
			echo ($defaultAction ? '<script>isLoading = true;</script>' .$defaultAction : '<script>askForToggle();</script>');
		}
	}

	public function setCurrentRequest($url){
		$this->request = $url;
	}

	public function hasToBeActive($pagesArray, $debug = false){
		if($this->request != null){
			$currentRequest = basename($this->request);
		}else{
			$currentRequest = basename($_SERVER['REQUEST_URI']);
		}
		foreach($pagesArray as $page){
			if(strpos($page, '*') !== false){
				$page = substr($page,0,strlen($page)-1);
				if($debug){
					var_dump( $currentRequest . ' VS ' . $page);
				}
				if(strpos($currentRequest, $page) !== false){
					return true;
				}
			}
			if($debug){
				var_dump( $currentRequest . ' VS ' . $page);
			}
			if ($page === $currentRequest) {
				return true;
			}
		}
		return false;
	}

}