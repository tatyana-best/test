<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

class CBPDealsCompanyActivity extends CBPActivity
{
	public function __construct($name)
	{
		parent::__construct($name);
		$this->arProperties = array(
			'Title' => '',
			'CompanyId' => '',
			'CountAllDeals' => 0,
			'SumSuccessDeals' => 0,
			'CountFailDeals' => 0,
			'CountDealsInWork' => 0	
		);

		foreach (self::getPropertiesMap() as $key => $property)
		{
			$this->arProperties[$key] = $property['Default'] ?? null;
		}
	}

	public function Execute()
	{

		if (!CModule::IncludeModule("crm"))
            return CBPActivityExecutionStatus::Closed;

		$arFilter = array(
			'COMPANY_ID' => $this->CompanyId
		);

		$arSelect = array("ID", "STAGE_ID", "CLOSED", "OPPORTUNITY");

		$rsDeals = CCrmDeal::GetList(Array(), $arFilter, $arSelect);

		while($arDeal = $rsDeals->Fetch()) {
			$this->CountAllDeals ++;
			if($arDeal["CLOSED"] != "Y") 
				$this->CountDealsInWork ++;
			elseif($arDeal["CLOSED"] == "Y" && $arDeal["STAGE_ID"] == "WON")
				$this->SumSuccessDeals  = $this->SumSuccessDeals + $arDeal["OPPORTUNITY"];
			elseif($arDeal["CLOSED"] == "Y" && $arDeal["STAGE_ID"] != "WON")
				$this->CountFailDeals ++;
		}

		return CBPActivityExecutionStatus::Closed;
	}

	public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
	{
		$arErrors = array();

		if (strlen($arTestProperties["CompanyId"]) <= 0)
		{
			$arErrors[] = array(
				"code" => "emptyCode",
				"message" => GetMessage("DEALS_COMPANY_ACTIVITY_EMPTY_COMPANY_ID"),
			);
		}

		return array_merge($arErrors, parent::ValidateProperties($arTestProperties, $user));
	}

	public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = '', $popupWindow = null, $siteId = '')
    {

			if (!CModule::IncludeModule('crm'))
			{
				return '';
			}
	
			$dialog = new \Bitrix\Bizproc\Activity\PropertiesDialog(__FILE__, [
				'documentType' => $documentType,
				'activityName' => $activityName,
				'workflowTemplate' => $arWorkflowTemplate,
				'workflowParameters' => $arWorkflowParameters,
				'workflowVariables' => $arWorkflowVariables,
				'currentValues' => $arCurrentValues,
				'formName' => $formName,
				'siteId' => $siteId,
			]);
	
			$dialog->setMap(self::getPropertiesMap());
	
			return $dialog;


	}

	private static function getPropertiesMap(): array
	{

		return [
			'CompanyId' => [
				'Name' => GetMessage('DEALS_COMPANY_ACTIVITY_COMPANY'),
				'FieldName' => 'company_id',
				'Type' => 'int',
				'Required' => true,
				'AllowSelection' => true,
			],
		];
	}


	public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
	{

		$errors = [];
		$properties = [];

		$documentService = CBPRuntime::GetRuntime(true)->getDocumentService();

		foreach (self::getPropertiesMap() as $key => $property)
		{
			$properties[$key] = $documentService->GetFieldInputValue(
				$documentType,
				$property,
				$property['FieldName'],
				$arCurrentValues,
				$errors
			);
		}

		if ($errors)
		{
			return false;
		}

		$errors = self::ValidateProperties($properties, new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser));

		if ($errors)
		{
			return false;
		}

		$activity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
		$activity['Properties'] = $properties;

		return true;


	}
}
