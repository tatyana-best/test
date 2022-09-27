<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Задача 1");
//\Bitrix\Main\Loader::includeModule('iblock');
?>

<?if (CModule::IncludeModule("crm")):

	function getLeads(){
		$status = array(
			"0" => "NEW",
			"1" => "IN_PROCESS",
		);

		$arFilter = array(
			"STATUS_ID" => $status,
		);
		$arOrder = array(); 
		$getLeads = CCrmLead::GetListEx($arOrder, $arFilter);
		$leads = array();
		while($arLead = $getLeads->fetch()){
			$leads[$arLead["ID"]] = $arLead;
		}
		return $leads;

	}

	function getDeals(){
		$arFilter = array(
			'CHECK_PERMISSIONS' => 'N'
		);
		$arOrder = array(); 
		$getDeals = CCrmDeal::GetListEx($arOrder, $arFilter);
		$deals = array();
		while($arDeal = $getDeals->fetch()){
			$deals[] = $arDeal;
		}
		return $deals;
	}

	$type_id = array(
		0 => 'PHONE',
		1 => 'EMAIL'
	);
	$dbResMultiFields = CCrmFieldMulti::GetList(array(),array('ENTITY_ID'=>'COMPANY','TYPE_ID'=>$type_id,'ELEMENT_ID'=>4));
	$companies = [];
	while($arMultiFields = $dbResMultiFields->Fetch()){
		$companies[$arMultiFields['TYPE_ID']] = $arMultiFields;
	}
	$company_phone = $companies['PHONE']['VALUE'];
	$company_email = $companies['EMAIL']['VALUE'];

