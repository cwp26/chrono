<?php
/**
* ChronoForms 8
* Copyright (c) 2023 ChronoEngine.com, All rights reserved.
* Author: (ChronoEngine.com Team)
* license:     GNU General Public License version 2 or later; see LICENSE.txt
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
defined('_JEXEC') or die('Restricted access');
?>
<?php
	$class = "";
	if(!empty($element["color"])){
		$class .= "colored ".$element["color"]["name"];
	}else{
		$class .= "colored "."slate";
	}
	if(!empty($element["icon"])){
		if(!empty($element["icon"]["position"])){
			$class .= " ".$element["icon"]["position"];
		}
		$class .= " iconed";
	}
	if(!empty($element["position"])){
		$class .= " ".$element["position"];
	}

	$field = array_merge($formElementToField($element), ["class" => $class]);
	
	if($element["btype"] == "lastpage"){
		$active_page = intval($this->get("app_active_page", 0));
		$last_page = $active_page > 0 ? $active_page - 1 : 0;
		$icon = !empty($field["icon"]) ? Chrono::ShowIcon($field["icon"]) : "";
		echo '<a class="nui button '.$field["class"].'" href="'.Chrono::r(Chrono::addUrlParam($this->current_url, ["chronopage" => $last_page])).'">'.$icon.$field["label"].'</a>';
	}else{
		new FormField(... $field);
	}
?>