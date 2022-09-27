<?/*
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<tr>
	<td align="right" width="40%"><span><?=GetMessage("DEALS_COMPANY_ACTIVITY_COMPANY_ID");?>:</span></td>
	<td width="60%">
		<input type="text" name="company_id" id="id_company_id" value="<?=htmlspecialchars($arCurrentValues['company_id']);?>" size="50">
		<input type="button" value="..." onclick="BPAShowSelector('id_company_id', 'string');">
	</td>
</tr>
*/?>

<?php

use Bitrix\Main;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

Main\UI\Extension::load('ui.entity-selector');
//\Bitrix\Main\Page\Asset::getInstance()->addJs(getLocalPath('activities/bitrix/crmaddproductrow/script.js'));
/** @var \Bitrix\Bizproc\Activity\PropertiesDialog $dialog */

foreach ($dialog->getMap() as $fieldId => $field): ?>
	<tr rel="label">
		<td align="right" width="40%"><?=htmlspecialcharsbx($field['Name'])?>:</td>
		<td width="60%">
			<?= $dialog->renderFieldControl($field, null, !empty($field['AllowSelection']), 0) ?>
		</td>
	</tr>
<?php endforeach;?>


