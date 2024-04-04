<?php

/**
 * ChronoForms 8
 * Copyright (c) 2023 ChronoEngine.com, All rights reserved.
 * Author: (ChronoEngine.com Team)
 * license:     GNU General Public License version 2 or later; see LICENSE.txt
 * Visit http://www.ChronoEngine.com for regular updates and information.
 **/
defined('_JEXEC') or die('Restricted access');

$conditions = [['id', "=", $this->data("id")]];
if ($this->DataExists("chronoform")) {
	$conditions = [['alias', "=", $this->data("chronoform")]];
}
if (!$this->isAdmin()) {
	$conditions[] = "AND";
	$conditions[] = ['published', "=", 1];
}

$row =  CF8Model::instance()->Select(conditions: $conditions, single: true);
$elements = [1 => ["id" => 1, "type" => "page"]];
if (empty($row)) {
	echo "Form not found.";
	return;
}
$elements = $row["elements"];

if(!$this->isAdmin() && !empty($row["params"]["acl"])){
	if(!in_array($row["params"]["acl"], $this->user()->getAuthorisedViewLevels())){
		ChronoSession::setFlash("error", $row["params"]["acl_error"]);
		return;
	}
}
if(isset($this->vars["app_viewlevels"])){
	unset($this->vars["app_viewlevels"]);
}

$this->loadLanguageFile(alias:"/locales");

if(!empty($row["params"]["locales"])){
	foreach($row["params"]["locales"]["lang"] as $k => $lang){
		if($lang == $this->locale){
			$strings = $row["params"]["locales"]["strings"][$k];
			$lines = CF8::multiline($strings);
			foreach($lines as $line){
				CF8::$locales[$line->name] = $line->value;
			}

			break;
		}
	}
}

$views_path = __DIR__ . '/chronoforms/views/';

$active_page = 0;
$active_section = "load";
$pages_ids = [];
$pages = [];
foreach ($row["elements"] as $element) {
	if ($element["type"] == "page") {
		$pages_ids[] = $element["id"];
		$pages[] = $element;
	}
}

$elements_by_parent = [];
foreach ($row["elements"] as $element) {
	if ($element["type"] != "page") {
		$elements_by_parent[$element["parent"]][] = $element;
	}
}

$sectoken = "";
$completed_pages = ChronoSession::get("chronoforms8_formpages_" . $row["id"], []);
$completed_data = ChronoSession::get("chronoforms8_formdata_" . $row["id"], []);
$completed_vars = ChronoSession::get("chronoforms8_formvars_" . $row["id"], []);
$completed_elements = ChronoSession::get("chronoforms8_formelements_" . $row["id"], []);

if ($this->isPost && $_POST["chronoform"] == $row["alias"]) {
	if (!$this->DataExists("sectoken") || ChronoSession::get("sectoken". $row["id"], "") != $this->data("sectoken")) {
		$active_page = 0;
		$active_section = "load";
		$this->errors[] = "Your session has timed out or your tried to access a wrong page.";
	} else {
		if ($this->DataExists("chronopage") && strlen($this->data("chronopage"))) {
			$active_page = intval($this->data("chronopage"));
		}

		$active_section = "submit";

		// ChronoSession::clear("sectoken");
	}
} else {
	if ($row["params"]["page_jump"] == "1") {
		if ($this->DataExists("chronopage") && strlen($this->data("chronopage"))) {
			$active_page = intval($this->data("chronopage"));
		}
	}
}

if ($active_page > count($pages_ids) - 1) {
	$active_page = count($pages_ids) - 1;
} else if ($active_page < 0) {
	$active_page = 0;
}

if ($active_page > 0) {
	for ($i = 0; $i < $active_page; $i++) {
		if (!in_array($i, $completed_pages)) {
			$active_page = $i;
			$active_section = "load";
			break;
		}
	}
}

// Chrono::pr($completed_pages);
// Chrono::pr($active_page);
// Chrono::pr($active_section);

$active_page_id = $pages_ids[$active_page];

