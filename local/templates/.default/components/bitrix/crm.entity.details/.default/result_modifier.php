<?
foreach($arResult["EDITOR"]["ENTITY_DATA"]["LAST_COMPANY_INFOS"] as $key => $item) {
	$arResult["EDITOR"]["ENTITY_DATA"]["LAST_COMPANY_INFOS"][$key]["title"] = $item["id"].". ". $item["title"];
}
