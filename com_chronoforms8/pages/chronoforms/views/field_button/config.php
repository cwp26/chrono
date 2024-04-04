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
	<?php new FormField(name: "elements[$id][label]", label: "Label", value: "Button $id"); ?>
	<?php new FormField(name: "elements[$id][fieldname]", label: "Field Name", value: "button_$id"); ?>
	<?php
		new FormField(name: "elements[$id][btype]", label: "Button Type", type: "select", options: [
			new Option(text: "Submit", value: "submit"),
			new Option(text: "Reset", value: "reset"),
			new Option(text: "Button", value: "button"),
			new Option(text: "Previous Page Link", value: "lastpage"),
		]);
	?>
</div>
<?php
$behaviors = ["hint", "icon", "color", "field_button.position", "field_class", "html_attributes", "events_triggers", "events_listeners"];
$listBehaviors($id, $behaviors);
?>