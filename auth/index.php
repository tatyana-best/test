<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/about/auth/index.php");

if (is_string($_REQUEST["backurl"]) && mb_strpos($_REQUEST["backurl"], "/") === 0)
{
	LocalRedirect($_REQUEST["backurl"]);
}

$APPLICATION->SetTitle(GetMessage("AUTH_TITLE"));
?>
<p><?=GetMessage("AUTH_SUCCESS")?></p>

<p><a href="<?=SITE_DIR?>"><?=GetMessage("AUTH_BACK")?></a></p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>