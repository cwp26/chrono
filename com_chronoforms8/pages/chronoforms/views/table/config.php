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
<div class="equal fields">
	<?php new FormField(name: "elements[$id][dbtable]", type:"select", label: "Table name", hint: "The database table to read the data from.", options:['' => ""] + CF8Model::instance()->Tables()); ?>
</div>
<?php foreach(["n" => []] + (!empty($this->data["elements"][$id]["columns"]) ? $this->data["elements"][$id]["columns"] : []) as $k => $item): ?>
	<div class="nui form clonable columns-<?php echo $id; ?>" data-selector=".clonable.columns-<?php echo $id; ?>" data-cloner=".columns-<?php echo $id; ?>-cloner" data-key="<?php echo $k; ?>">
		<div class="equal fields">
			<?php
				new FormField(name: "elements[$id][columns][".$k."][path]", label: "Data Path");
				new FormField(name: "elements[$id][columns][".$k."][header]", label: "Header Text");
				new FormField(name: "elements[$id][columns][".$k."][class]", label: "Class");
			?>
			<button type="button" class="nui label red rounded link self-center remove-clone"><?php echo Chrono::ShowIcon("trash"); ?></button>
		</div>
	</div>
<?php endforeach; ?>
<button type="button" class="nui button blue iconed columns-<?php echo $id; ?>-cloner"><?php echo Chrono::ShowIcon("plus"); ?>Add Table Column</button>
<?php
$behaviors = ["table.expand", "table.sortable", "table.limit", "where_statement"];
$listBehaviors($id, $behaviors);
?>