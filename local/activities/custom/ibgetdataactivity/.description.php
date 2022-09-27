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
	//'ADDITIONAL_RESULT' => ['IblockFieldsResult'],	

	'RETURN' => array(
		'IblockElementId' => array(
			'NAME' => 'ID найденного элемента',
			'TYPE' => 'int',
		),
		/*'CountFailDeals' => array(
			'NAME' => GetMessage('DEALS_INVOICE_CONTACT_ACTIVITY_COUNT_FAIL_DEALS'),
			'TYPE' => 'int',
		),
		'CountSuccessDeals' => array(
			'NAME' => GetMessage('DEALS_INVOICE_CONTACT_ACTIVITY_COUNT_SUCCESS_DEALS'),
			'TYPE' => 'int',
		),
		'SumSuccessDeals' => array(
			'NAME' => GetMessage('DEALS_INVOICE_CONTACT_ACTIVITY_SUM_SUCCESS_DEALS'),
			'TYPE' => 'int',
		),
		'SumFailInoice' => array(
			'NAME' => GetMessage('DEALS_INVOICE_CONTACT_ACTIVITY_SUM_FAIL_INVOICE'),
			'TYPE' => 'int',
		),
		'SumSuccessInoice' => array(
			'NAME' => GetMessage('DEALS_INVOICE_CONTACT_ACTIVITY_SUM_SUCCESS_INVOICE'),
			'TYPE' => 'int',
		),	*/
	),
	
);