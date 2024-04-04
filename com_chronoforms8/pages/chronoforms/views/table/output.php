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
if(!empty($element["dbtable"])){
	$model = new ChronoModel();
	$model->Table = $element["dbtable"];


	$where = "";
	if(!empty($element["where"])){
		$where = CF8::parse($element["where"]);
	}

	$limit = 0;
	if(!empty($element["limit"])){
		$limit = CF8::parse($element["limit"]);
	}
	
	$rows = $model->Select(where:$where, paging:true, limit:$limit, order_by:true);
	$count = $model->Select(where:$where, count:true);

	if(is_array($rows) && !empty($element["columns"])){
		$columns = [];
		foreach($element["columns"] as $column){
			$expand = ($element["expand"] == $column["path"]);
			$sort = in_array($column["path"], isset($element["sortable"]) ? $element["sortable"] : []);
			$func = null;
			$columns[] = new TableColumn(name:$column["path"], title:$column["header"], expand:$expand, sortable:$sort, func:$func, class:$column["class"]);
		}

		new DataTable($rows, $columns, count:$count, limit:$limit);
		// new DataTable($rows, [
		// 	new TableColumn(selector:true, name:"id"),
		// 	new TableColumn(name:"title", title:"Title", expand:true, sortable:true, func:function($row){
		// 		return '<a href="'.ChronoApp::$instance->extension_url.'&action=edit&id='.$row["id"].'">'.$row["title"].'</a>'.' ('.$row["alias"].')';
		// 	}),
		// 	new TableColumn(name:"published", title:Chrono::l("Published"), func:function($row){
		// 		if($row["published"] == "1"){
		// 			return '<a href="'.ChronoApp::$instance->extension_url.'&action=toggle&id='.$row["id"].'&field=published&value=0">'.Chrono::ShowIcon("check nui green").'</a>';
		// 		}else{
		// 			return '<a href="'.ChronoApp::$instance->extension_url.'&action=toggle&id='.$row["id"].'&field=published&value=1">'.Chrono::ShowIcon("xmark nui red").'</a>';
		// 		}
		// 	}),
		// 	new TableColumn(name:"view", title:"View Form", func:function($row){
		// 		return '<a href="'.ChronoApp::$instance->extension_url.'&action=view&id='.$row["id"].'">View</a>';
		// 	}),
		// 	new TableColumn(name:"log", title:"Data Log", func:function($row){
		// 		if(!empty($row["params"]["log_data"])){
		// 			return '<a href="'.ChronoApp::$instance->extension_url.'&action=datalog&form_id='.$row["id"].'">Data</a>';
		// 		}
		// 	}),
		// 	new TableColumn(name:"id", title:"ID"),
		// ]);
	}
}