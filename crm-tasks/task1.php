<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Задача 1");
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

	echo "<pre>".print_r(getLeads(),true)."</pre>";

	foreach(getLeads() as $lead_id => $lead){
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
	}

	echo "Выполнено";

endif;?>