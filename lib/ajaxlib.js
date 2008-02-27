

function getCityProgress(target, idcity, objResult){

	var ajax = CreateAjaxObj();
   
    ajax.onreadystatechange = function() {
            //enquanto estiver processando...emite a msg de carregando
			if(ajax.readyState == 1) {
			   //teste.innerHTML = "Carregando...";   
	        }
	        
			//após ser processado - chama função processXML que vai varrer os dados
            if(ajax.readyState == 4 ) {
            
	            if(ajax.responseText){
	            	try{
	            		//
	            		objResult.innerHTML = ajax.responseText;
	            	}catch(e){
	            	
            		}
            	} 
			   //teste.innerHTML = "dgd";
			   
            }
            //teste.innerHTML = "dgd2";
         }
   

    ajax.open("POST", target, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
   //ajax.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
	ajax.send("idcity=" + idcity);
}

function CreateAjaxObj(){
	
	var ajax = null;
	
	try {
         ajax = new ActiveXObject("Microsoft.XMLHTTP");
      } 
      catch(e) {
         try {
            ajax = new ActiveXObject("Msxml2.XMLHTTP");
         }
	     catch(ex) {
            try {
               ajax = new XMLHttpRequest();
            }
	        catch(exc) {
               alert("Esse browser não tem recursos para uso do Ajax");
               ajax = null;
            }
         }
      }
      
      return ajax;
}