// $CheckSecurityElements = function ($elements_by_parent, $parent_id, $section) use (&$CheckSecurityElements, &$completed_elements) {
// 	$elements = $elements_by_parent[$parent_id];
// 	foreach ($elements as $element) {
// 		if(isset($element["settings"]["disabled"]) && !empty($element["settings"]["disabled"])){
// 			continue;
// 		}
// 		if(!empty($element["acl"])){
// 			if(!in_array($element["acl"], $this->user()->getAuthorisedViewLevels())){
// 				continue;
// 			}
// 		}
// 		if (((strlen($section) > 0) && $element["section"] == $section) || (strlen($section) == 0)) {
// 			if (str_starts_with($element["name"], "security_") && isset($completed_elements[$element["id"]])) {
// 				$element["type"] = "actions";
// 				// Chrono::pr($element);
// 				require(__DIR__ . "/display_element.php");
// 			} else {
// 				if (isset($elements_by_parent[$element["id"]]) && isset($completed_elements[$element["id"]])) {
// 					$CheckSecurityElements($elements_by_parent, $element["id"], "");
// 				}
// 			}
// 			if (!empty($this->errors)) {
// 				break;
// 			}
// 		}
// 	}
// };

// $ValidateField = function ($element) {
// 	$name = $element["fieldname"];
// 	$data = Chrono::getVal($this->data, $name);

// 	foreach ($element as $k => $v) {
// 		if (str_starts_with($k, "validation_")) {
// 			$rule = str_replace("validation_", "", $k);
// 			$error = isset($element[$k]["prompt"]) ? $element[$k]["prompt"] : "";
// 			switch ($rule) {
// 				case "required":
// 					if ($element["name"] == "field_file") {
// 						$file = Chrono::getVal($_FILES, $name);
// 						if (is_null($file)) {
// 							$this->errors[$name] = $error;
// 						}
// 					} else {
// 						if (
// 							is_null($data) ||
// 							(is_string($data) && strlen($data) == 0) ||
// 							(is_array($data) && count($data) == 0)
// 						) {
// 							$this->errors[$name] = $error;
// 						}
// 					}
// 					break;
// 				case "function":
// 					if(!is_null($data) && !empty($element[$k]["php"])){
// 						$value = $data;
// 						$result = eval($element[$k]["php"]);
// 						if(!is_null($result) && $result !== true){
// 							$this->errors[$name] = $result;
// 						}
// 					}
// 					break;
// 			}
// 		}
// 	}
// };

// $CheckFieldsValidations = function ($elements_by_parent, $parent_id, $section) use (&$CheckFieldsValidations, &$ValidateField, &$completed_elements) {
// 	$elements = $elements_by_parent[$parent_id];
// 	foreach ($elements as $element) {
// 		if(isset($element["settings"]["disabled"]) && !empty($element["settings"]["disabled"])){
// 			continue;
// 		}
// 		if(!empty($element["acl"])){
// 			if(!in_array($element["acl"], $this->user()->getAuthorisedViewLevels())){
// 				continue;
// 			}
// 		}
// 		if (((strlen($section) > 0) && $element["section"] == $section) || (strlen($section) == 0)) {
// 			if (str_starts_with($element["name"], "field_") && isset($completed_elements[$element["id"]])) {
// 				foreach ($element as $k => $v) {
// 					if (str_starts_with($k, "validation_")) {
// 						$ValidateField($element);
// 					}
// 				}
// 				// $element["type"] = "actions";
// 				// // Chrono::pr($element);
// 				// require(__DIR__ . "/display_element.php");
// 			} else {
// 				if (isset($elements_by_parent[$element["id"]]) && isset($completed_elements[$element["id"]])) {
// 					$CheckFieldsValidations($elements_by_parent, $element["id"], "");
// 				}
// 			}
// 			// if(!empty($this->errors)){
// 			// 	break;
// 			// }
// 		}
// 	}
// };

// $FileUploadElements = function ($elements_by_parent, $parent_id, $section) use (&$FileUploadElements, &$completed_elements) {
// 	$elements = $elements_by_parent[$parent_id];
// 	foreach ($elements as $element) {
// 		if(isset($element["settings"]["disabled"]) && !empty($element["settings"]["disabled"])){
// 			continue;
// 		}
// 		if(!empty($element["acl"])){
// 			if(!in_array($element["acl"], $this->user()->getAuthorisedViewLevels())){
// 				continue;
// 			}
// 		}
// 		if (((strlen($section) > 0) && $element["section"] == $section) || (strlen($section) == 0)) {
// 			if ($element["name"] == "field_file") {
// 				$name = $element["fieldname"];
// 				if(!isset($_FILES[$name]) || empty($_FILES[$name]["name"]) || (isset($_FILES[$name]["name"][0]) && empty($_FILES[$name]["name"][0]))){
// 					continue;
// 				}

// 				$target_dir = $this->front_path."uploads/";
				
