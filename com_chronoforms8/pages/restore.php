<?php

/**
 * ChronoForms 8
 * Copyright (c) 2023 ChronoEngine.com, All rights reserved.
 * Author: (ChronoEngine.com Team)
 * license:     GNU General Public License version 2 or later; see LICENSE.txt
 * Visit http://www.ChronoEngine.com for regular updates and information.
 **/
defined('_JEXEC') or die('Restricted access');

if(!empty($_FILES)){
	$file = $_FILES['backup'];
	
	if(!empty($file['size'])){
		
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		
		if($ext != 'cf8bak' && $ext != "cf6bak"){
			ChronoSession::setFlash("error", "Invalid backup file extension.");
			ChronoApp::$instance->Redirect(ChronoApp::$instance->extension_url."&action=restore");
		}
		
		$target = $file['tmp_name'];
		
		if($ext == 'cf8bak'){
			$data = file_get_contents($target);
			
			$rows = json_decode($data, true);
			// Chrono::pr($rows);die();
			if(!empty($rows)){
				foreach($rows as $row){
					if(isset($row['id'])){
						$row['id'] = 0;
						$row['alias'] .= "-".gmdate("Ymd-His");
						$row['title'] .= "-".gmdate("Ymd-His");
						$row['published'] = 0;
						$result = CF8Model::instance()->Insert($row);

						if ($result === true) {
							ChronoSession::setFlash("success", sprintf(Chrono::l("%s Form restored successfully."), $row["title"]));
						}else{
							ChronoSession::setFlash("error", sprintf(Chrono::l("%s Error restoring form."), $row["title"]));
							break;
						}
					}
				}

				ChronoApp::$instance->redirect(ChronoApp::$instance->extension_url . "&action=index");
			}
		}else if($ext == 'cf6bak'){
			$data = file_get_contents($target);
			
			$rows = json_decode($data, true);
			// Chrono::pr($rows);die();
			if(!empty($rows)){
				foreach($rows as $row){
					if(isset($row['id'])){
						$row['id'] = 0;
						
						$result = CF8Model::instance()->Insert($row);

						if ($result === true) {
							ChronoSession::setFlash("success", sprintf(Chrono::l("%s Form restored successfully."), $row["title"]));
						}else{
							ChronoSession::setFlash("error", sprintf(Chrono::l("%s Error restoring form."), $row["title"]));
							break;
						}
					}
				}

				ChronoApp::$instance->redirect(ChronoApp::$instance->extension_url . "&action=index");
			}
		}
	}
}
?>
<form class="nui form" action="<?php echo ChronoApp::$instance->current_url; ?>" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<?php
	$buttons = [
		new MenuButton(name: "upload", title: "Upload", icon: "upload", color: "blue"),
		new MenuButton(name: "close", link: true, title: "Close", icon: "xmark", color: "red", url: "action=index"),
	];
	$title = "Restore Form(s)";
	new MenuBar(title: $title, buttons: $buttons);
	?>

	<div class="equal fields">
	<?php new FormField(name: "backup", label: "Backup File", type:"file", extensions:["cf8bak", "cf6bak"], code: 'data-validations=\'{"rules":[{"type":"required","prompt":"Please choose the backup file."}]}\''); ?>
	</div>
</form>