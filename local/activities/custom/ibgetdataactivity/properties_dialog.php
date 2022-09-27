<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (CModule::IncludeModule("iblock")){
	$res = CIBlock::GetList(
	 	Array(), 
	   	Array(
		  'TYPE' => array(), 
		  'SITE_ID' => 's1', 
		  'ACTIVE' => 'Y', 		  
	   ), true
	);
	$i = 0;
	$typeBlocks = array();
	while($ar_res = $res->Fetch())
	{
	   if($ar_res['ELEMENT_CNT'] > 0){
	   	$typeBlocks[$ar_res['ID']] = $ar_res['NAME'];
	   	$currentIblock = $ar_res['ID'];
	   	$i++;
	   }
	}

}

$arOrder = array('ID', 'SORT', 'NAME', 'ACTIVE_FROM', 'ACTIVE_TO', 'CODE');
$currentIblock = !empty($currentValues['IblockId']) ? $currentValues['IblockId'] : '';
$currentIblockOrder = !empty($currentValues['IblockOrder']) ? $currentValues['IblockOrder'] : '';

?>

<tbody id="iblock_change">
<tr>
	<td align="right" width="40%">
		<span style="font-weight: bold"><?=GetMessage("IB_ACTIVITY_GET_DATA_TYPE")?></span>
	</td>
	<td width="60%">
		<select name="IblockId" onchange="BPCGDEA_getIblockList(this.value)">
			<?foreach($typeBlocks as $iblockId => $iblockName):?>
				<option value="<?=htmlspecialcharsbx($iblockId)?>"
					<?=($currentIblock == $iblockId) ? 'selected' : ''?>>
					<?=htmlspecialcharsbx($iblockName)?>
				</option>
			<?endforeach;?>
		</select>
	</td>
</tr>
</tbody>

<tbody id="order_change">
<tr>
	<td align="right" width="40%">
		<span style="font-weight: bold"><?=GetMessage("IB_ACTIVITY_GET_DATA_ORDER")?></span>
	</td>
	<td width="60%">
		<select name="IblockOrder">
			<?foreach($arOrder as $item):?>
				<option value="<?=$item?>" <?=($currentIblockOrder == $item) ? 'selected' : ''?>>
					<?=$item?>
				</option>
			<?endforeach;?>
		</select>
	</td>
</tr>
</tbody>


<tbody id="fields_change"><?=$renderEntityFields;?></tbody>

<script>
	var BPCGDEA_getIblockList = function(iblock)
	{
		if(!iblock)
			return;

		var container = BX('fields_change');
		container.innerHTML = '';

		BX.ajax.post(
			'/local/activities/custom/ibgetdataactivity/ajax_ibgetdata.php',
			{
				'site_id': BX.message('SITE_ID'),
				'sessid' : BX.bitrix_sessid(),
				'document_type' : <?=Cutil::PhpToJSObject($documentType)?>,
				'activity': 'IbGetDataActivity',
				'IblockId': iblock,
				'content_type': 'html',
				'customer_action' : 'getIblockList'
			},
			function(response) {
				if(response)
				{
					container.innerHTML = response;
				}
			}
		);
	};
</script>