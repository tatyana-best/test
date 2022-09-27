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
			'IblockFields'     => null,			
			'ElementId' => 0,
			'IsElementMess' => ''
		];
	}
	

	public function Execute()
	{
		if (!CModule::IncludeModule("iblock"))
            return CBPActivityExecutionStatus::Closed;

		$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_*");

		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");
		

		//$arFilter = array_merge(array("IBLOCK_ID"=>$this->IblockId), $this->IblockFields);
		$arFilter = array("IBLOCK_ID"=>$this->IblockId);
		
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
		$arFields = [];
		while($ob = $res->GetNextElement()){ 
		 	$arFields[$arFields['ID']] = $ob->GetFields();  
			$this->ElementId = $arFields[$arFields['ID']]['ID'];		
			$this->IsElementMess = GetMessage('IB_ACTIVITY_GET_DATA_IS_ELEMENT_MESSAGE_YES');
		}

		AddMessage2Log("Инфоблок: " . $this->IblockId . " Массив полей: ". json_encode($arFields), "iblock");

		if($this->ElementId == 0)
			$this->IsElementMess = GetMessage('IB_ACTIVITY_GET_DATA_IS_ELEMENT_MESSAGE_NO');


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
		      	
		      	$renderEntityFields = self::renderEntityFields($currentActivity['Properties']['IblockId'], $currentValues);
		      	/*foreach($renderEntityFields as $key => $val)
		      		$currentValues['IblockFields'][$key] = $val;*/

				define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");		
				AddMessage2Log("renderEntityFields----: ". json_encode($renderEntityFields), "iblock");		      	

			}
		}
		else
		{
			$renderEntityFields = self::renderEntityFields($currentValues['IblockId'], $currentValues);
			/*foreach($renderEntityFields as $key => $val)
		      		$currentValues['IblockFields'][$key] = $val;*/
			define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");
		     AddMessage2Log("Если есть значения по умолчанию. Массив полей: ". json_encode($renderEntityFields), "iblock");
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
		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");
		foreach ($currentValues['IblockFields'] as $fieldId)
		{
			/*if (!array_key_exists($fieldId, $iblockFields))
			{
				$errors[] = [
					'code'    => 'incorrectFieldType',
					'message' => str_replace('#FIELD#', $fieldId, GetMessage("IB_ACTIVITY_GET_DATA_ERROR_FIELD_TYPE")),
				];
				break;
			}*/

			$properties['IblockFields'][$fieldId] = $iblockFields[$fieldId];
			
			
		    AddMessage2Log("properties----: ". json_encode($properties['IblockFields'][$fieldId]), "iblock");
			
		}

		if (!empty($errors))
			return false;

		$currentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($workflowTemplate, $activityName);
		$currentActivity['Properties'] = $properties;

		return true;
	}

	/*public static function getAjaxResponse($request)
	{
		$response = '';

		if (empty($request['customer_action']))
			return '';

		if ($request['customer_action'] == 'getEntityFields')
		{
			$response = self::renderEntityFields($request['iblock']);
		}

		return $response;
	}*/


	protected static function renderEntityFields($iblock, $currentValues = [])
	{
		$html = '';	

		$entityFields = self::getIblockFields($iblock);	

		foreach($entityFields as $k => $val){
			$html .= '<tr><td align="right" width="40%" class="adm-detail-content-cell-l"><span style="font-weight: bold">'.$k.'</span></td><td width="60%" class="adm-detail-content-cell-r">';

			//if (is_array($currentValues['IblockFields'][$k]) && array_key_exists($k, $currentValues['IblockFields'][$k]))
				$html .= '<textarea rows="1" cols="50" rel="tat" name="IblockFields[]" style="width: 60%" autocomplete="off">'.$currentValues['IblockFields'][$k].'</textarea>';
			//else
				//$html .= '<textarea rows="1" cols="50" name="IblockFields[]" style="width: 60%" autocomplete="off"></textarea>';

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