// 				if(!empty($element["upload_dir"])){
// 					$element["upload_dir"] = CF8::parse($element["upload_dir"]);
// 					if(file_exists($element["upload_dir"])){
// 						$target_dir = $element["upload_dir"];
// 					}else{
// 						$this->errors[$name] = "Error, upload directory does not exist.";
// 						$this->debug[$element['name'].$element['id']]['error'] = "Upload dir not available: ".$element["upload_dir"];
// 						return;
// 					}
// 				}

// 				// Chrono::pr($_FILES[$name]);
// 				// die();

// 				$_files = $_FILES[$name];
// 				$multiple = true;
// 				if(!is_array($_FILES[$name]["name"])){
// 					$multiple = false;
// 					foreach($_FILES[$name] as $key => $value){
// 						$_files[$key] = [$value];
// 					}
// 				}

// 				foreach($_files["name"] as $fk => $_file_name){
// 					$pathinfo = pathinfo(basename($_files["name"][$fk]));
				
// 					$file_extension = strtolower($pathinfo["extension"]);
// 					$file_name = basename($pathinfo["filename"]);
// 					$file_slug = Chrono::slug($file_name);
	
// 					$file_safename = gmdate('YmdHis').'_'.$file_slug.".".$file_extension;
// 					if(!empty($element["filename_provider"])){
// 						$element["filename_provider"] = CF8::parse($element["filename_provider"]);
// 						$file_safename = CF8::parse($element["filename_provider"], ["file" => [
// 							"name" => $file_name,
// 							"safename" => $file_slug,
// 							"extension" => $file_extension,
// 						]]);
// 						// $file_safename = str_replace(["NAME", "SLUG", "EXTENSION"], [$file_name, $file_slug, $file_extension], $element["filename_provider"]);
// 					}
	
// 					$target_file = $target_dir . $file_safename;
// 					$target_file = str_replace(["/", "\\"], "/", $target_file);
					
// 					if (file_exists($target_file)) {
// 						$this->errors[$name] = "Sorry, file already exists.";
// 						return;
// 					}
	
// 					// Check file size
// 					if ($_files["size"][$fk] > intval($element["max_size"]) * 1000) {
// 						$this->errors[$name] = sprintf("Sorry, your file is too large, the maximum file size is %s KB.", intval($element["max_size"]));
// 						return;
// 					}
	
// 					// Allow certain file formats
// 					$element["extensions"] = !empty($element["extensions"]) ? $element["extensions"] : [];
// 					if (!in_array($file_extension, (array)$element["extensions"])) {
// 						$this->errors[$name] = "Sorry, only ".implode(", ", (array)$element["extensions"])." files are allowed.";
// 						return;
// 					}
	
// 					if (move_uploaded_file($_files["tmp_name"][$fk], $target_file)) {
// 						if($multiple){
// 							$prev = $this->data($name, []);
// 							$prev[] = $file_safename;
// 							$this->SetData($name, $prev);
// 						}else{
// 							$this->SetData($name, $file_safename);
// 						}

// 						$this->debug[$element['name'].$element['id']]['success'][] = "File ".$file_name.".".$file_extension." was uploaded to ".$target_file;
// 					} else {
// 						$this->errors[$name] = "Sorry, there was an error uploading your file.";
// 						return;
// 					}
// 				}

// 				$path = $target_dir . $file_safename;
// 				if($multiple){
// 					$path = [];
// 					$files = $this->data($name, []);
// 					foreach($files as $file){
// 						$path[] = $target_dir . $file;
// 					}
// 				}
// 				$this->set($element['name'].$element['id']."_upload", [
// 					"path" => $path,
// 				]);
// 			} else {
// 				if (isset($elements_by_parent[$element["id"]]) && isset($completed_elements[$element["id"]])) {
// 					$FileUploadElements($elements_by_parent, $element["id"], "");
// 				}
// 			}
// 		}
// 	}
// };

// Chrono::loadAsset("/assets/events.js");
// $form_html_id = "chronoform-".$row["alias"];
// $form_id = $row["id"];
// $eventsCode = [
// 	"var form = document.querySelector('#$form_html_id');"
// ];
// $eventsCode[] = "var Events = {};";
// $eventsCode[] = "function TestEvent(ename){
// 	let last = true;
// 	Events[ename].forEach(fn => {
// 		let result = fn();
// 		last = last && result;
// 	});

// 	if(last === true){
// 		form.dispatchEvent(new CustomEvent(ename));
// 	}
// }";
// $eventsCode[] = "function GetValues(field, mfield){
// 	if(field.getAttribute('type') == 'checkbox' || field.getAttribute('type') == 'radio'){
// 		let values = [];
// 		mfield.forEach(f => {
// 			if(f.checked){
// 				values.push(f.value);
// 			}
// 		});
// 		return values;
// 	}else{
// 		return [field.value];
// 	}
// }";
// $triggers = [];
// $identifiers = [];

