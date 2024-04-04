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
if(!empty($action["dbtable"])){
	$model = new ChronoModel();
	$model->Table = $action["dbtable"];

	if(!empty($action["external_database"]["database"])){
		$option = $action["external_database"];
		$model->DBO = (new Joomla\Database\DatabaseFactory)->getDriver('mysqli', $option);
	}

	if(!empty($action["json_fields"])){
		$model->JSON = $action["json_fields"];
	}

	$sql = "";
	if(!empty($action["sql"])){
		$sql = CF8::parse($action["sql"]);
	}

	$where = "";
	if(!empty($action["where"])){
		$where = CF8::parse($action["where"]);
	}

	$limit = 0;
	if(!empty($element["limit"])){
		$limit = CF8::parse($element["limit"]);
	}

	$paging = false;
	if(!empty($element["behaviors"]) && in_array("read_data.paging", $element["behaviors"])){
		$paging = true;
	}

	$single = false;
	$count = false;
	if($action["read_type"] == "single"){
		$single = true;
	}
	if($action["read_type"] == "count"){
		$count = true;
	}

	$fields = "*";
	if(!empty($action["fields"])){
		$fields = [];
		$lines = CF8::multiline($action['fields']);
		foreach($lines as $line){
			$fields[$line->name] = $line->name;
			if(!empty($line->value)){
				$fields[$line->name] = CF8::parse($line->value);
			}
		}
	}
	
	$result = $model->Select(count:$count, single:$single, where:$where, fields:$fields, limit:$limit, paging:$paging, sql:$sql);

	if($action["read_type"] != "count"){
		if(!empty($result)){
			$DisplayElements($elements_by_parent, $element["id"], "found");
		}else{
			$DisplayElements($elements_by_parent, $element["id"], "not_found");
		}
	}

	$this->set(CF8::getname($element), $result);

	$this->debug[CF8::getname($element)]['returned'] = $result;

	if(!is_null($result) && $action["read_type"] == "single" && isset($action["behaviors"]) && in_array("read_data.merge_data", $action["behaviors"])){
		if(!empty($result)){
			foreach($result as $k => $v){
				if(!isset($this->data[$k])){
					$this->data[$k] = $v;
				}
			}
		}
		// $this->MergeData($result); // merge overwrites new entered data with row data if page is reloaded
	}
}