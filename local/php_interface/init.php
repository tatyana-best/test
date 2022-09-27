<?php

include($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/include/connect_js_libs.php');

include($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/include/functions.php');


if(CSite::InDir('/crm/company/details/')) {

	AddEventHandler("main", "OnProlog", Array("LinkDeals", "LinkOnPrologHandler"));
	
	class LinkDeals
	{
		function LinkOnPrologHandler()
		{
			$arJsConfig = array(
				'custom_link_deals' => array(
					'js' => '/local/php_interface/js_libs/link_deals.js',
					'css' => '/local/php_interface/js_libs/main.css',
					'rel' => Array("window")
				)
	
			);
	
			foreach ($arJsConfig as $ext => $arExt) {
				\CJSCore::RegisterExt($ext, $arExt);
			}
	
		}
	}

	CUtil::InitJSCore(array("custom_link_deals"));
}

use Bitrix\Highloadblock\HighloadBlockTable as HL;
CModule::IncludeModule('highloadblock');

AddEventHandler('tasks', 'OnTaskAdd', 'AddTaskInHighloadBlock');
function AddTaskInHighloadBlock(&$arFields) {

	define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");

   	$hlbl = 2;
	$hlblock = HL::getById($hlbl)->fetch(); 
	
	$entity = HL::compileEntity($hlblock); 
	$entity_data_class = $entity->getDataClass(); 

	if (CModule::IncludeModule("tasks"))
	{
		$res = CTasks::GetList(
			Array("TITLE" => "ASC"), 
			Array("ID" => $arFields)
		);
	
		while ($arTask = $res->GetNext())
		{
			AddMessage2Log("Дата: " . $arTask["CREATED_DATE"] . " Номер задачи: ". $arFields, "highloadblock");
			$date_create = $arTask["CREATED_DATE"];
		}
	}

   $data = array(
	  "UF_TASK_ID"=>$arFields,
	  "UF_TASK_CREATER"=>'1',
	  "UF_TASK_DATE_CREATE"=>$date_create
   );

   $result = $entity_data_class::add($data);
}


use Bitrix\Main\Diag\Debug;
$eventManager = Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler(
    'tasks',
	'\Bitrix\Tasks\Internals\Task\Checklist::OnUpdate',
    function (\Bitrix\Main\Event $e) {
		if (CModule::IncludeModule("tasks")) {

			Debug::writeToFile(array_keys($e->getParameters()),'getParameters', '/log.txt');
			$fields = $e->getParameter('fields');
			Debug::writeToFile($fields,'fields', '/log.txt');
          	
          	$checkBoxId = $e->getParameter('id');
            $check_id = $checkBoxId['ID']; //можно использовать эту переменную в строчке 116 вместо $_REQUEST['checkListItemId']
          	Debug::writeToFile($check_id,'id_check', '/log.txt');
          
          	$primary = $e->getParameter('primary');
			Debug::writeToFile($primary,'primary', '/log.txt');
          
          	$object = $e->getParameter('object');
			//Debug::writeToFile(strval($object), 'object', '/log.txt');
          
          	$task_id = $_REQUEST['taskId'];
          	
          	
            $res = CTasks::GetList(
              Array(), 
              Array("ID" => $task_id),
              Array("ID", "RESPONSIBLE_ID"),
            );

            if ($arTask = $res->GetNext())
            {
              $responsible = $arTask["RESPONSIBLE_ID"];
              $list = \CTaskCheckListItem::getList(['filter'=>['TASK_ID'=>$task_id,  'IS_COMPLETE' => 'N', 'ID' => $_REQUEST['checkListItemId']], 'select'=>['ID', 'TITLE', 'IS_COMPLETE'], 'order'=>['ID']]);
              foreach($list as $item )
              {
                $title_check = $item['TITLE'];
                $id_check = $item['ID'];
              }

            }
            
          	if($title_check){
              $my_task = \CTaskItem::getInstance($task_id, 1);
              CTaskElapsedItem::add($my_task, array("SECONDS" => mt_rand(120, 12000), "USER_ID" => $responsible, "COMMENT_TEXT" => $title_check, "CREATED_DATE" => date()));
            }

		}
    }
);


CModule::IncludeModule('tasks');
AddEventHandler('tasks', 'OnTaskUpdate', 'CloseTask');
function CloseTask(&$arFields) {

	define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");

	if (CModule::IncludeModule("tasks"))
	{
		$res = CTasks::GetList(
			Array("TITLE" => "ASC"), 
			Array("ID" => $arFields),
			Array("ID", "REAL_STATUS", "CREATED_DATE", "STATUS_CHANGED_BY",  "UF_*")
		);
	
		if ($arTask = $res->GetNext()) 
		{
			//AddMessage2Log("Дата: " . $arTask["CREATED_DATE"] . " Номер задачи: ". $arFields . " Привязанная сделка: ".$arTask['UF_CRM_TASK'][0] . " Статус задачи: ".$arTask['REAL_STATUS'] . " Пользователь, изменивший статус: ".$arTask['STATUS_CHANGED_BY'], "tasks");
			$deal_id = (int)mb_substr($arTask['UF_CRM_TASK'][0], 2);
			$deal_cod = $arTask['UF_CRM_TASK'][0][0];

			if($arTask['REAL_STATUS'] == CTasks::STATE_COMPLETED && $deal_cod == 'D') {
				//AddMessage2Log("Условие ок", "crm");
				if (CModule::IncludeModule("crm")){
					$arFilter = array(
						'ID' => $deal_id
					);
					$arOrder = array(); 
					$getDeals = CCrmDeal::GetListEx($arOrder, $arFilter);
					if($deals = $getDeals->fetch()){
						$type_id = array(
							0 => 'PHONE',
							1 => 'EMAIL'
						);
						$dbResMultiFields = CCrmFieldMulti::GetList(array(),array('ENTITY_ID'=>'COMPANY','TYPE_ID'=>$type_id,'ELEMENT_ID'=>$deals['COMPANY_ID']));
						$companies = [];
						while($arMultiFields = $dbResMultiFields->Fetch()){
							$companies[$arMultiFields['TYPE_ID']] = $arMultiFields;
						}

						$company_phone = $companies['PHONE']['VALUE'];
						$company_email = $companies['EMAIL']['VALUE'];

						AddMessage2Log("Название сделки (нет телефона): " . $deals["TITLE"] ." - ".$deals["ID"]. " Номер телефона компании " . $company_phone . " Email компании " . $company_email, "crm");

						if($deals['COMPANY_HAS_PHONE'] == '') {
							//AddMessage2Log("Название сделки (нет телефона): " . $deals["TITLE"] . " Ответственный за сделку " . $deals["ASSIGNED_BY_ID"] . " Стадия сделки " . $deals["STAGE_ID"], "crm");

							$arComms = array(
							   array(
								  'ID' => 0,
								  'TYPE' => 'EMAIL',
								  'VALUE' => $company_email,
								  'ENTITY_ID' => $deal_id,
								  'ENTITY_TYPE_ID' => 3
							   )
							);
							$fields = [
								"SUBJECT" => "Письмо по сделке ".$deals["TITLE"],
								"DESCRIPTION" => "Важное письмо для сделки без телефона",
								"DESCRIPTION_TYPE" => 3,//text,html,bbCode type id in: CRest::call('crm.enum.contenttype');
								"COMPLETED" => "N",//send now
								"DIRECTION" => 2,// CRest::call('crm.enum.activitydirection');
								"OWNER_ID" => $deal_id,
								"OWNER_TYPE_ID" => 2, // CRest::call('crm.enum.ownertype');
								"TYPE_ID" => 4, //2- звонок CRest::call('crm.enum.activitytype');
								"COMMUNICATIONS" => $arComms,
								"START_TIME" => date(),
								"END_TIME" => date("d.m.Y", strtotime("+10 days")),
								"RESPONSIBLE_ID" => $deals["ASSIGNED_BY_ID"],
								'SETTINGS' => [
									'MESSAGE_FROM' => implode(
										' ',
										[$deals['ASSIGNED_BY_NAME'], $deals['ASSIGNED_BY_LAST_NAME'], '<' . $staff . '>']
									),
		
								],
							];
							$ID = CCrmActivity::Add($fields, false, true, array('REGISTER_SONET_EVENT' => true));
							if($ID > 0)
							{
							   CCrmActivity::SaveCommunications($ID, $arComms, $fields, true, false);
							}

						} else {
								//AddMessage2Log("Название сделки (есть телефон): " . $deals["TITLE"] . " Ответственный за сделку " . $deals["ASSIGNED_BY_ID"] . " Стадия сделки " . $deals["STAGE_ID"], "crm");
								$arComms = array(
								   array(
									  'ID' => 0,
									  'TYPE' => 'PHONE',
									  'VALUE' => $company_phone,
									  'ENTITY_ID' => $deal_id,
									  'ENTITY_TYPE_ID' => 3
								   )
								);
								$fields = [
								"SUBJECT" => "Звонок по сделке ".$deals["TITLE"],
								"DESCRIPTION" => "Важный звонок для сделки с телефоном",
								"DESCRIPTION_TYPE" => 3,//text,html,bbCode type id in: CRest::call('crm.enum.contenttype');
								"COMPLETED" => "N",//send now
								"DIRECTION" => 2,// CRest::call('crm.enum.activitydirection');
								"OWNER_ID" => $deal_id,
								"OWNER_TYPE_ID" => 2, // CRest::call('crm.enum.ownertype');
								"TYPE_ID" => 2, 
								"COMMUNICATIONS" => $arComms,
								"START_TIME" => date(),
								"END_TIME" => date("d.m.Y", strtotime("+10 days")),
								"RESPONSIBLE_ID" => $arTask['STATUS_CHANGED_BY'],
							];
							$ID = CCrmActivity::Add($fields, false, true, array('REGISTER_SONET_EVENT' => true));
							if($ID > 0)
							{
							   CCrmActivity::SaveCommunications($ID, $arComms, $fields, true, false);
							}

						}

						$entity = new CCrmDeal(true);
						$stage_deal = array('NEW' => 0, 'PREPARATION' => 1, 'PREPAYMENT_INVOICE' => 2, 'EXECUTING' => 3, 'FINAL_INVOICE' => 4, 'WON' => 5);
						$current_stage = $deals['STAGE_ID'];
						$num_current_stage = $stage_deal[$current_stage];
						if($num_current_stage != 5){
							$num_new_stage = $num_current_stage + 1;
							$fields1 = array( 
								'STAGE_ID' => array_flip($stage_deal)[$num_new_stage],
							);
							$entity->update($deal_id, $fields1); 
						}

						$rows = array(); 
						for($i = 1; $i <= 2; $i++){
							$rows[] = array( 
								'PRODUCT_NAME' => 'Название продукта '.$i, 
								'QUANTITY' => 2,   
								'PRICE' => 300, 
								'MEASURE_CODE' => 796
                              );
						}
						CCrmProductRow::SaveRows('D', $deal_id, $rows);

						$arr=CCatalogProduct::GetByIDEx(24);
						if($tax=CCatalogVat::GetByID(2)->fetch()) 
						{
							\CCrmProductRow::Add(['OWNER_TYPE' => 'D', 'OWNER_ID' => $deal_id, 'PRODUCT_ID' => 24, 'DISCOUNT_RATE' => '', 'DISCOUNT_SUM' => '', 'TAX_RATE' => $tax['RATE'], 'TAX_INCLUDED' => 'N', 'QUANTITY' => 1, 'PRICE' => '', 'PRICE_NETTO' => $arr['PRICES'][1]['PRICE'], 'PRICE_BRUTTO' => '', 'PRICE_EXCLUSIVE' => $arr['PRICES'][1]['PRICE'], 'MEASURE_CODE' => 796, 'CURRENCY_ID' => RUB]);
						}
					}
				}
			}
		}
	}
}