// $BuildElementEvents = function($element) use(&$eventsCode, &$triggers, &$identifiers){
// 	if(!empty($element["behaviors"]) && !empty($element["triggers"])){
		
// 	}
	// if($element["type"] == "views" && str_starts_with($element["name"], "field_")){
	// 	$js_field_name = 'field_'.$element["id"];
	// 	$field_name = $element["fieldname"];
	// 	switch($element["name"]){
	// 		case "field_checkboxes":
	// 			$field_name = $element["fieldname"]."[]";
	// 			break;
	// 	}
	// 	$field_selector = "[name=\"$field_name\"]";
	// 	$js_field_holder = "$js_field_name.closest('.field')";

	// 	switch($element["name"]){
	// 		case "field_button":
	// 			$js_field_holder = "$js_field_name";
	// 			break;
	// 		case "field_checkboxes":
	// 		case "field_radios":
	// 			$js_field_holder = "$js_field_name.closest('.fields').closest('.field')";
	// 			break;
	// 	}

	// 	if(!isset($identifiers[$js_field_name])){
	// 		$identifiers[$js_field_name] = true;
	// 		$eventsCode[] = "let $js_field_name = form.querySelector('$field_selector');";
	// 		$eventsCode[] = "let multi_$js_field_name = form.querySelectorAll('$field_selector');";
	// 	}

	// 	if(!empty($element["behaviors"]) && !empty($element["listeners"])){
	// 		foreach($element["listeners"] as $tk => $listener){
	// 			$trigger_name = $listener["trigger"];
	// 			$actions = $listener["actions"];
	// 			$eventsCode[] = "form.addEventListener('$trigger_name', e => {";
	// 			switch($actions){
	// 				case "hide":
	// 					$eventsCode[] = "$js_field_holder.classList.add('hidden');";
	// 					break;
	// 				case "show":
	// 					$eventsCode[] = "$js_field_holder.classList.remove('hidden');";
	// 					break;
	// 				case "disable_validation":
	// 					$eventsCode[] = "$js_field_name.setAttribute('disable-validations', '1');";
	// 					$eventsCode[] = "$js_field_holder.classList.remove('error');";
	// 					$eventsCode[] = "if($js_field_holder.querySelector('.errormsg')){
	// 						$js_field_holder.querySelector('.errormsg').remove();
	// 					};";
	// 					$eventsCode[] = "if($js_field_holder.querySelector('.errormark')){
	// 						$js_field_holder.querySelector('.errormark').classList.add('hidden');
	// 					};";
	// 					break;
	// 				case "enable_validation":
	// 					$eventsCode[] = "$js_field_name.removeAttribute('disable-validations');";
	// 					$eventsCode[] = "if($js_field_holder.querySelector('.errormark')){
	// 						$js_field_holder.querySelector('.errormark').classList.remove('hidden');
	// 					};";
	// 					break;
	// 			}
	// 			$eventsCode[] = "});";
	// 		}
	// 	}
		
	// 	if(!empty($element["behaviors"]) && !empty($element["triggers"])){
	// 		$change_event = "change";
	// 		if(in_array($element["name"], ["field_text", "field_password", "field_textarea"])){
	// 			$change_event = "input";
	// 		}
	// 		foreach($element["triggers"] as $tk => $trigger){
	// 			$trigger_name = $trigger["name"];
	// 			$trigger_value = [];
	// 			if(!empty($trigger["value"])){
	// 				$trigger_value = json_encode((array)$trigger["value"]);
	// 			}
	// 			if(!isset($triggers[$trigger_name])){
	// 				$triggers[$trigger_name] = true;
	// 				$eventsCode[] = "Events['$trigger_name'] = [];";
	// 			}

	// 			$add_change_listener = true;
				
	// 			switch($trigger["condition"]){
	// 				case "ready":
	// 					$eventsCode[] = "TestEvent('$trigger_name');";
	// 					$add_change_listener = false;
	// 					break;
	// 				case "change":
	// 					break;
	// 				case "in":
	// 					$eventsCode[] = "Events['$trigger_name'].push(() => {
	// 						let values = GetValues($js_field_name, multi_$js_field_name);
	// 						let result = false;
	// 						$trigger_value.forEach(v => {
	// 							if(values.includes(v)){
	// 								result = true;
	// 								return;
	// 							}
	// 						});
	// 						return result;
	// 					});";
	// 					break;
	// 				case "not-in":
	// 					$eventsCode[] = "Events['$trigger_name'].push(() => {
	// 						let values = GetValues($js_field_name, multi_$js_field_name);
	// 						let result = true;
	// 						$trigger_value.forEach(v => {
	// 							if(values.includes(v)){
	// 								result = false;
	// 								return;
	// 							}
	// 						});
	// 						return result;
	// 					});";
	// 					break;
	// 				case "empty":
	// 					$eventsCode[] = "Events['$trigger_name'].push(() => {
	// 						let values = GetValues($js_field_name, multi_$js_field_name);
	// 						return (values.length == 0);
	// 					});";
	// 					break;
	// 				case "not-empty":
	// 					$eventsCode[] = "Events['$trigger_name'].push(() => {
	// 						let values = GetValues($js_field_name, multi_$js_field_name);
	// 						return (values.length != 0);
	// 					});";
	// 					break;
	// 			}

	// 			if($add_change_listener){
	// 				$eventsCode[] = "multi_$js_field_name.forEach(input => {
	// 					input.addEventListener('$change_event', e => {
	// 						TestEvent('$trigger_name');
	// 					});
	// 				});";
	// 			}
	// 		}
	// 	}
	// }
