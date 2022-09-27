<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

CUtil::InitJSCore(array('custom_popup'));
CUtil::InitJSCore(array('custom_ajax'));

$APPLICATION->SetTitle("PopUp");

CJSCore::Init(array("popup"));
?>
    <div id="hideBlock" style="display:none;">
        <div id="block">
        </div>
        <form id="my_form" method="post" action="">
            <input id="name" type="text" name="name">
            <input id="submit" type="submit" value="Отправить">
        </form>
    </div>
    <div class="css_popup">click Me</div>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');

