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
			'IblockElementId' => 0,
			'IblockId'         => null,
			'IblockOrder' => null,
			'IblockFields'     => array(),			
			'IblockFieldsResult' => array()
		];


	}
	

	public function execute()
	{
		if (!CModule::IncludeModule("iblock"))
            return CBPActivityExecutionStatus::Closed;

		$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_*");

		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");
		

		$arFilter = array_merge(array("IBLOCK_ID"=>$this->IblockId), $this->IblockFields);
		
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
		$result_fields = [];
		if($ob = $res->GetNextElement(true, false)){ 
		 	$arFields = $ob->GetFields(); 
		 	$arProps = $ob->GetProperties(); 
		 	$IblockElementId = $arFields['ID'];	
		}

		foreach($arFields as $key => $value){
			if(is_array($value['VALUE']))
			{
				$result_fields[$key] = implode('; ', $value['VALUE']);				
			}
			else
			{
				$result_fields[$key] = $value;				
			}
		}
		
		foreach($arProps as $key => $value){
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

		/*foreach($arFields as $key => $value){
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
		
		foreach($arProps as $key => $value){
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
		}*/

		
		//$this->IblockFieldsResult = $result_fields;

		//$this->SetPropertiesTypes($this->IblockFieldsResult);

		//AddMessage2Log("Результат: " . json_encode($this->IblockFieldsResult), "iblock");

		AddMessage2Log("ID элемента: " . $IblockElementId. " Все данные о найденном элементе: ". implode(', ',$result_fields), "iblock");

		$this->IblockElementId = $IblockElementId;


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
		      	//echo "<pre>cur".print_r($currentActivity['Properties']['IblockFields'],true)."</pre>";     	

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