// };

$ProcessElementsSubmit = function ($elements_by_parent, $parent_id, $section) use (&$ProcessElementsSubmit, &$completed_elements) {
	$elements = $elements_by_parent[$parent_id];
	foreach ($elements as $element) {
		if(isset($element["settings"]["disabled"]) && !empty($element["settings"]["disabled"])){
			continue;
		}
		if(!empty($element["acl"])){
			if(!in_array($element["acl"], $this->user()->getAuthorisedViewLevels())){
				continue;
			}
		}
		if (((strlen($section) > 0) && $element["section"] == $section) || (strlen($section) == 0)) {
			if(isset($completed_elements[$element["id"]])){
				$view_path = __DIR__.'/chronoforms/'.$element["type"].'/'.$element["name"];
				if(file_exists($view_path."/submit.php")){
					require($view_path."/submit.php");
				}

				if(!empty($element["behaviors"])){
					foreach($element["behaviors"] as $behavior){
						$bv_path = __DIR__.'/chronoforms/behaviors/'.$behavior;
						if(str_contains($behavior, ".")){
							$bv_path = __DIR__.'/chronoforms/'.$element["type"].'/'.$element["name"].'/behaviors/'.explode(".", $behavior)[1];
						}
						// echo $bv_path;
						if(file_exists($bv_path."/submit.php")){
							require($bv_path."/submit.php");
						}
					}
				}

				if (isset($elements_by_parent[$element["id"]])) {
					$ProcessElementsSubmit($elements_by_parent, $element["id"], "");
				}
			}
			// if (!empty($this->errors)) {
			// 	break;
			// }
		}
	}
};


if ($active_section == "submit") {
	$ProcessElementsSubmit($elements_by_parent, $active_page_id, "load");
	// $CheckSecurityElements($elements_by_parent, $active_page_id, "load");
	// $CheckFieldsValidations($elements_by_parent, $active_page_id, "load");

	if (!empty($this->errors)) {
		$active_section = "load";
	} else {
		// $FileUploadElements($elements_by_parent, $active_page_id, "load");

		if (!empty($this->errors)) {
			$active_section = "load";
		} else {
			// $completed_data = array_merge($completed_data, $this->data);
			// ChronoSession::set("chronoforms8_formdata_" . $row["id"], $completed_data);

			// if (!in_array($active_page, $completed_pages)) {
			// 	$completed_pages[] = $active_page;
			// 	ChronoSession::set("chronoforms8_formpages_" . $row["id"], $completed_pages);
			// }
		}
	}
}

if (isset($completed_data["output"])) {
	unset($completed_data["output"]);
}
// $this->MergeData($completed_data);
$this->data = array_merge($completed_data, $this->data);
// $this->MergeVars($completed_vars);
$this->vars = array_merge($completed_vars, $this->vars);
// Chrono::pr($active_page);
// Chrono::pr($active_section);
// $this->vars["formelements_" . $row["id"]] = [];

