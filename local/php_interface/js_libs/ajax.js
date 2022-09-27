BX.ready(function() {
    function ajaxTest(){
        const form = BX("my_form");
        const name = form.querySelector('[name="name"]');
        const mydata = {
            name: name.value,
        };
        form.addEventListener('submit', getFormValue);
        function getFormValue(Event) {
            Event.preventDefault();
        }
        BX.ajax({
            url: '/local/php_interface/js_libs/ajax.php',
            method: 'POST',
            dataType: 'json',
            data: mydata,
            onsuccess: function ($data) {
                console.log($data);
                var getData = JSON.parse(JSON.stringify($data));
                if(getData.NAME) {
                    BX("block").innerHTML = getData.TEXT + getData.NAME;
                    BX.removeClass("block", getData.CLASS1);
                    BX.addClass("block", getData.CLASS2);
                } else
                {
                    BX.removeClass("block", getData.CLASS2);
                    BX.addClass("block", getData.CLASS1);
                    BX("block").innerHTML = getData.TEXT;
                }
            },
            onfailure: function ($data) {
                console.error();
            }
        });
    }
    ajaxTest();

    var elem = BX("submit");
    elem.onclick = function(){
        ajaxTest();
        return false;
    };
});