//echo "<pre>".print_r(getDeals(),true)."</pre>";
/*$product_ids = array(23, 24, 25);
	$result = \Bitrix\Catalog\ProductTable::getList(array(
		'filter' => array('ID' => $product_ids),
		'select' => array('ID','QUANTITY','NAME'=>'IBLOCK_ELEMENT.NAME','CODE'=>'IBLOCK_ELEMENT.CODE'),	
	));	
	
	while($product=$result->fetch())
	{	
		//print_r($product);
		echo "<pre>".print_r($product,true)."</pre>";	
	}

	\Bitrix\Main\Loader::includeModule('Catalog');
	\Bitrix\Main\Loader::includeModule('crm');

	$arr=CCatalogProduct::GetByIDEx(24);
	if($tax=CCatalogVat::GetByID(2)->fetch()) 
	{ \CCrmProductRow::Add(['OWNER_TYPE' => 'D', 'OWNER_ID' => 64, 'PRODUCT_ID' => 24, 'DISCOUNT_RATE' => '', 'DISCOUNT_SUM' => '', 'TAX_RATE' => $tax['RATE'], 'TAX_INCLUDED' => 'N', 'QUANTITY' => 1, 'PRICE' => '', 'PRICE_NETTO' => $arr['PRICES'][1]['PRICE'], 'PRICE_BRUTTO' => '', 'PRICE_EXCLUSIVE' => $arr['PRICES'][1]['PRICE'], 'MEASURE_CODE' => 796, 'CURRENCY_ID' => RUB]);
	}
*/
/*foreach(getLeads() as $lead_id => $lead){
		$entity = new CCrmLead(true);
		$fields1 = array( 
			'STATUS_ID' => 'CONVERTED' 
		);
		$entity->update($lead_id, $fields1); 

		$entity = new CCrmDeal; 
		$fields2 = $lead;
		$fields2['TYPE_ID'] = array_rand(array('SERVICE' => 0, 'SALE' => 1, 'COMPLEX' => 2, 'GOODS'  => 3, 'SERVICES' => 4), 1); 
		$fields2['STAGE_ID'] = array_rand(array('NEW' => 0, 'PREPARATION' => 1, 'PREPAYMENT_INVOICE' => 2, 'EXECUTING' => 3, 'FINAL_INVOICE' => 4, 'WON' => 5), 1);
		$fields2['CLOSEDATE'] = $lead['DATE_CLOSED'];
		$fields2['MOVED_BY_ID'] = $lead['MOVED_BY'];
		$new_deal = $entity->add($fields2);

		$arFields = Array(
			"TITLE" => $fields2['STAGE_ID'],
			"DESCRIPTION" => $fields2['COMMENTS'],
			"RESPONSIBLE_ID" => $fields2['ASSIGNED_BY_ID'],
			"CREATED_BY" => $fields2['ASSIGNED_BY_ID'],
			"DEADLINE" => date("d.m.Y", strtotime("+20 days")),
			"UF_CRM_TASK" => array('D_'.$new_deal)
		);

		$task = new \Bitrix\Tasks\Item\Task($arFields);
		$result = $task->save();
		if($result->isSuccess())
		{
			echo "Задача для сделки создана!"."<br>";
		}
		else
		{
			print('Suck:');
			print_r($result->dump());
		}
}*/
	if (CModule::IncludeModule("iblock")){
		$arFilter = Array("IBLOCK_ID"=>5);
		$res = CIBlockElement::GetList(Array(), $arFilter); 
		$arRes = array();
		if ($ob = $res->GetNextElement(true, false)){ 
			$arFields = $ob->GetFields();
			$arRes = $arFields;
			$arProps = $ob->GetProperties();
		}
		//echo "<pre>".print_r($arRes,true)."</pre>";
		$arPropers = array();
		foreach($arProps as $key => $value){
			$arRes[$key] = $value['NAME'];
		}


		//echo "<pre>".print_r($arRes,true)."</pre>";

		$iblockTypes = Bitrix\Iblock\TypeTable::getList(array('select' => array('*', 'LANG_MESSAGE')))->FetchAll();
		$typeBlocks = array();
		$i = 0;
		foreach($iblockTypes as $key => $value){
			if($value['IBLOCK_TYPE_LANG_MESSAGE_LANGUAGE_ID'] == 'ru') {
				$res = CIBlock::GetList(
					Array(), 
					Array(
					  'TYPE' => $value['IBLOCK_TYPE_LANG_MESSAGE_IBLOCK_TYPE_ID'], 
					  'SITE_ID' => SITE_ID, 
					  'ACTIVE' => 'Y', 
					  "CNT_ACTIVE" => "Y", 
				   ), true
				);
				$ii = 0;
				while($ar_res = $res->Fetch())
				{
					//echo "<pre>i".$i."   ".print_r($ar_res['CODE'],true)."</pre>";
					$ii++;
				}

				if($ii > 0){
					$typeBlocks[$value['IBLOCK_TYPE_LANG_MESSAGE_IBLOCK_TYPE_ID']] = $value['IBLOCK_TYPE_LANG_MESSAGE_NAME'];
					if($i == 0)
						$current_type = $value['IBLOCK_TYPE_LANG_MESSAGE_IBLOCK_TYPE_ID'];
					$i ++;
				}
			}
		}
		//echo "<pre>".print_r($typeBlocks,true)."</pre>";
		$res = CIBlock::GetList(
		 	Array(), 
		   	Array(
			  'TYPE'=>'photos', 
			  'SITE_ID'=>SITE_ID, 
			  'ACTIVE'=>'Y', 
			  "CNT_ACTIVE"=>"Y", 
		   ), true
		);
		while($ar_res = $res->Fetch())
		{
			// echo "<pre>".print_r($ar_res['CODE'],true)."</pre>";
		}
	}


	$entityFields = [];
		$preparedFields = [];

		$preparedFields = [];
		if (!CModule::IncludeModule('iblock'))
			return [];			
		
		$arFilter = Array("IBLOCK_ID"=>6);
		$res = CIBlockElement::GetList(Array(), $arFilter); 
		
		if ($ob = $res->GetNextElement(true, false)){ 
			$arFields = $ob->GetFields();
			$arProps = $ob->GetProperties();
		}


		foreach($arFields as $key => $value){
			$preparedFields[$key] = $key;
		}
		
		foreach($arProps as $key => $value){
			$preparedFields['PROPERTY_'.$key] = 'PROPERTY_'.$key;
		}

		$listIgnoreFieldId = ['IBLOCK_ID', 'IBLOCK_TYPE_ID', 'IBLOCK_CODE', 'IBLOCK_NAME', 'IBLOCK_EXTERNAL_ID'];		
		foreach ($listIgnoreFieldId as $fieldId)
			unset($preparedFields[$fieldId]);
		echo "<pre>".print_r($preparedFields,true)."</pre>";

	echo "Выполнено";

endif;?>