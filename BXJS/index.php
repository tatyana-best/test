<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

CUtil::InitJSCore(array('custom_main'));

echo "----------"."<br>";
echo Loc::getMessage("MY_MESSAGE1");
echo "----------"."<br>";
?>
<div id="div">
    <?echo GetMessage("MY_MESSAGE1")?>
</div>
<?

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');


