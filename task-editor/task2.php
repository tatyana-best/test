<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Задачи. Задание 2.");
?>
<?
if (CModule::IncludeModule("tasks")):?>
<?
	
	$checklists = array(
		0 => array(
			'TITLE'=>'Подготовка Технического задания', 
			'IS_COMPLETE'=>'Y',
			'IS_PARENT' => 'N'
		),
		1 => array(
			'TITLE'=>'Дизайн', 
			'IS_COMPLETE'=>'N',
			'IS_PARENT' => 'N'
		),
		2 => array(
			'TITLE'=>'Верстка', 
			'IS_COMPLETE'=>'N',
			'IS_PARENT' => 'N'
		),
		3 => array(
			'TITLE'=>'Бэкенд', 
			'IS_COMPLETE'=>'N',
			'IS_PARENT' => 'N'
		),
		4 => array(
			'TITLE'=>'Оценка', 
			'IS_COMPLETE'=>'Y',
			'IS_PARENT' => 'Y'
		),
		5 => array(
			'TITLE'=>'Подписание договор', 
			'IS_COMPLETE'=>'Y',
			'IS_PARENT' => 'Y'
		),
		6 => array(
			'TITLE'=>'Создание подзадач по проекту', 
			'IS_COMPLETE'=>'N',
			'IS_PARENT' => 'Y'
		),
	);
	
	
	$userId = 1;

	$tasksIds = getAllTasksIds();

	foreach($tasksIds as $taskId) {

		if($taskId['PARENT_ID'] == 0){
			$parent = \CTaskItem::getInstance($taskId['ID'], $userId);
			foreach ($checklists as $item)
			{
				if($item['IS_PARENT'] == 'Y')
					\CTaskCheckListItem::add($parent, array("TITLE" => $item['TITLE'], "IS_COMPLETE" => $item['IS_COMPLETE']));
			}
		}
		else {
			$child = \CTaskItem::getInstance($taskId['ID'], $userId);
			foreach ($checklists as $item)
			{
				if($item['IS_PARENT'] == 'N') 
					\CTaskCheckListItem::add($child, array("TITLE" => $item['TITLE'], "IS_COMPLETE" => $item['IS_COMPLETE']));
			}
		}

	}


	echo "Выполнено!";

?>
<?endif;?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>