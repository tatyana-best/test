<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

class CBPIbGetDataActivity extends CBPActivity
{

	public function __construct($name)
	{
		parent::__construct($name);
		$this->arProperties = [
			'Title'            => '',
			'IblockId'         => null,
			'IblockOrder' => null,
			'IblockFields'     => array(),			
			'IblockFieldsResult' => array()
		];

	}


	//формируем запрос на выборку элементов инфоблока
	public static function QueryGetArrayFieldsProperties($iblockId, $fields = [])
	{
		if (!CModule::IncludeModule("iblock"))
            return false;

		$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_*");		

		$arFilter = array_merge(array($iblockId), $fields);
		
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
		
		if($ob = $res->GetNextElement(true, false)){ 
		 	$arFields = $ob->GetFields(); 
		 	$arProps = $ob->GetProperties(); 		 	
		}

		$arrayFieldsProperties['fields'] = $arFields;
		$arrayFieldsProperties['properties'] = $arProps;

		return $arrayFieldsProperties;

	}
	

	public function execute()
	{
		
		$arFieldsProps = self::QueryGetArrayFieldsProperties(["IBLOCK_ID"=>$this->IblockId], $this->IblockFields);		

		$result_fields = [];

		foreach($arFieldsProps['fields'] as $key => $value){
			if(is_array($value['VALUE']))
			{
				$result_fields[$key] = implode('; ', $value['VALUE']);				
			}
			else
			{
				$result_fields[$key] = $value;				
			}
		}
		
		foreach($arFieldsProps['properties'] as $key => $value){
			if(is_array($value['VALUE'])){
				$result_fields['PROPERTY_'.$key] = implode('; ', $value['VALUE']['VALUE']);				
			}
			else
			{
				$result_fields['PROPERTY_'.$key] = $value['VALUE'];				
			}
		}

		if($result_fields)
		{
			$result_fields['MESSAGE'] = GetMessage('IB_ACTIVITY_GET_DATA_IS_ELEMENT_MESSAGE_YES');			
		}
		else
		{
			$result_fields['MESSAGE'] = GetMessage('IB_ACTIVITY_GET_DATA_IS_ELEMENT_MESSAGE_NO');			
		}


		$this->IblockFieldsResult = $result_fields;

		$this->SetProperties($result_fields);

		return CBPActivityExecutionStatus::Closed;
	}


	public static function GetPropertiesDialog(
		$documentType, $activityName, $workflowTemplate, $workflowParameters,
		$workflowVariables, $currentValues = null, $formName = '')
	{
		
		if (!is_array($workflowParameters))
			$workflowParameters = [];
		if (!is_array($workflowVariables))
			$workflowVariables = [];

		$renderEntityFields = '';

		if (!is_array($currentValues))
		{
			$currentValues = [
				'IblockId'         => null,
				'IblockOrder'       => null,
				'IblockFields'     => array(),			
			];

			$currentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($workflowTemplate, $activityName);
			if (is_array($currentActivity['Properties']))
			{
				$currentValues['IblockId'] = $currentActivity['Properties']['IblockId'];
				$currentValues['IblockOrder'] = $currentActivity['Properties']['IblockOrder'];	
				$currentValues['IblockFields'] = $currentActivity['Properties']['IblockFields'];	
		      	
		      	$renderEntityFields = self::renderEntityFields($currentActivity['Properties']['IblockId'],$currentValues);
			}
		}
		else
		{
			$renderEntityFields = self::renderEntityFields($currentValues['IblockId'], $currentValues);
		}
		

		$runtime = CBPRuntime::GetRuntime();

		return $runtime->ExecuteResourceFile(
			__FILE__,
			'properties_dialog.php',
			[
				'documentType'       => $documentType,
				'currentValues'      => $currentValues,
				'formName'           => $formName,
				'renderEntityFields' => $renderEntityFields,
			]
		);
	}

