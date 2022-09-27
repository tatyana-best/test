<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

CUtil::InitJSCore(array('custom_viewer'));

?>
    <div class="click" id="click">
        Click me
    </div>
    <div class="items" id="items">
        <?for ($i = 1; $i <= 7; $i++):?>
        <div class="item">
            <img
                    class="data-bx-image"
                    src="/local/images/<?=$i;?>.jpg"
                    data-bx-viewer="image"
                    data-bx-src="/local/images/<?=$i;?>.jpg"
                    data-bx-download="/local/images/<?=$i;?>.jpg"
                    data-bx-viewer="image"
                    onload="this.parentNode.className='item';"
            >
        </div>
        <?endfor;?>
    </div>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');