<?php
/**
* ChronoForms 8
* Copyright (c) 2023 ChronoEngine.com, All rights reserved.
* Author: (ChronoEngine.com Team)
* license:     GNU General Public License version 2 or later; see LICENSE.txt
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
defined('_JEXEC') or die('Restricted access');

require_once(dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR."libraries".DIRECTORY_SEPARATOR."chrono_lib".DIRECTORY_SEPARATOR."chrono.php");

class CF8Model extends ChronoModel
{
	public $Table = "#__chronoforms8";
	public $PKey = "id";
	public $Data = [];
	public $JSON = ["elements", "params"];
	public $Title = "Form";

	public $Fields = [
		"id",
		"title",
		"alias",
		"published",
		"elements",
		"params",
	];
}

class CF8LogModel extends ChronoModel
{
	public $Table = "#__chronoforms8_datalog";
	public $PKey = "id";
	public $Data = [];
	public $JSON = ["data"];

	public $id = 0;
	public $form_id = 0;
	public $user_id = 0;
	public $ip = "";
	public $created = "";
	public $data = "";
}

class CF8{
	public static $locales = [];

	public static function multiline($string){
		$results = [];
		$lines = trim($string);
		$lines = explode("\n", $lines);
		foreach($lines as $line){
			$line = trim($line);
			if(strlen($line) == 0){
				continue;
			}
			$pts = explode("=", $line, 2);
			$nline = new stdClass();
			$nline->name = $pts[0];
			$nline->value = null;
			if(count($pts) > 1){
				$nline->value = $pts[1];
			}
			$results[] = $nline;
		}
		return $results;
	}

	public static function getlabel($element){
		if(!empty($element["label"])){
			return $element["label"];
		}else if(!empty($element["placeholder"])){
			return $element["placeholder"]["text"];
		}else{
			return $element["fieldname"];
		}
	}

	public static function getname($element){
		if(!empty($element["settings"]["name"])){
			return $element["settings"]["name"];
		}else{
			return $element["name"].$element["id"];
		}
	}

	public static function parse($code, $parse_data = []){
		$pattern = '/{([a-z_]+)([\.][^:]+)?:([^}]*?)}/i';
		$output = trim($code);
		preg_match_all($pattern, $output, $matches);

		if(!empty($matches[0])){
			
			$tags = $matches[0];
			
			foreach($tags as $k => $tag){
				$result = null;

				$type_not_found = false;
				$type = $matches[1][$k];
				$method = ltrim($matches[2][$k], '/.');
				$name = trim($matches[3][$k]);
				$params = [];
				if(str_contains($name, " ")){
					$pts = explode(" ", $name, 2);
					$name = $pts[0];
					$params = explode(" ", $pts[1]);
				}
				
				if($type == 'var'){
					$result = ChronoApp::$instance->get($name, !empty($params) ? $params[0] : null);

				}else if($type == 'const'){
					$result = $name;
				}else if($type == 'data'){
					if(empty($name)){
						$result = ChronoApp::$instance->data;
					}else{
						$result = ChronoApp::$instance->data($name, !empty($params) ? $params[0] : null);
					}
				}else if($type == 'session'){
					$result = ChronoSession::get($name, !empty($params) ? $params[0] : null);

				}else if($type == 'user'){
					$result = ChronoApp::$instance->user()->$name;

				}else if($type == 'date'){
					if(strlen($name) == 0){
						$name = "Y-m-d H:i:s";
					}
					$result = gmdate($name);

				}else if($type == 'icon'){
					$result = Chrono::ShowIcon($name);

				}else if($type == 'l'){
					$result = isset(self::$locales[$name]) ? self::$locales[$name] : "";

				}else if($type == '_'){
					$result = Chrono::l($name);

				}else if($type == 'url'){
					if(empty($name)){
						$result = Chrono::r(ChronoApp::$instance->current_url);
					}

				}else if($type == 'path'){
					if($name == "front"){
						$result = trim(ChronoApp::$instance->front_path, "/\\");
					}
					
				}else if($type == 'str'){

					if($name == 'uuid'){
						$result = Chrono::uuid();

					}else if($name == 'rand'){
						if(!empty($name) AND is_numeric($name)){
							$first = str_repeat('%04X', ceil((float)$name/4));
							$result = substr(sprintf($first, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)), 0, $name);
						}else{
							$result = mt_rand();
						}
						
					}else if($name == 'ip'){
						$result = $_SERVER['REMOTE_ADDR'];
					}
					
				}else{
					if(!empty($parse_data) && isset($parse_data[$type])){
						if(isset($parse_data[$type][$name])){
							$result = $parse_data[$type][$name];
						}
					}else{
						$type_not_found = true;
					}
				}

				if(!is_null($result)){
					if(!empty($method)){
						$model = new ChronoModel();
						switch($method){
							case "quote":
								$result = $model->quote($result);
								break;
							case "quoteName":
								$result = $model->quoteName($result);
								break;
							case "encode":
								$result = json_encode($result);
								break;
							case "decode":
								$result = json_decode($result, true);
								break;
						}
					}
				}

				if(count($tags) == 1 && strlen($tag) == strlen($output)){
					if(is_array($result)){
						return $result;
					}
				}else{
					if(is_array($result)){
						$result = json_encode($result);
					}
				}

				if($type_not_found){
					// $output = substr_replace($output, $result, strpos($output, $tag), strlen($tag));
				}else{
					if(is_null($result)){
						$result = "";
					}
					$output = substr_replace($output, $result, strpos($output, $tag), strlen($tag));
				}
			}
		}


		return $output;
	}
}
