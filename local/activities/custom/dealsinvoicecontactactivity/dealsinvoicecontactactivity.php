<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

class CBPDealsInvoiceContactActivity extends CBPActivity
{
	public function __construct($name)
	{
		parent::__construct($name);
		$this->arProperties = array(
			'Title' => '',
			'ContactId' => '',
			'CountDealsInWork' => 0,
			'CountFailDeals' => 0,
			'CountSuccessDeals' => 0,
			'SumSuccessDeals' => 0,
			'SumFailInoice' => 0,
			'SumSuccessInoice' => 0
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
			'CONTACT_ID' => $this->ContactId
		);

		$arSelect = array("ID", "STAGE_ID", "CLOSED", "OPPORTUNITY");

		$rsDeals = CCrmDeal::GetList(Array(), $arFilter, $arSelect);

		while($arDeal = $rsDeals->Fetch()) {
			if($arDeal["CLOSED"] != "Y") 
				$this->CountDealsInWork ++;
			elseif($arDeal["CLOSED"] == "Y" && $arDeal["STAGE_ID"] == "WON"){
				$this->SumSuccessDeals  = $this->SumSuccessDeals + $arDeal["OPPORTUNITY"];
				$this->CountSuccessDeals ++;
			}
			elseif($arDeal["CLOSED"] == "Y" && $arDeal["STAGE_ID"] != "WON")
				$this->CountFailDeals ++;
			
		}

		$Filter = array(
			'=CONTACT_ID' => $this->ContactId
		);

		$factory = Bitrix\Crm\Service\Container::getInstance()->getFactory(\CCrmOwnerType::SmartInvoice);

		$allItems = $factory->getItems(['select'=> array('ID', 'CONTACT_ID', 'STAGE_ID', 'OPPORTUNITY'), 'filter'=>$Filter]);

		foreach($allItems as $item){
			if($item->get('STAGE_ID') == 'DT31_1:P'){
				$this->SumSuccessInoice = $this->SumSuccessInoice + $item->get('OPPORTUNITY');
			} elseif($item->get('STAGE_ID') == 'DT31_1:D'){
				$this->SumFailInoice = $this->SumFailInoice + $item->get('OPPORTUNITY');
			}
		}

		return CBPActivityExecutionStatus::Closed;
	}

	public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
	{
		$arErrors = array();

		if (strlen($arTestProperties["ContactId"]) <= 0)
		{
			$arErrors[] = array(
				"code" => "emptyCode",
				"message" => GetMessage("DEALS_INVOICE_CONTACT_ACTIVITY_EMPTY_CONTACT_ID"),
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
			'ContactId' => [
				'Name' => GetMessage('DEALS_INVOICE_CONTACT_ACTIVITY_CONTACT_ID'),
				'FieldName' => 'contact_id',
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