$DisplayElements = function ($elements_by_parent, $parent_id, $section) use (&$DisplayElements, &$completed_elements, $active_page_id, $pages_ids, $active_page, $active_section) {
	static $current_page_id;
	if(in_array($parent_id, $pages_ids)){
		$current_page_id = $parent_id;
	}

	$elements = !empty($elements_by_parent[$parent_id]) ? $elements_by_parent[$parent_id] : [];
	foreach ($elements as $element) {
		if(isset($element["settings"]["disabled"]) && !empty($element["settings"]["disabled"])){
			continue;
		}
		if(!empty($element["acl"])){
			if(!in_array($element["acl"], $this->user()->getAuthorisedViewLevels())){
				continue;
			}
		}
		if ($element["section"] == $section) {
			// $BuildElementEvents($element);

			require(__DIR__ . "/display_element.php");

			$completed_elements[$element["id"]] = $element;

			$completed_elements[$element["id"]]["page_id"] = $current_page_id;

			// if ($active_section == "load") {
			// 	$completed_elements[$element["id"]]["page_id"] = $active_page_id;
			// }else{
			// 	$next_page = $active_page + 1;
			// 	if(isset($pages_ids[$next_page])){
			// 		$next_page_id = $pages_ids[$next_page];
			// 		$completed_elements[$element["id"]]["page_id"] = $next_page_id;
			// 	}
			// }
		}
	}
};

ob_start();
$this->set("app_active_page", $active_page);
$DisplayElements($elements_by_parent, $active_page_id, $active_section);

$completed_vars = array_merge($completed_vars, $this->vars);
ChronoSession::set("chronoforms8_formvars_" . $row["id"], $completed_vars);
ChronoSession::set("chronoforms8_formelements_" . $row["id"], $completed_elements);

$next_page_on = false;
$next_page = $active_page;
$next_page_id = $pages_ids[$next_page];
if ($row["params"]["next_page"] == "1") {
	if ($active_section == "submit") {
		if ($active_page < count($pages_ids) - 1) {
			$next_page = $active_page + 1;
			$next_page_id = $pages_ids[$next_page];

			$this->set("app_active_page", $next_page);
			$DisplayElements($elements_by_parent, $next_page_id, "load");
			$next_page_on = true;

			$completed_data = array_merge($completed_data, $this->data);
			ChronoSession::set("chronoforms8_formdata_" . $row["id"], $completed_data);

			if (!in_array($active_page, $completed_pages)) {
				$completed_pages[] = $active_page;
				ChronoSession::set("chronoforms8_formpages_" . $row["id"], $completed_pages);
			}

			$completed_vars = array_merge($completed_vars, $this->vars);
			ChronoSession::set("chronoforms8_formvars_" . $row["id"], $completed_vars);
			ChronoSession::set("chronoforms8_formelements_" . $row["id"], $completed_elements);
		}else{
			// last page
			// if last page submit event
			if ($active_page == count($pages_ids) - 1) {
				if ($row["params"]["log_data"] == "1") {
					$data = [];
					foreach ($elements as $element) {
						if ($element["type"] == "views") {
							if (str_starts_with($element["name"], "field_") && $element["name"] != "field_button") {
								if (!empty($element["fieldname"])) {
									$data[$element["id"]] = Chrono::getVal($this->data, $element["fieldname"]);
								}
							}
						}
					}
					$logdata = [
						"form_id" => $row["id"],
						"user_id" => $this->user()->id,
						"ip" => $_SERVER['REMOTE_ADDR'],
						"created" => gmdate("Y-m-d H:i:s"),
						"data" => json_encode($data),
					];
					CF8LogModel::instance()->Insert($logdata);
				}

				// Chrono::pr($completed_elements);

				ChronoSession::clear("chronoforms8_formpages_" . $row["id"]);
				ChronoSession::clear("chronoforms8_formdata_" . $row["id"]);
				ChronoSession::clear("chronoforms8_formvars_" . $row["id"]);
				ChronoSession::clear("chronoforms8_formelements_" . $row["id"]);
				ChronoSession::clear("sectoken". $row["id"]);
			}
		}
	}
}

