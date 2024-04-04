<?php
/**
* ChronoForms 8
* Copyright (c) 2023 ChronoEngine.com, All rights reserved.
* Author: (ChronoEngine.com Team)
* license:     GNU General Public License version 2 or later; see LICENSE.txt
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
defined('_JEXEC') or die('Restricted access');

$sort = Chrono::getVal($this->settings, "index_sort", "id asc");
$rows = CF8Model::instance()->Select(order_by:true, order:$sort);
?>
<form class="nui form" action="<?php echo ChronoApp::$instance->current_url; ?>" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<?php
	new MenuBar(title: "Forms", buttons: [
		new MenuButton(link: true, url: "action=edit", title: "New", color:"blue", icon:"plus"),
		new MenuButton(action:true, title: "Copy", icon:"copy", color:"grey", url:"action=copy"),
		new MenuButton(action:true, title: "Delete", icon:"trash", color:"red", url:"action=delete"),
		new MenuButton(action:true, title: "Backup", icon:"download", color:"grey", url:"action=backup&output=component"),
		new MenuButton(link: true, url: "action=restore", title: "Restore", color:"grey", icon:"upload"),
		new MenuButton(link: true, url: "action=extend", title: "Extend", color:"grey", icon:"puzzle-piece"),
		new MenuButton(link: true, url: "action=settings", title: "Settings", color:"slate", icon:"gear"),
	]);

	new DataTable($rows, [
		new TableColumn(selector:true, name:"id"),
		new TableColumn(name:"title", title:"Title", expand:true, sortable:true, func:function($row){
			return '<a href="'.ChronoApp::$instance->extension_url.'&action=edit&id='.$row["id"].'">'.$row["title"].'</a>'.' ('.$row["alias"].')';
		}),
		new TableColumn(name:"published", title:Chrono::l("Published"), func:function($row){
			if($row["published"] == "1"){
				return '<a href="'.ChronoApp::$instance->extension_url.'&action=toggle&id='.$row["id"].'&field=published&value=0">'.Chrono::ShowIcon("check nui green").'</a>';
			}else{
				return '<a href="'.ChronoApp::$instance->extension_url.'&action=toggle&id='.$row["id"].'&field=published&value=1">'.Chrono::ShowIcon("xmark nui red").'</a>';
			}
		}),
		new TableColumn(name:"view", title:"View Form", func:function($row){
			return '<a href="'.ChronoApp::$instance->extension_url.'&action=view&id='.$row["id"].'">View</a>';
		}),
		new TableColumn(name:"log", title:"Data Log", func:function($row){
			if(!empty($row["params"]["log_data"])){
				return '<a href="'.ChronoApp::$instance->extension_url.'&action=datalog&form_id='.$row["id"].'">Data</a>';
			}
		}),
		new TableColumn(name:"id", title:"ID"),
	]);
	?>
</form>