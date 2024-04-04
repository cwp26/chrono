<?php

/**
 * ChronoForms 8
 * Copyright (c) 2023 ChronoEngine.com, All rights reserved.
 * Author: (ChronoEngine.com Team)
 * license:     GNU General Public License version 2 or later; see LICENSE.txt
 * Visit http://www.ChronoEngine.com for regular updates and information.
 **/
defined('_JEXEC') or die('Restricted access');

$form =  CF8Model::instance()->Select(conditions: [['id', "=", ChronoApp::$instance->data("form_id")]], single: true);

$rows =  CF8LogModel::instance()->Select(conditions: [['form_id', "=", ChronoApp::$instance->data("form_id")]], order_by:true, order:"created asc", paging:true);
// Chrono::pr($rows);
$count = CF8LogModel::instance()->Select(conditions: [['form_id', "=", ChronoApp::$instance->data("form_id")]], count:true);
?>
<form class="nui form" action="<?php echo ChronoApp::$instance->current_url; ?>" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<?php
	new MenuBar(title: "Data Log", buttons: [
		new MenuButton(action: true, title: "Forms", icon: "arrow-left", color: "blue", url: "action=index"),
		new MenuButton(action: true, title: "Delete", icon: "trash", color: "red", url: "action=deletelog&form_id=".ChronoApp::$instance->data('form_id')),
	]);

	$columns = [
		new TableColumn(selector: true, name: "id"),
		new TableColumn(name: "created", title: "Saved On", expand: true, sortable:true, func: function ($row) {
			return '<a href="' . ChronoApp::$instance->extension_url . '&action=viewlog&id=' . $row["id"] . '">' . $row["created"] . '</a>';
		}),
		new TableColumn(name: "user_id", title: "User ID", sortable:true),
		new TableColumn(name: "ip", title: "IP", sortable:true),
	];

	$data = [];
	foreach ($form["elements"] as $element) {
		if ($element["type"] == "views") {
			if (str_starts_with($element["name"], "field_") && $element["name"] != "field_button") {
				if (!empty($element["fieldname"])) {
					if($element["name"] == "field_hidden"){
						$label = $element["fieldname"];
					}else{
						$label = $element["label"];
					}
					if(strlen($label) > 50){
						$label = substr($label, 0, 50)."...";
					}
					$columns[] = new TableColumn(name: "field", title: $label, func: function ($row) use ($element) {
						if(isset($row["data"][$element["id"]])){
							return $row["data"][$element["id"]];
						}
						return "";
					});
				}
			}
		}
	}

	new DataTable($rows, $columns, count:$count, wide:true);
	?>
</form>