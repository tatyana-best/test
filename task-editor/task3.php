<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Задачи. Задание 3.");
?>
<?
if (CModule::IncludeModule("tasks")):?>
<?
	$user_ids = getUsers();

	//просматриваем каждую подзадачу и в ней каждый чек-лист, 
	//чек-лист с title=BX_CHECKLIST_1 - это главный элемент, в котором указано, выполнены все чек-листы или нет
	//если его поле IS_COMPLETE равно N, значит не все чек-листы выполнены в списке чек-листов
	$tasksIds = getTasksIds('!=PARENT_ID');

	$task_ids_result = [];

	foreach($tasksIds as $taskId) {

		$list = \CTaskCheckListItem::getList(['filter'=>['TASK_ID'=>$taskId,  'TITLE' => 'BX_CHECKLIST_1', 'IS_COMPLETE' => 'N'], 'select'=>['ID', 'TITLE', 'IS_COMPLETE'], 'order'=>['ID']]);
		foreach($list as $item )
		{
			$task_ids_result[] = $taskId;
		}
	}

	//добавляем подзадачам, у которых есть невыполненные чек-листы, двух наблюдателей
	foreach($task_ids_result as $item){

		/*НЕ ПОЛУЧИЛОСЬ ТАК:
		$task = new \Bitrix\Tasks\Item\Task($item);
		$task['AUDITORS'] = array(0 => 1);
		$result = $task->save();
		if($result->isSuccess())
		{
			print('EEEEEhaaaa!!!');
		}
		else
		{
			print('Suck:');
			print_r($result->dump());
		}
		*/
		$arFields = Array(
			"AUDITORS" => getTwoAuditors()
		);

		$ID = $item;

		$obTask = new CTasks;
		$success = $obTask->Update($ID, $arFields);

		if($success)
		{
			echo "Наблюдатели к подзадаче номер ".$ID." добавлены!"."<br>";
		}
		else
		{
			if($e = $APPLICATION->GetException())
				echo "Error: ".$e->GetString();
		}
	}


	//просматриваем каждую родительскую задачу и в ней каждый чек-лист, 
	//если его поле IS_COMPLETE равно Y, записываем номер задачи в массив task_ids_result
	$tasksIds = getTasksIds('=PARENT_ID');
	$task_ids_result = [];
	foreach($tasksIds as $taskId) {
		$list = \CTaskCheckListItem::getList(['filter'=>['TASK_ID'=>$taskId,  'IS_COMPLETE' => 'Y'], 'select'=>['ID', 'TITLE', 'IS_COMPLETE'], 'order'=>['ID']]);
		foreach($list as $item )
		{
			$task_ids_result[] = $taskId;
		}
	}

	//выбираем только уникальные значения в массиве task_ids_result
	$unicTasks = arrayUnique($task_ids_result);
	//если есть задачи, в которых имеются выполненные чек-листы, добавляем им одного соисполнителя
	if($unicTasks) {

		foreach($unicTasks as $item){
	
			$arFields = Array(
				"ACCOMPLICES" => array(0 => $user_ids[array_rand($user_ids)])
			);
	
			$ID = $item;
		
			$obTask = new CTasks;
			$success = $obTask->Update($ID, $arFields);
	
			if($success)
			{
				echo "Соисполнитель к задаче номер ".$ID." добавлен!"."<br>";
			}
			else
			{
				if($e = $APPLICATION->GetException())
					echo "Error: ".$e->GetString();
			}
		}
	}
?>
<?endif;?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>