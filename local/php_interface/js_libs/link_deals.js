//console.log("-----------------------Ready-----------------------------");//ПОДКЛЮЧЕНА

BX.ready(function(){
	BX.addCustomEvent("onEntityDetailsTabShow", function(p) {

		var href= window.location.href,
			matches
			;
		//if(matches=href.match(/\/crm\/company\/details\/(\d+)/)) {
			console.log('----------------------');
			console.log(p);
			console.log('----------------------');
			if(p._id == 'tab_deal'){
				function deal (){
					var elem = document.querySelector(".crm-menu-bar-btn.btn-new");
					//console.log(elem);//ВСЕ ПРАВИЛЬНО, ПОЛУЧИЛИ НУЖНЫЙ ЭЛЕМЕНТ
					//elem.addEventListener("click", dealCreate, false);
	
					elem.setAttribute('href', "/company/personal/user/1/");
					elem.removeAttribute("onclick");
	
					//import {Event} from 'main.core';	ТАК ВЫВОДИТСЯ ОШИБКА
					//Event = require('main.core');	//ТАК ОШИБКИ НЕТ, НО НЕ РАБОТАЕТ, 
					//Event.unbindAll(elem, 'click'); //ВСЕ РАВНО ПОЯВЛЯЕТСЯ ОКНО СОЗДАНИЯ СДЕЛКИ
					//ПЫТАЛАСЬ ТАКЖЕ ПОСТАВИТЬ ЭТИ ДВЕ СТРОЧКИ ПЕРЕД elem.addEventListener - НЕ РАБОТАЕТ
				}
				setTimeout(deal, 2000);
			}
		//}
	});
});

function dealCreate() {
	//alert("111"); //РАБОТАЕТ!!!
	window.location.href = '/company/personal/user/1/';
};

