<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Задачи. Задание 4.");
?>
<?
if (CModule::IncludeModule("tasks")):?>
<?
	$user_ids = getUsers();

	$tasksIds = getTasksIds('!=PARENT_ID');

	$task_ids_result = [];

	foreach($tasksIds as $taskId) {
		$list = \CTaskCheckListItem::getList(['filter'=>['TASK_ID'=>$taskId,  'TITLE' => 'BX_CHECKLIST_1', 'IS_COMPLETE' => 'N'], 'select'=>['ID', 'TITLE', 'IS_COMPLETE'], 'order'=>['ID']]);
		foreach($list as $item )
		{
			$task_ids_result[] = $taskId;
		}
	}

	//добавляем подзадачам, у которых есть невыполненные чек-листы, три отчета
	foreach($task_ids_result as $item){
		$list = \CTaskCheckListItem::getList(['filter'=>['TASK_ID'=>$item, '!=TITLE' => 'BX_CHECKLIST_1',  'IS_COMPLETE' => 'N'], 'select'=>['ID', 'TITLE', 'IS_COMPLETE'], 'order'=>['ID']]);
		foreach($list as $val)
		{
			$task = \CTaskItem::getInstance($item, 1);
			CTaskElapsedItem::add($task, array("SECONDS" => mt_rand(120, 12000), "COMMENT_TEXT" => $val["TITLE"], "CREATED_DATE" => date()));
		}
	}

	echo "Выполнено!";


?>
<?endif;?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>