<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

if(isset($_POST['task']) && $_POST['task'] !='') {
	if (CModule::IncludeModule("tasks")):
		$user_ids = getUsers();
		$uf_iblock_id = 16; 
		$uf_name = Array("ID", "NAME", "UF_USER"); 
		if (CModule::IncludeModule("iblock")):
			$subtasks = array();
			$sections_id = array();
		   $uf_arresult = CIBlockSection::GetList(Array("SORT"=>"­­ASC"), Array("IBLOCK_ID" => $uf_iblock_id), false, $uf_name);
		   while($uf_value = $uf_arresult->GetNext()):
				$subtasks[] = array(
					"NAME" => $uf_value["NAME"],
					"USER" => $uf_value["UF_USER"],
					"ID" => $uf_value["ID"]
				);
				$sections_id[] = $uf_value["ID"];
		   endwhile;
			//echo "<pre>".print_r($subtasks,true)."</pre>";
	
			$arSelect = Array("ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION_ID");
			$arFilter = Array("IBLOCK_ID"=>$uf_iblock_id, "SECTION_ID"=>$sections_id,  "SHOW_NEW" => "Y");
			$res = CIBlockElement::GetList(Array(), $arFilter, $arSelect, false, false);
			$elements = array();
			while($ob = $res->GetNextElement()){ 
				$arFields = $ob->GetFields();
				$elements[$arFields['IBLOCK_SECTION_ID']][$arFields['ID']] = $arFields['NAME'];
			}
			//echo "<pre>".print_r($elements,true)."</pre>";
		endif;
	
		foreach($subtasks as $subtask){
			$arFields = Array(
				"TITLE" => $subtask["NAME"],
				"RESPONSIBLE_ID" => $subtask["USER"],
				"PARENT_ID" => $_POST['task'],
				"CREATED_BY" => $user_ids[array_rand($user_ids)],
				"DEADLINE" => date("d.m.Y", strtotime("+10 days")),
			);
	
			$task = new \Bitrix\Tasks\Item\Task($arFields);
			$result = $task->save();
			if($result->isSuccess())
			{
				//echo "Подзадача " .$subtask["NAME"]. " для задачи 219 создана!"."<br>";
			}
			else
			{
				print('Suck:');
				print_r($result->dump());
			}
	
			$subtask_id = $task->getId();
	
			$parent = \CTaskItem::getInstance($subtask_id, 1);
			foreach ($elements[$subtask["ID"]] as $element)
			{
				\CTaskCheckListItem::add($parent, array("TITLE" => $element, "IS_COMPLETE" => 'N'));
				//echo "<pre>".print_r($subtask["ID"],true)."</pre>";
			}
	
		}
	endif;
	echo $_POST['task'];
}

