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
<?php foreach(["n" => []] + (!empty($this->data["elements"][$id]["listeners"]) ? $this->data["elements"][$id]["listeners"] : []) as $k => $item): ?>
	<div class="nui form clonable listeners-<?php echo $id; ?>" data-selector=".clonable.listeners-<?php echo $id; ?>" data-cloner=".listeners-<?php echo $id; ?>-cloner" data-key="<?php echo $k; ?>">
		<div class="equal fields">
			<?php
				new FormField(name: "elements[$id][listeners][".$k."][trigger]", label: "On Trigger of");
				new FormField(name: "elements[$id][listeners][".$k."][actions]", type:"select", multiple:true, label: "Action to do", options:[
					new Option(text:"Show", value:"show"),
					new Option(text:"Hide", value:"hide"),
					new Option(text:"Disable Validation", value:"disable_validation"),
					new Option(text:"Enable Validation", value:"enable_validation"),
					new Option(text:"Enable", value:"enable"),
					new Option(text:"Disable", value:"disable"),
				]);
			?>
			<button type="button" class="nui label red rounded link flex_center remove-clone self-center"><?php echo Chrono::ShowIcon("trash"); ?></button>
		</div>
		<div class="nui divider block"></div>
	</div>
<?php endforeach; ?>
<button type="button" class="nui button blue iconed listeners-<?php echo $id; ?>-cloner"><?php echo Chrono::ShowIcon("plus"); ?>Add Listener</button>