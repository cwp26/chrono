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
	<?php new FormField(name: "elements[$id][label]", label: "Label", value: "Select $id"); ?>
	<?php new FormField(name: "elements[$id][fieldname]", label: "Field Name", value: "select_$id"); ?>
</div>
<?php new FormField(name: "elements[$id][options]", type: "textarea", label: "Options", value: "=Make a choice
blue=Blue Pill
Red Pill", rows: 5, hint: "Multiline list of options, may use the format value=Text"); ?>
<?php
$behaviors = ["validation_required", "validation_function", "hint", "field_class", "multi_selection", "html_attributes", "dynamic_options", "events_triggers", "events_listeners", 
"field_select.countries", 
"field_select.searchable", 
"field_select.additions", 
"selected_values"];
$listBehaviors($id, $behaviors);
?>