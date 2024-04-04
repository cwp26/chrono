<?php
/**
* ChronoForms 8
* Copyright (c) 2023 ChronoEngine.com, All rights reserved.
* Author: (ChronoEngine.com Team)
* license:     GNU General Public License version 2 or later; see LICENSE.txt
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
defined('_JEXEC') or die('Restricted access');

ChronoPage::SaveSettings();

ChronoPage::ReadSettings();
?>
<form class="nui form" action="<?php echo $this->current_url; ?>" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<?php
		ChronoPage::SettingsHTML();
	?>

	<fieldset class="nui segment white bordered rounded">
		<legend class="nui bold large label grey rounded">ReCaptcha</legend>
		<div class="equal fields">
			<?php new FormField(name: "settings[recaptcha][sitekey]", label: "ReCaptcha Site Key", hint:"Global reCaptcha site key to use when the site key is empty in your reCaptcha view"); ?>
			<?php new FormField(name: "settings[recaptcha][secretkey]", label: "ReCaptcha Secret Key", hint:"Global reCaptcha secret key to use when the secret key is empty in your reCaptcha view"); ?>
		</div>
	</fieldset>

	<fieldset class="nui segment white bordered rounded">
		<legend class="nui bold large label grey rounded">Admin Settings</legend>
		<div class="equal fields">
			<?php new FormField(name: "settings[index_sort]", type:"select", label: "Sort forms by", options:[
				new Option(text:"ID ASC", value:"id asc"),
				new Option(text:"ID DESC", value:"id desc"),
				new Option(text:"Title ASC", value:"title asc"),
				new Option(text:"Title DESC", value:"title desc"),
			], hint:"The field to use for sorting forms."); ?>
		</div>
	</fieldset>

	<fieldset class="nui segment white bordered rounded">
		<legend class="nui bold large label grey rounded">Validation Key</legend>
		<div class="equal fields">
			<?php new FormField(name: "settings[vkey]", label: "Your validation key", hint:"The validation key is stored for convenience and can be cleared from the system if necessary."); ?>
		</div>
	</fieldset>

</form>