var node = 1;
			var edge = 1;
			function add_attrNodo_fields() {
   		 	node++;
    		var objTo = document.getElementById('node_fileds')
    		var divtest = document.createElement("div");
    		divtest.innerHTML = '<div class="centralBlock"><div class="newrow">Attributo nodo ' + node +':</div><div class="content"><span>Nome attributo nodo: <input type="text" name="nomeAttr' +node+'" value="" /></span><span>Valore nodo: <input type="text" name="valAttr'+node+'" value="" /></span><span> Regola : <input type="text" name="ruleNode'+node+'" value="" /></span></div></div>';
    		objTo.appendChild(divtest);
			}  
			function add_attrArco_fields() {
   		 	edge++;
    		var objTo = document.getElementById('edge_fileds')
    		var divtest = document.createElement("div");
    		divtest.innerHTML = '<div class="centralBlock"><div class="newrow">Attributo arco ' + edge +':</div><div class="content"><span>Nome attributo arco: <input type="text" name="nomeArco' +edge+'" value="" /></span><span>Valore arco: <input type="text"  name="valArco'+edge+'" value="" /></span><span> Regola: <input type="text"  name="ruleEdge'+edge+'" value="" /></span></div></div>';
    		objTo.appendChild(divtest);
			}  
			function showCreation(){
				$divC=document.getElementById('creation');
				$divE=document.getElementById('elimination');
				$divP=document.getElementById('path');
				$divH=document.getElementById('head_menu');
				$divB=document.getElementById('back_arrow');
				$divC.style="display: block;";
				$divB.style="display: block; float: top;";
				$divE.style="display: none;";
				$divP.style="display: none;";
				$divH.style="display: none;";
			}
			function showElimination(){
				$divC=document.getElementById('creation');
				$divE=document.getElementById('elimination');
				$divP=document.getElementById('path');
				$divH=document.getElementById('head_menu');
				$divB=document.getElementById('back_arrow');
				$divC.style="display: none;";
				$divE.style="display: block;";
				$divP.style="display: none;";
				$divH.style="display: none;";
				$divB.style="display: block; float: top;"
			}
			function showPath(){
				$divC=document.getElementById('creation');
				$divE=document.getElementById('elimination');
				$divP=document.getElementById('path');
				$divH=document.getElementById('head_menu');
				$divB=document.getElementById('back_arrow');
				$divC.style="display: none;";
				$divE.style="display: none;";
				$divP.style="display: block;";
				$divH.style="display: none;";
				$divB.style="display: block;float: top;";
			}
			function showMenu(){
				$divC=document.getElementById('creation');
				$divE=document.getElementById('elimination');
				$divP=document.getElementById('path');
				$divH=document.getElementById('head_menu');
				$divB=document.getElementById('back_arrow');
				$divC.style="display: none;";
				$divE.style="display: none;";
				$divP.style="display: none;";
				$divH.style="display: block;";
				$divB.style="display: none;";
			}