BX.ready(function(){
    var obImageView = BX.viewElementBind(
        'items',
        {showTitle: true, lockScroll: false},
        function(node){
            return BX.type.isElementNode(node) && (node.getAttribute('data-bx-viewer') || node.getAttribute('data-bx-image'));
        }
    );

    var block = BX("items");
    var link = BX("click");
    link.onclick = function(){
        block.style.display = "flex";
    };
});