// Chrono::pr($completed_elements);
$form_html_id = "chronoform-".$row["alias"];
if(!empty($completed_elements)){
	$eventsCode = [
		"var form = document.querySelector('#$form_html_id');"
	];

	$triggers_fns = [];
	$triggers_calls = [];

	$triggers_actions = [];
	foreach($completed_elements as $complete_element){
		if($active_section == "load" && ($complete_element["page_id"] != $active_page_id)){
			continue;
		}else{
			if ($next_page_id != $pages_ids[$active_page]) {
				if($active_section == "submit" && ($complete_element["page_id"] != $next_page_id)){
					continue;
				}
			}
		}
		if($active_section == "submit" && !$next_page_on){
			continue;
		}
		// $next_page = $active_page + 1;
		// if(isset($pages_ids[$next_page])){
		// 	$next_page_id = $pages_ids[$next_page];
		// 	if($active_section == "submit" && ($complete_element["page_id"] != $next_page_id)){
		// 		continue;
		// 	}
		// }

		if(isset($complete_element["fieldname"])){
			$fname = $complete_element["fieldname"];
			if($complete_element["name"] == "field_checkboxes"){
				$fname .= "[]";
			}
			$field_selector = "form.querySelector(\"[name='".$fname."']\")";
		}else{
			$field_selector = "form.querySelector('.".$complete_element["name"].$complete_element["id"]."')";
		}

		if(!empty($complete_element["triggers"])){
			$change_event = "change";
			if(in_array($complete_element["name"], ["field_text", "field_password", "field_textarea"])){
				$change_event = "input";
			}
			foreach($complete_element["triggers"] as $trigger){
				if(!empty($trigger["name"])){
					$tnames = (array)$trigger["name"];
					foreach($tnames as $tname){
						$tname = str_replace(" ", "_", $tname);

						if(!isset($triggers_fns[$tname])){
							$triggers_fns[$tname] = [];
							$triggers_calls[$tname] = [];
						}
	
						switch($trigger["condition"]){
							case "ready":
								$triggers_fns[$tname][] = "true";
								$triggers_calls[$tname][] = "TestEvent_$tname();";
								break;
							case "change":
								$triggers_fns[$tname][] = "true";
								$triggers_calls[$tname][] = "SetupEvent($field_selector, '$change_event', () => {
									TestEvent_$tname();
								});";
								break;
							case "empty":
								$triggers_fns[$tname][] = "isEmpty($field_selector)";
								$triggers_calls[$tname][] = "SetupEvent($field_selector, '$change_event', () => {
									TestEvent_$tname();
								});";
								break;
							case "not-empty":
								$triggers_fns[$tname][] = "!isEmpty($field_selector)";
								$triggers_calls[$tname][] = "SetupEvent($field_selector, '$change_event', () => {
									TestEvent_$tname();
								});";
								break;
							case "in":
								$triggers_fns[$tname][] = "HasValue($field_selector, ['".implode("','", $trigger["value"])."'])";
								$triggers_calls[$tname][] = "SetupEvent($field_selector, '$change_event', () => {
									TestEvent_$tname();
								});";
								break;
							case "not-in":
								$triggers_fns[$tname][] = "!HasValue($field_selector, ['".implode("','", $trigger["value"])."'])";
								$triggers_calls[$tname][] = "SetupEvent($field_selector, '$change_event', () => {
									TestEvent_$tname();
								});";
								break;
						}
					}
					
				}
			}
		}

		if(!empty($complete_element["listeners"])){
			foreach($complete_element["listeners"] as $listener){
				if(!empty($listener["trigger"])){
					$tname = $listener["trigger"];
					$tname = str_replace(" ", "_", $tname);

					if(!isset($triggers_actions[$tname])){
						$triggers_actions[$tname] = [];
					}
					if(!empty($listener["actions"])){
						$listener["actions"] = (array)$listener["actions"];

						if(in_array("show", $listener["actions"])){
							$triggers_actions[$tname][] = "ShowField($field_selector);";
						}
						if(in_array("hide", $listener["actions"])){
							$triggers_actions[$tname][] = "HideField($field_selector);";
						}
						if(in_array("enable", $listener["actions"])){
							$triggers_actions[$tname][] = "EnableField($field_selector);";
						}
						if(in_array("disable", $listener["actions"])){
							$triggers_actions[$tname][] = "DisableField($field_selector);";
						}
						if(in_array("disable_validation", $listener["actions"])){
							$triggers_actions[$tname][] = "DisableValidation($field_selector);";
						}
						if(in_array("enable_validation", $listener["actions"])){
							$triggers_actions[$tname][] = "EnableValidation($field_selector);";
						}
					}
				}
			}
		}
	}

	if(!empty($triggers_fns)){
		foreach($triggers_fns as $tname => $tconditions){
			if(!empty($triggers_actions[$tname])){
				$eventsCode[] = "function TestEvent_$tname(){
					let result = (".(implode(" && ", $tconditions)).");
					//console.log('test $tname = '+result);
					if(result){
						".implode("\n", $triggers_actions[$tname])."
					}
					return result;
				}";
			}
		}

		foreach($triggers_calls as $tname => $tcalls){
			foreach($tcalls as $tcall){
				if(!in_array($tcall, $eventsCode)){
					$eventsCode[] = $tcall;
				}
			}
		}
	}
}

