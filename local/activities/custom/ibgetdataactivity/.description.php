<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arActivityDescription = array(
	'NAME' => GetMessage('IB_ACTIVITY_GET_DATA_NAME'),
	'DESCRIPTION' => GetMessage('IB_ACTIVITY_GET_DATA_DESC'),
	'TYPE' => 'activity',
	'CLASS' => 'IbGetDataActivity',
	'JSCLASS' => 'BizProcActivity',
	'CATEGORY' => array(
		'ID' => 'other',
	),
	'ADDITIONAL_RESULT' => ['IblockFieldsResult'],	
	
);