	public static function GetPropertiesDialogValues(
		$documentType, $activityName, &$workflowTemplate, &$workflowParameters,
		&$workflowVariables, $currentValues, &$errors)
	{
		$errors = [];

		$properties = ['DocumentType' => $documentType];
		$iblockFields = self::getIblockFields($currentValues['IblockId']);
		$properties['IblockId'] = $currentValues['IblockId'];
		$properties['IblockOrder'] = $currentValues['IblockOrder'];
		

		foreach ($iblockFields as $fieldId => $fieldValue)
		{
			$properties['IblockFields'][$fieldId] = $currentValues['IblockFields'][$fieldId];			
		}		


		$arFieldsProps = self::QueryGetArrayFieldsProperties(["IBLOCK_ID"=>$currentValues['IblockId']], $currentValues['IblockFields']);			

		$result_fields = [];

		foreach($arFieldsProps['fields'] as $key => $value){
			if(is_array($value['VALUE']))
			{
				$result_fields[$key]['Name'] = $key;
				$result_fields[$key]['Type'] = 'string';
			}
			else
			{
				$result_fields[$key]['Name'] = $key;
				$result_fields[$key]['Type'] = 'string';
			}
		}
		
		foreach($arFieldsProps['properties'] as $key => $value){
			if(is_array($value['VALUE'])){
				$result_fields['PROPERTY_'.$key]['Name'] = $key;
				$result_fields['PROPERTY_'.$key]['Type'] = 'string';
			}
			else
			{
				$result_fields['PROPERTY_'.$key]['Name'] = $key;
				$result_fields['PROPERTY_'.$key]['Type'] = 'string';
			}
		}

		if($result_fields)
		{
			$result_fields['MESSAGE']['Name'] = GetMessage('IB_ACTIVITY_GET_DATA_IS_ELEMENT_MESSAGE_YES');
			$result_fields['MESSAGE']['Type'] = 'string';
		}
		else
		{
			$result_fields['MESSAGE']['Name'] = GetMessage('IB_ACTIVITY_GET_DATA_IS_ELEMENT_MESSAGE_NO');
			$result_fields['MESSAGE']['Type'] = 'string';
		}

		$properties['IblockFieldsResult'] = $result_fields;
			
		

		if (!empty($errors))
			return false;

		$currentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($workflowTemplate, $activityName);
		$currentActivity['Properties'] = $properties;

		return true;
	}


	protected static function renderEntityFields($iblock, $currentValues = [])
	{
		$html = '';	

		$entityFields = self::getIblockFields($iblock);	
	
		foreach($entityFields as $k => $val){
			$html .= '<tr><td align="right" width="40%" class="adm-detail-content-cell-l"><span style="font-weight: bold">'.$k.'</span></td><td width="60%" class="adm-detail-content-cell-r">';

			$html .= '<textarea rows="1" cols="50" rel="tat" name="IblockFields['.$k.']" style="width: 60%" autocomplete="off">'.$currentValues['IblockFields'][$k].'</textarea>';

			$html .= '</td></tr>';
		}		

		return $html;
	}

	protected static function getIblockFields($iblock)
	{		
		$entityFields = [];
		$preparedFields = [];

		$preparedFields = [];
		if (!CModule::IncludeModule('iblock'))
			return [];			
		
		$arFilter = Array("IBLOCK_ID"=>$iblock);
		$res = CIBlockElement::GetList(Array(), $arFilter); 
		
		if ($ob = $res->GetNextElement(true, false)){ 
			$arFields = $ob->GetFields();
			$arProps = $ob->GetProperties();
		}


		foreach($arFields as $key => $value){
			$preparedFields[$key] = $key;
		}
		
		foreach($arProps as $key => $value){
			$preparedFields['PROPERTY_'.$key] = 'PROPERTY_'.$key;
		}

		$listIgnoreFieldId = ['IBLOCK_ID', 'IBLOCK_TYPE_ID', 'IBLOCK_CODE', 'IBLOCK_NAME', 'IBLOCK_EXTERNAL_ID'];		
		foreach ($listIgnoreFieldId as $fieldId)
			unset($preparedFields[$fieldId]);

		return $preparedFields;
	}


}