$buffer = ob_get_clean();
if(!empty($eventsCode)){
	Chrono::loadAsset("/assets/events.js");
	echo "
	<script>
	document.addEventListener('DOMContentLoaded', function (event) {
		".implode("\n", $eventsCode)."
	})
	</script>
	";
	// Chrono::pr($eventsCode);
}
?>
<?php if (!empty($this->errors)) : ?>
	<div class="nui alert red">
		<ul>
			<?php foreach ($this->errors as $error) : ?>
				<li><?php echo $error; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
<?php
	if(!empty($row["params"]["navbar"]) && ($row["params"]["navbar"] == "1") && (count($pages_ids) > 1) && ($active_page < count($pages_ids) - 1)){
		$navigation = '<div class="nui flex equal items stackable">';

		foreach($pages_ids as $k => $pid){
			$page_title = !empty($pages[$k]["title"]) ? $pages[$k]["title"] : 'Page'.$k;
			$page_num = !empty($pages[$k]["icon"]) ? Chrono::ShowIcon($pages[$k]["icon"]) : Chrono::ShowIcon(($k + 1));

			$navigation .= '<div class="item nui flex spaced justify-center align-center">';

			if($next_page > $k){
				$navigation .= '<span class="nui label circular green" style="--pad:1em;">'.Chrono::ShowIcon("check").'</span>';
			}else{
				$navigation .= '<span class="nui label circular slate" style="--pad:1em;">'.$page_num.'</span>';
			}

			if($next_page_id == $pid){
				$navigation .= '<span class="nui bold">'.$page_title.'</span>';
			}else{
				if($next_page > $k){
					$navigation .= '<a class="nui bold underlined" href="'.Chrono::r(Chrono::addUrlParam($this->current_url, ["chronopage" => $k])).'">'.$page_title.'</a>';
				}else{
					$navigation .= '<span class="nui disabled">'.$page_title.'</span>';
				}
			}
			$navigation .= '</div>';
		}
		$navigation .= '</div>';
		$navigation .= '<div class="nui divider block"></div>';

		echo $navigation;
	}
?>
<?php
	$action_url = Chrono::addUrlParam($this->current_url, ["chronoform" => $row["alias"]]);
	$ajax = "";
	if (!empty($row["params"]["ajax"])){
		$ajax = "dynamic";
	}
?>
<form class="nui form <?php echo $ajax; ?>" <?php if (!empty($row["params"]["ajax"])) : ?>data-output="#chronoform-<?php echo $row["alias"]; ?>" <?php endif; ?> id="<?php echo $form_html_id; ?>" action="<?php echo $action_url; ?>" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<?php
	// $DisplayElements($elements_by_parent, $active_page_id, $active_section);

	// $next_page_on = false;
	// $next_page = $active_page;
	// if ($row["params"]["next_page"] == "1") {
	// 	if ($active_section == "submit") {
	// 		if ($active_page < count($pages_ids) - 1) {
	// 			$next_page = $active_page + 1;
	// 			$next_page_id = $pages_ids[$next_page];

	// 			$DisplayElements($elements_by_parent, $next_page_id, "load");
	// 			$next_page_on = true;

	// 			// echo '<input type="hidden" name="chronopage" value="' . $next_page . '" >';
	// 		}
	// 	}
	// }
	echo $buffer;
	if ($active_section == "load" || $next_page_on) {
		$token = uniqid("", true);
		ChronoSession::set("sectoken". $row["id"], $token);

		echo '<input type="hidden" name="chronoform" value="' . $row["alias"] . '" >';
		echo '<input type="hidden" name="chronopage" value="' . $next_page . '" >';
		echo '<input type="hidden" name="sectoken" value="' . $token . '" >';
	}

	if (!$this->isAdmin() && !$this->validated(true)) {
		echo '<a href="https://www.chronoengine.com/?ref=chronoforms8-form" target="_blank" class="chronocredits">This form was created by ChronoForms 8</a>';
	}
	?>

	<?php if ($row["params"]["debug"] == "1") : ?>
		<div class="nui segment bordered rounded block">
			<h3>Debug</h3>
			<h4>Data</h4>
			<?php Chrono::pr($this->data); ?>
			<h4>Files</h4>
			<?php Chrono::pr($_FILES); ?>
			<h4>Vars</h4>
			<?php Chrono::pr($this->vars); ?>
			<h4>Info</h4>
			<?php Chrono::pr($this->debug); ?>
		</div>
	<?php endif; ?>
</form>