/**
 * @author marcus
 */

function mail2list_add_filter(){
	
	var tr_name = document.createElement("TR");
	tr_name.setAttribute('valign','top');
		var td_name_andor = document.createElement("TD");
			var select_input = document.createElement("SELECT");
				select_input.setAttribute('name','filters_andor[]');
				var option_input = document.createElement("OPTION");
				option_input.setAttribute('value','and');
				option_input.innerHTML = 'AND';
			select_input.appendChild(option_input);
				var option_input = document.createElement("OPTION");
				option_input.setAttribute('value','or');
				option_input.innerHTML = 'OR';
			select_input.appendChild(option_input);
		td_name_andor.appendChild(select_input);
	tr_name.appendChild(td_name_andor);
		var copio = document.getElementById('m2l_filter_filters_td').cloneNode(true);
		copio.innerHTML += ' <a href="#" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode,true);">Rimuovi...</a>';
    tr_name.appendChild(copio);
	
	document.getElementById('m2l_filter_table').appendChild(tr_name);

}