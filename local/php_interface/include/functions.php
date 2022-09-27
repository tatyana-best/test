<?
//получаем id задач (или подзадач)
function getTasksIds($string){
	$tasks = \Bitrix\Tasks\Item\Task::find(array('select' => array('ID', 'TITLE', 'PARENT_ID'), 'filter' => array($string => 0)));
	if($tasks->isSuccess())
	{
		$ids = [];
		foreach($tasks as $task)
	   {
			$ids[] = $task->get("ID");
	   }
	}
	else
	{
		return false;
	}

	return $ids;
}

//получаем id и маркер, является ли задача подзадачей, для всех задач и подзадач
function getAllTasksIds(){
	$tasks = \Bitrix\Tasks\Item\Task::find(array('select' => array('ID', 'TITLE', 'PARENT_ID'), 'filter' => array()));
	if($tasks->isSuccess())
	{
		$ids = array();
		foreach($tasks as $key => $task)
	   {
			$ids[$key]["ID"] = $task->get("ID");
			$ids[$key]["PARENT_ID"] = $task->get("PARENT_ID");
	   }
	}
	else
	{
		return false;
	}

	return $ids;
}

//получаем задачу для записи в HighLoadBlock
function getTasksForHighLoadBlock($id){
	$tasks = \Bitrix\Tasks\Item\Task::find(array('select' => array('ID', 'CREATED_BY', 'CREATED_DATE',), 'filter' => array('ID' => $id)));
	if($tasks->isSuccess())
	{
		$ids = array();
		foreach($tasks as $key => $task)
	   {
			$ids[$key]["ID"] = $task->get("ID");
			$ids[$key]["CREATED_BY"] = $task->get("CREATED_BY");
			$ids[$key]["CREATED_DATE"] = $task->get("CREATED_DATE");
	   }
	}
	else
	{
		return false;
	}

	return $ids;
}

//получаем id всех активных пользователей
function getUsers(){
	$rsUsers = CUser::GetList(($by="id"), ($order="desc"), array("ACTIVE" => "Y"));
	$user_ids = [];
	while($arItem = $rsUsers->GetNext()) :
		$user_ids[] = $arItem["ID"]; 
	endwhile;

	return $user_ids;
}

//выбираем из значений элементов массива только уникальные значения
function arrayUnique($arr) {
	$uniq_arr = array();
	foreach ($arr as $item) {
		if (!in_array($item, $uniq_arr)) {
			$uniq_arr[] = $item;
		}
	}

	return $uniq_arr;
}

//генерируем двух разных случайных пользователей (для добавления двух наблюдателей)

function getTwoAuditors() {
	$user_ids = getUsers();
	$auditor1 = $user_ids[array_rand($user_ids)];
	$auditor2 = $user_ids[array_rand($user_ids)];
	$auditors[0] = $auditor1;
	$auditors[1] = $auditor2;
	while ($auditor1 == $auditor2) {
		$auditor2 = $user_ids[array_rand($user_ids)];
		$auditors[1] = $auditor2;
	}
	return $auditors;
}