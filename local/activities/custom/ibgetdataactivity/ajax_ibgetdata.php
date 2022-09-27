<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('bizproc');

global $APPLICATION;

if (!check_bitrix_sessid())
	die();
if (!CBPDocument::CanUserOperateDocumentType(CBPCanUserOperateOperation::CreateWorkflow, $GLOBALS["USER"]->GetID(), $_REQUEST['document_type']))
	die();

CUtil::DecodeUriComponent($_REQUEST);

$activityType = $_REQUEST['activity'];

$runtime = CBPRuntime::GetRuntime();
$runtime->StartRuntime();

$arActivityDescription = $runtime->GetActivityDescription($activityType);
if ($arActivityDescription == null)
	die ("Bad activity type!".htmlspecialcharsbx($activityType));

$runtime->IncludeActivityFile($activityType);

$isHtml = (!empty($_REQUEST['content_type']) && $_REQUEST['content_type'] == 'html');
if ($isHtml)
	$APPLICATION->ShowAjaxHead();

	
	if (CModule::IncludeModule("iblock")){
		$arFilter = Array("IBLOCK_ID"=>$_REQUEST['IblockId']);
		$res = CIBlockElement::GetList(Array(), $arFilter); 
		$arRes = array();
		if ($ob = $res->GetNextElement(true, false)){ 
			$arFields = $ob->GetFields();
			$arRes = $arFields;
			$arProps = $ob->GetProperties();
		}
		$arPropers = array();
		foreach($arProps as $key => $value){
			$arRes['PROPERTY_'.$key] = $value['NAME'];
		}

		$listIgnoreFieldId = ['IBLOCK_ID', 'IBLOCK_TYPE_ID', 'IBLOCK_CODE', 'IBLOCK_NAME', 'IBLOCK_EXTERNAL_ID'];		
		foreach ($listIgnoreFieldId as $fieldId)
			unset($arRes[$fieldId]);
		
		$str = '';
		foreach($arRes as $k => $val){
			$str .= '<tr><td align="right" width="40%" class="adm-detail-content-cell-l"><span style="font-weight: bold">'.$k.'</span></td><td width="60%" class="adm-detail-content-cell-r">';
			$str .= '<textarea rows="1" cols="50" name="IblockFields['.$k.']" style="width: 60%" ></textarea>';
			$str .= '</td></tr>';
		}
	}

$res = $str;
echo $isHtml? $res : CUtil::PhpToJSObject($res);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");