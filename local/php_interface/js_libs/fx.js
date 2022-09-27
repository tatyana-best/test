BX.ready(function(){
    var banner = BX("block");
    var button = BX("submit");
    var easing = new BX.easing({
        duration : 1500,
        start : { height : 0, opacity : 0 },
        finish : { height : 100, opacity: 100 },
        transition : BX.easing.transitions.quart,
        step : function(state){
            banner.style.height = state.height + "px";
            banner.style.opacity = state.opacity/100;
        },
        complete : function() {
            banner.style.border = "2px solid red";
            button.value = "Показать еще анимацию";
            button.style.background = "red";
            button.style.cursor = "pointer";
        }
    });
    easing.animate();

    var eas = new BX.easing({
        duration : 2500,
        start : { borderRadius: 0, margin: 0 },
        finish : { borderRadius : 50, margin:  200 },
        step : function(state){
            banner.style.borderRadius = state.borderRadius + "%";
            banner.style.margin = state.margin/5 + "px";
        },
        complete : function() {
            banner.style.border = "none";
            button.value = "Отправить";
            button.style.background = "white";
            button.style.cursor = "pointer";
        }
    });
    var elem = BX("submit");
    elem.onclick = function(){
        eas.animate();
    };

});