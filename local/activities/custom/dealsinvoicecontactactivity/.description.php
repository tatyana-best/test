<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arActivityDescription = array(
	'NAME' => GetMessage('DEALS_INVOICE_CONTACT_ACTIVITY_NAME'),
	'DESCRIPTION' => GetMessage('DEALS_INVOICE_CONTACT_ACTIVITY_DESCR'),
	'TYPE' => 'activity',
	'CLASS' => 'DealsInvoiceContactActivity',
	'JSCLASS' => 'BizProcActivity',
	'CATEGORY' => array(
		'ID' => 'other',
	),
	'RETURN' => array(
		'CountDealsInWork' => array(
			'NAME' => GetMessage('DEALS_INVOICE_CONTACT_ACTIVITY_COUNT_DEALS_IN_WORK'),
			'TYPE' => 'int',
		),
		'CountFailDeals' => array(
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
		),	
	),
);
?>