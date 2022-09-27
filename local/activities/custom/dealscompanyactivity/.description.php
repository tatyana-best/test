<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arActivityDescription = array(
	'NAME' => GetMessage('DEALS_COMPANY_ACTIVITY_NAME'),
	'DESCRIPTION' => GetMessage('DEALS_COMPANY_ACTIVITY_DESCR'),
	'TYPE' => 'activity',
	'CLASS' => 'DealsCompanyActivity',
	'JSCLASS' => 'BizProcActivity',
	'CATEGORY' => array(
		'ID' => 'other',
	),
	'RETURN' => array(
			'CountAllDeals' => array(
			'NAME' => GetMessage('DEALS_COMPANY_ACTIVITY_COUNT_ALL_DEALS'),
			'TYPE' => 'int',
			),
			'SumSuccessDeals' => array(
				'NAME' => GetMessage('DEALS_COMPANY_ACTIVITY_SUM_SUCCESS_DEALS'),
				'TYPE' => 'int',
			),
			'CountFailDeals' => array(
				'NAME' => GetMessage('DEALS_COMPANY_ACTIVITY_COUNT_FAIL_DEALS'),
				'TYPE' => 'int',
			),
			'CountDealsInWork' => array(
				'NAME' => GetMessage('DEALS_COMPANY_ACTIVITY_COUNT_DEALS_IN_WORK'),
				'TYPE' => 'int',
			),
	),
);
?>

