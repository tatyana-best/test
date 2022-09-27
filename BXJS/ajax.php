<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

CUtil::InitJSCore(array('custom_ajax'));

?>
<div id="block">
</div>
<form id="my_form" method="post" action="">
    <input id="name" type="text" name="name">
    <input id="submit" type="submit" value="Отправить">
</form>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');

