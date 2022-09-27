BX.ready(function()
{
    var message = BX.message("MY_MESSAGE1");
    var s = BX.create('span', {text: 'someText', attrs: {id: 'elemId'}});

    BX('div').innerHTML = message;
    alert(message);
});

