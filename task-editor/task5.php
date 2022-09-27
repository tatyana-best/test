<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Задачи. Задание 5.");
use Bitrix\Main\Loader;
Loader::IncludeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
?>
<?if (CModule::IncludeModule("tasks")):?>
<?
	$user_ids = getUsers();

	$arFields = Array(
		"TITLE" => "Задача для записи в HighLoadBlock*",
		"DESCRIPTION" => "Описание для задачи",
		"RESPONSIBLE_ID" => $user_ids[array_rand($user_ids)],
		"CREATED_BY" => $user_ids[array_rand($user_ids)],
		"DEADLINE" => date("d.m.Y", strtotime("+15 days")),
	);

	$task = new \Bitrix\Tasks\Item\Task($arFields);
	$result = $task->save();

	if($result->isSuccess())
	{
		echo "Задача создана!"."<br>";
	}
	else
	{
		print('Suck:');
		print_r($result->dump());
	}

	$task_id = $task->getId();

	//echo "<pre>".print_r($task_id,true)."</pre>";
	//echo "<pre>".print_r($task['TITLE'],true)."</pre>";
	//echo "<pre>".print_r($task['CREATED_BY'],true)."</pre>";


/*СОЗДАЕМ НОВЫЙ HIGHLOADBLOCK

	$arLangs = Array(
		'ru' => 'Новые задачи',
		'en' => 'New tasks'
	);


	$res = HL\HighloadBlockTable::add(array(
		'NAME' => 'NewTasks',
		'TABLE_NAME' => 'new_tasks', 
	));


	if ($res->isSuccess()) {
		$id = $res->getId();
		foreach($arLangs as $lang_key => $lang_val){
			HL\HighloadBlockLangTable::add(array(
				'ID' => $id,
				'LID' => $lang_key,
				'NAME' => $lang_val
			));
		}
	} else {
		$errors = $res->getErrorMessages();
		var_dump($errors);  
	}

	$UFObject = 'HLBLOCK_'.$id;

	$arTaskFields = Array(
		'UF_TASK_ID'=>Array(
			'ENTITY_ID' => $UFObject,
			'FIELD_NAME' => 'UF_TASK_ID',
			'USER_TYPE_ID' => 'string',
			'MANDATORY' => 'Y',
			"EDIT_FORM_LABEL" => Array('ru'=>'ИД задачи', 'en'=>'Task ID'), 
			"LIST_COLUMN_LABEL" => Array('ru'=>'ИД задачи', 'en'=>'Task ID'),
			"LIST_FILTER_LABEL" => Array('ru'=>'ИД задачи', 'en'=>'Task ID'), 
			"ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
			"HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
		),
		'UF_TASK_CREATER'=>Array(
			'ENTITY_ID' => $UFObject,
			'FIELD_NAME' => 'UF_TASK_CREATER',
			'USER_TYPE_ID' => 'string',
			'MANDATORY' => 'Y',
			"EDIT_FORM_LABEL" => Array('ru'=>'Пользователь, создавший задачу', 'en'=>'Date added'), 
			"LIST_COLUMN_LABEL" => Array('ru'=>'Пользователь, создавший задачу', 'en'=>'Date added'),
			"LIST_FILTER_LABEL" => Array('ru'=>'Пользователь, создавший задачу', 'en'=>'Date added'), 
			"ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
			"HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
		),
		'UF_TASK_DATE_CREATE'=>Array(
			'ENTITY_ID' => $UFObject,
			'FIELD_NAME' => 'UF_TASK_DATE_CREATE',
			'USER_TYPE_ID' => 'string',
			'MANDATORY' => 'Y',
			"EDIT_FORM_LABEL" => Array('ru'=>'Дата создания задачи', 'en'=>'Date create'), 
			"LIST_COLUMN_LABEL" => Array('ru'=>'Дата создания задачи', 'en'=>'Date create'),
			"LIST_FILTER_LABEL" => Array('ru'=>'Дата создания задачи', 'en'=>'Date create'), 
			"ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
			"HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
		),
	);


	$arTaskFieldsRes = Array();
	foreach($arTaskFields as $arTaskField){
		$obUserField  = new CUserTypeEntity;
		$ID = $obUserField->Add($arTaskField);
		$arSavedFieldsRes[] = $ID;
	};
*/



	$hlbl = 2;
	$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 
	
	$entity = HL\HighloadBlockTable::compileEntity($hlblock); 
	$entity_data_class = $entity->getDataClass(); 

   $data = array(
	  "UF_TASK_ID"=>'33',
	  "UF_TASK_CREATER"=>'1',
	  "UF_TASK_DATE_CREATE"=>date("d.m.Y h:m:s")
   );

   $result = $entity_data_class::add($data);

	echo "Данные записаны в HighLoadBlock!"."<br>";

?>
<?endif;?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>