<?
foreach($arResult["GRID"]["ROWS"] as $key => $item) {
	$arResult["GRID"]["ROWS"][$key]["PRODUCT_NAME"] = "*".$item["PRODUCT_NAME"];
}