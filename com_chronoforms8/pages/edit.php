<?php
/**
* ChronoForms 8
* Copyright (c) 2023 ChronoEngine.com, All rights reserved.
* Author: (ChronoEngine.com Team)
* license:     GNU General Public License version 2 or later; see LICENSE.txt
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
defined('_JEXEC') or die('Restricted access');

ChronoPage::Save(CF8Model::instance());

Chrono::loadAsset("/assets/form_builder.min.css");
Chrono::loadAsset("/assets/form_builder.min.js");
// Chrono::loadAsset("/assets/nui.tinymce.min.js");
Chrono::loadAsset("/assets/ace.min.js");

$row =  CF8Model::instance()->Select(conditions:[['id', "=", ChronoApp::$instance->data("id")]], single: true);
$elements = [1 => ["id" => 1, "type" => "page"]];
if (!is_null($row) && !empty($row["elements"])) {
	$elements = $row["elements"];
}
ChronoApp::$instance->MergeData($row);
// Chrono::pr($row);

$views = [];
$views_path = __DIR__ . '/chronoforms/views/';
$scan = scandir($views_path);

foreach ($scan as $folder) {
	if (is_dir($views_path . $folder) && !str_contains($folder, ".")) {
		$path = $views_path . $folder . "/info.json";
		if(!file_exists($path)){
			ChronoSession::setFlash("error", "View json file not found:".$folder);
			continue;
		}
		$myfile = fopen($path, "r") or die("Unable to open file $path");
		$data = fread($myfile, filesize($path));
		fclose($myfile);
		$view = json_decode($data);
		$view->name = $folder;
		$views[$view->group][] = $view;
	}
}

$actions = [];
$actions_path = __DIR__ . '/chronoforms/actions/';
$scan = scandir($actions_path);

foreach ($scan as $folder) {
	if (is_dir($actions_path . $folder) && !str_contains($folder, ".")) {
		$path = $actions_path . $folder . "/info.json";
		if(!file_exists($path)){
			ChronoSession::setFlash("error", "Action json file not found:".$folder);
			continue;
		}
		$myfile = fopen($path, "r") or die("Unable to open file $path");
		$data = fread($myfile, filesize($path));
		fclose($myfile);
		$action = json_decode($data);
		if (!empty($action->hidden)) {
			continue;
		}
		$action->name = $folder;
		$actions[$action->group][] = $action;
	}
}

$loadElements = function ($elements, $pid, $section) use(&$loadElements) {
	foreach ($elements as $element) {
		if ($element["type"] != "page") {
			if ($element["parent"] == $pid && $element["section"] == $section) {
				$element["pid"] = $pid;
				ChronoApp::$instance->MergeData($element);
				require(__DIR__ . "/load_element.php");
			}
		}
	}
};

// Chrono::addHeaderTag('<script src="'.$this->root_url.'media/vendor/tinymce/tinymce.min.js?nocache'.'"></script>');
Chrono::loadEditor();
?>
<form class="nui form" action="<?php echo ChronoApp::$instance->current_url; ?>" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<?php
	$buttons = [
		new MenuButton(name: "save", title: "Save", icon: "floppy-disk", color: "blue"),
		new MenuButton(name: "close", link: true, title: "Close", icon: "xmark", color: "red", url: "action=index"),
		new MenuButton(name: "help", link: true, title: "Help", icon: "question", color: "slate", params:"target='_blank'", url: "https://www.chronoengine.com/faqs/chronoforms/chronoforms8/"),
	];
	$title = "New Form";
	if(!empty($row["id"])){
		$title = "Edit Form";
		array_push($buttons, new MenuButton(name: "preview", link: true, title: "Preview", icon: "display", color: "colored slate", params:"target='_blank'", url: "action=view&id=".$row["id"]));
	}
	new MenuBar(title: $title, buttons: $buttons);

	new FormField(name: "id", label: "ID", type: "hidden");
	?>

	<div class="equal fields">
	<?php new FormField(name: "title", label: "Title", value:"Form @ ".gmdate("d-m-Y H:i:s"), code: 'data-validations=\'{"rules":[{"type":"required","prompt":"This field is required."}]}\''); ?>
	<?php new FormField(name: "alias", label: "Alias", value:"form-".gmdate("d-m-Y-H-i-s"), hint: "This alias is used for calling form in shortcodes and menuitems, example: my-form", code: 'data-validations=\'{"rules":[{"type":"required","prompt":"This field is required."},{"type":"regex","regex":"/^[0-9A-Za-z-]+$/","prompt":"Only alphabetical characters, numbers and - are allowed."}]}\''); ?>
	</div>
	
	<div class="equal fields">
		<?php
		new FormField(name: "published", label: "Published", type: "select", options: [
			new Option(text: "Yes", value: "1"),
			new Option(text: "No", value: "0"),
		]);
		new FormField(name: "params[debug]", label: "Debug", type: "select", hint:"Show form debug data", options: [
			new Option(text: "No", value: "0"),
			new Option(text: "Yes", value: "1"),
		]);
		?>
	</div>
	<div>
		<div class="nui flex tabular menu top attached">
			<div class="active item" data-tab="designer">Designer</div>
			<div class="item" data-tab="settings">Settings</div>
		</div>
		<div class="nui segment flex white spaced bordered bottom attached tab" data-tab="designer">
			<div style="width:75%">
				<div class="nui flex vertical spaced form_designer" data-url="<?php echo ChronoApp::$instance->extension_url; ?>&output=component">
					<?php foreach ($elements as $element) : ?>
						<?php if ($element["type"] == "page") : ?>
							<?php $pid = $element["id"]; ?>

							<div class="nui block page_box">
								<input type="hidden" name="elements[<?php echo $pid; ?>][id]" value="<?php echo $pid; ?>">
								<input type="hidden" name="elements[<?php echo $pid; ?>][type]" value="page">

								<div class="nui flex tabular menu top attached" data-parent='[data-tab="designer"]'>
									<div class="item nui header">Page<span class="nui label rounded grey page_counter"><?php echo $pid; ?></span></div>
									<div class="active item" data-tab="load">Load</div>
									<div class="item" data-tab="submit">Submit</div>
									<div class="item" data-tab="page-options">Options</div>
									<div class="item right nui header">
										<div class="nui label rounded link drag_page"><?php echo Chrono::ShowIcon("sort"); ?></div>
										<div class="nui label rounded link remove_page"><?php echo Chrono::ShowIcon("xmark"); ?></div>
									</div>
								</div>
								<div class="nui segment spaced thick bordered rounded bottom attached tab flex vertical droppable sortable form_page" data-pid="<?php echo $pid; ?>" data-section="load" data-tab="load" data-hint="Drag Views or Actions from the right side."><?php $loadElements($elements, $pid, "load"); ?></div>
								<div class="nui segment spaced thick bordered rounded bottom attached tab flex vertical droppable sortable form_page" data-pid="<?php echo $pid; ?>" data-section="submit" data-tab="submit" data-hint="Drag Views or Actions from the right side."><?php $loadElements($elements, $pid, "submit"); ?></div>
								<div class="nui segment form white bordered rounded bottom attached tab" data-tab="page-options">
									<?php new FormField(name: "elements[".$pid."][title]", label: "Title", value: "Page".$pid, hint:"Page title is used in Navigation bar in Multi Page forms"); ?>
									<?php new FormField(name: "elements[".$pid."][icon]", label: "Icon", hint:"Page icon is used in Navigation bar in Multi Page forms"); ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>

				<div class="nui block"></div>

				<button type="button" class="nui button blue block full width iconed add_page"><?php echo Chrono::ShowIcon("plus"); ?>New Page</button>
			</div>

			<div class="nui segment flex vertical bordered rounded white tools_box">
				<?php if (strlen(ChronoApp::$instance->Data("id")) > 0) : ?>
					<h4><?php echo $row["title"]; ?></h4>
					<button type="button" id="quick_save" name="apply" class="nui button blue iconed dynamic"><?php echo Chrono::ShowIcon("check"); ?>Quick Save</button>
				<?php endif; ?>

				<div class="nui block"></div>

				<div class="nui flex tabular menu top attached slate" data-parent='[data-tab="designer"]'>
					<div class="active item" data-tab="views" data-demo="field">Views</div>
					<div class="item" data-tab="actions" data-demo="message">Actions</div>
				</div>
				<div class="nui flex vertical bordered rounded slate bottom attached tab" data-tab="views">
					<div class="nui flex vertical p1 divided rounded accordion">
						<?php foreach(["Fields", "Security", "Areas", "Content"] as $group): ?>
							<div class="item <?php if($group == "Fields"){ echo "active"; } ?> nui pv1">
								<div class="title nui bold">
									<i class="dropdown icon"></i>
									<?php echo $group; ?>
								</div>
								<div class="content nui flex vertical spaced p0">
									<?php foreach ($views[$group] as $view) : ?>
										<div class="nui label rounded colored teal inverted link draggable original_item" data-type="views" data-name="<?php echo $view->name; ?>"><?php echo Chrono::ShowIcon($view->icon); ?><?php echo $view->title; ?><?php echo (!empty($view->premium) ? Chrono::ShowIcon("dollar-sign nui black") : "") ?></div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="nui flex vertical bordered rounded slate bottom attached tab" data-tab="actions">
					<div class="nui flex vertical p1 divided rounded accordion">
						<?php foreach(["Basics", "Database", "Advanced", "Joomla"] as $group): ?>
							<div class="item <?php if($group == "Basics"){ echo "active"; } ?> nui pv1">
								<div class="title nui bold">
									<i class="dropdown icon"></i>
									<?php echo $group; ?>
								</div>
								<div class="content nui flex vertical spaced p0">
									<?php foreach ($actions[$group] as $action) : ?>
										<div class="nui label rounded colored purple inverted link draggable original_item" data-type="actions" data-name="<?php echo $action->name; ?>"><?php echo Chrono::ShowIcon($action->icon); ?><?php echo $action->title; ?><?php echo (!empty($action->premium) ? Chrono::ShowIcon("dollar-sign nui black") : "") ?></div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

			</div>

		</div>
		<div class="nui segment form white bordered rounded bottom attached tab" data-tab="settings">
			<fieldset class="nui segment bordered rounded">
				<legend class="nui bold large label grey rounded">Multi Page</legend>

				<div class="equal fields">
					<?php
						new FormField(name: "params[next_page]", label: "Next Page", type: "select", hint:"How to decide next page ?", options: [
							new Option(text: "Auto", value: "1"),
							new Option(text: "Manual", value: "0"),
						]);
						new FormField(name: "params[page_jump]", label: "Page Hopping", type: "select", hint:"Users can go back or forward to previous completed pages", options: [
							new Option(text: "Yes", value: "1"),
							new Option(text: "No", value: "0"),
						]);
					?>
				</div>
				<div class="equal fields">
					<?php
						new FormField(name: "params[navbar]", label: "Navigation Bar", type: "select", hint:"Enable the navigation bar for Multi page forms ?", options: [
							new Option(text: "Yes", value: "1"),
							new Option(text: "No", value: "0"),
						]);
					?>
				</div>
			</fieldset>
			
			<fieldset class="nui segment bordered rounded">
				<legend class="nui bold large label grey rounded">Data Log</legend>
				<div class="equal fields">
				<?php
					new FormField(name: "params[log_data]", label: "Log Data", type: "select", hint:"Log form data to the log table ?", options: [
						new Option(text: "Yes", value: "1"),
						new Option(text: "No", value: "0"),
					]);
				?>
				</div>
			</fieldset>

			<fieldset class="nui segment bordered rounded">
				<legend class="nui bold large label grey rounded">Processing</legend>
				<div class="equal fields">
				<?php
					new FormField(name: "params[ajax]", label: "AJAX Form", type: "select", hint:"Use AJAX to submit the form, parent page will not reload.", options: [
						new Option(text: "No", value: ""),
						new Option(text: "Yes", value: "1"),
					]);
				?>
				</div>
			</fieldset>
			
			<fieldset class="nui segment bordered rounded">
				<legend class="nui bold large label grey rounded">ViewLevels</legend>
				<div class="equal fields">
				<?php
					$levels = $this->get("app_viewlevels", []);

					$options = [new Option(text: "?", value: "")];
					foreach($levels as $level){
						$options[] = new Option(text: $level["title"], value: $level["id"]);
					}
					new FormField(name: "params[acl]", label: "Viewlevel", type: "select", hint:"Which user levels can access any of the form pages", options: $options);
					new FormField(name: "params[acl_error]", label: "Access error", value:"You can not access this form.", hint:"Error shown when the user does not have the access level selected.");
				?>
				</div>
			</fieldset>

			<fieldset class="nui segment bordered rounded">
				<legend class="nui bold large label grey rounded">Locales</legend>
				
				<?php foreach(["n" => ""] + (!empty($row["params"]["locales"]) ? $row["params"]["locales"]["lang"] : []) as $k => $lang): ?>
					<div class="nui form clonable locales" data-selector=".clonable.locales" data-cloner=".locales-cloner" data-key="<?php echo $k; ?>">
						<?php
							new FormField(name: "params[locales][lang][".$k."]", label: "Language Code", value:"", hint:"The language code in your Joomla site.");
							new FormField(name: "params[locales][strings][".$k."]", type:"textarea", rows:10, label: "Language Strings", value:"", hint:"The language translation strings in this format:
								LANGUAGE_STRING=String translated in this Language
								Call language strings in your form using {l:LANGUAGE_STRING}");
						?>
						<button type="button" class="nui button red iconed block remove-clone"><?php echo Chrono::ShowIcon("xmark"); ?>Remove Language</button>
						<div class="nui divider block"></div>
					</div>
				<?php endforeach; ?>
				<button type="button" class="nui button blue iconed locales-cloner"><?php echo Chrono::ShowIcon("plus"); ?>Add Language</button>
			</fieldset>
		</div>
	</div>

</form>