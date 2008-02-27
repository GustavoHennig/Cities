<html>
<head>
<?php
require_once dirname(__FILE__).'/../lib/config.php';
echo '
	<!-- jsProgressBarHandler prerequisites : prototype.js -->
	<script type="text/javascript" src="'. $config->www_root .'lib/jsprogressbar/js/prototype/prototype.js"></script>

	<!-- jsProgressBarHandler core -->
	<script type="text/javascript" src="'. $config->www_root .'lib/jsprogressbar/js/bramus/jsProgressBarHandler.js"></script>
	
	<script type="text/javascript" src="'. $config->www_root .'lib/ajaxlib.js"></script>
';
 ?>
</head>
<body>
<div id="teste" style="visibility: hidden" >a</div>
<span class="progressBar" id="prgb_element">40</span>
</body>
</html>

<script language="JavaScript" >

   function Dados(valor) {
      //verifica se o browser tem suporte a ajax
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
	  //se tiver suporte ajax
	  if(ajax) {
	     //deixa apenas o elemento 1 no option, os outros são excluídos
		 document.forms[0].listCidades.options.length = 1;
	     
		 idOpcao  = document.getElementById("opcoes");
		 
	     ajax.open("POST", "cidades.php", true);
		 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		 
		 ajax.onreadystatechange = function() {
            //enquanto estiver processando...emite a msg de carregando
			if(ajax.readyState == 1) {
			   idOpcao.innerHTML = "Carregando...!";   
	        }
			//após ser processado - chama função processXML que vai varrer os dados
            if(ajax.readyState == 4 ) {
			   if(ajax.responseXML) {
			      processXML(ajax.responseXML);
			   }
			   else {
			       //caso não seja um arquivo XML emite a mensagem abaixo
				   idOpcao.innerHTML = "--Primeiro selecione o estado--";
			   }
            }
         }
		 //passa o código do estado escolhido
	     var params = "estado="+valor;
         ajax.send(params);
      }
   }
   
   function processXML(obj){
      //pega a tag cidade
      var dataArray   = obj.getElementsByTagName("cidade");
      
	  //total de elementos contidos na tag cidade
	  if(dataArray.length > 0) {
	     //percorre o arquivo XML paara extrair os dados
         for(var i = 0 ; i < dataArray.length ; i++) {
            var item = dataArray[i];
			//contéudo dos campos no arquivo XML
			var codigo    =  item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
			var descricao =  item.getElementsByTagName("descricao")[0].firstChild.nodeValue;
			
	        idOpcao.innerHTML = "--Selecione uma das opções abaixo--";
			
			//cria um novo option dinamicamente  
			var novo = document.createElement("option");
			    //atribui um ID a esse elemento
			    novo.setAttribute("id", "opcoes");
				//atribui um valor
			    novo.value = codigo;
				//atribui um texto
			    novo.text  = descricao;
				//finalmente adiciona o novo elemento
				document.forms[0].listCidades.options.add(novo);
		 }
	  }
	  else {
	    //caso o XML volte vazio, printa a mensagem abaixo
		idOpcao.innerHTML = "--Primeiro selecione o estado--";
	  }	  
   }
   
   var teste = document.getElementById('teste');
   
   teste.innerHTML = "iniciando..";
   
   
   setTimeout("refresh_prb()", 1000);
   
   function refresh_prb(){
   		var sp = document.getElementById('teste');
   		//var val;
   		alert(sp.innerHTML);
   		getCityProgress(1675, sp);
   		
   		
   		alert(sp.innerHtml);
   		myJsProgressBarHandler.setPercentage('prgb_element', sp.innerHTML);
   		//alert('Olah');
   }
   
  // teste.innerHTML = "HEHE";
   
   
   </script>
   
<?php











?>