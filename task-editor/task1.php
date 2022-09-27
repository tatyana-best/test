<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Задачи. Задание 1.");
?>
<?
if (CModule::IncludeModule("tasks")):?>
<?
	//Записываем в массив user_ids id всех активных пользователей
	$user_ids = getUsers();

	//Реализация с помощью Add и GetList
	/*
	//Записываем с массив ids id всех задач, которые не являются подзадачами "PARENT_ID" => 0
	$rsTask = CTasks::GetList(array(), array("PARENT_ID" => 0), array("*"), array("USER_ID" => 1));
	$ids = [];
	while($arTask = $rsTask->GetNext()){
		$ids[] = $arTask["ID"];
	}

	//Перебираем все задачи из массива ids
	foreach($ids as $id) {
		$arFields1 = Array(
			"TITLE" => "Первая подзадача",
			"DESCRIPTION" => "Описание первой подзадачи",
			"RESPONSIBLE_ID" => $user_ids[array_rand($user_ids)],
			"PARENT_ID" => $id,
			"CREATED_BY" => $user_ids[array_rand($user_ids)],
			"DEADLINE" => date("d.m.Y", strtotime("+10 days")),
		);

		$arFields2 = Array(
			"TITLE" => "Вторая подзадача",
			"DESCRIPTION" => "Описание второй подзадачи",
			"RESPONSIBLE_ID" => $user_ids[array_rand($user_ids)],
			"PARENT_ID" => $id,
			"CREATED_BY" => $user_ids[array_rand($user_ids)],
			"DEADLINE" => date("d.m.Y", strtotime("+20 days")),
		);

		//Создаем подзадачи
		$obTask = new CTasks;
		$ID1 = $obTask->Add($arFields1);
		$ID2 = $obTask->Add($arFields2);

		$success1 = ($ID1>0);
	
		if($success1)
		{
			echo "Первая подзадача для задачи " .$id. " создана!"."<br>";
		}
		else
		{
			if($e = $APPLICATION->GetException())
				echo "Error1: ".$e->GetString();  
		}

		$success2 = ($ID2>0);
	
		if($success2)
		{
			echo "Вторая подзадача для задачи " .$id. " создана!"."<br>";
		}
		else
		{
			if($e = $APPLICATION->GetException())
				echo "Error2: ".$e->GetString();  
		}
	}*/


	//Реализация с помощью нового Api

	foreach(getTasksIds('=PARENT_ID') as $id) {
	
		$arFields1 = Array(
			"TITLE" => "Первая подзадача",
			"DESCRIPTION" => "Описание первой подзадачи",
			"RESPONSIBLE_ID" => $user_ids[array_rand($user_ids)],
			"PARENT_ID" => $id,
			"CREATED_BY" => $user_ids[array_rand($user_ids)],
			"DEADLINE" => date("d.m.Y", strtotime("+10 days")),
		);
	
		$arFields2 = Array(
			"TITLE" => "Вторая подзадача",
			"DESCRIPTION" => "Описание второй подзадачи",
			"RESPONSIBLE_ID" => $user_ids[array_rand($user_ids)],
			"PARENT_ID" => $id,
			"CREATED_BY" => $user_ids[array_rand($user_ids)],
			"DEADLINE" => date("d.m.Y", strtotime("+20 days")),
		);

		$task1 = new \Bitrix\Tasks\Item\Task($arFields1);
		$result = $task1->save();
		if($result->isSuccess())
		{
			echo "Первая подзадача для задачи " .$id. " создана!"."<br>";
		}
		else
		{
			print('Suck:');
			print_r($result->dump());
		}
	
		$task2 = new \Bitrix\Tasks\Item\Task($arFields2);
		$result = $task2->save();
		if($result->isSuccess())
		{
			echo "Вторая подзадача для задачи " .$id. " создана!"."<br>";
		}
		else
		{
			print('Suck:');
			print_r($result->dump());
		}
	}
?>
<?endif;?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>