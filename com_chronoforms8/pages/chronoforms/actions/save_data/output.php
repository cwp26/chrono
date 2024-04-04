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
	$data = CF8::parse($action["datasource"]);
	if(!is_array($data)){
		$data = [];
	}
	$model = new ChronoModel();
	$model->Table = $action["dbtable"];

	if(!empty($action["external_database"]["database"])){
		$option = $action["external_database"];
		$model->DBO = (new Joomla\Database\DatabaseFactory)->getDriver('mysqli', $option);
	}

	if(!empty($action["pkey"])){
		$model->PKey = $action["pkey"];
	}

	if(!empty($action["json_fields"])){
		$model->JSON = $action["json_fields"];
	}

	if(!empty($action["allowed_fields"])){
		foreach($data as $k => $v){
			if(!in_array($k, $action["allowed_fields"])){
				unset($data[$k]);
			}
		}
	}else{
		$fields = [];
		$tcolumns = $model->Select(sql: "SHOW FULL COLUMNS FROM ".$action["dbtable"]);
		foreach($tcolumns as $tcolumn){
			$fields[] = $tcolumn["Field"];
		}
		$model->Fields = $fields;
	}

	if(!empty($action["data_override"])){
		$lines = CF8::multiline($action["data_override"]);
		foreach($lines as $line){
			if(str_starts_with($line->name, "-")){
				$line->name = substr_replace($line->name, "", 0, 1);
				if(isset($data[$line->name])){
					unset($data[$line->name]);
				}
				continue;
			}
			$data[$line->name] = CF8::parse($line->value);
		}
	}


	// foreach($data as $k => $v){
	// 	$model->$k = $v;
	// }

	if(empty($action["where"])){
		$result = $model->Insert($data);
	}else{
		$result = $model->Update($data, where:CF8::parse($action["where"]));
	}

	$this->set(CF8::getname($element), $result);

	// Chrono::pr($data);
	
	// $autoid = 0;
	// if($result !== false && empty($action["where"])){
	// 	if($result == true){
	// 		$autoid = $data[$model->PKey];
	// 	}else{
	// 		$autoid = $result;
	// 		$result = true;
	// 	}
	// 	$this->set(CF8::getname($element)."_autoid", $autoid);

	// 	$this->debug[CF8::getname($element)]['autoid'] = $autoid;
	// }

	if($result !== false && empty($action["where"])){
		if($result == true){
			$result = $data;
		}
	}

	$this->debug[CF8::getname($element)]['returned'] = $result;
}