	function updateMap(data){
       	
		$.each(data, function(key, val){
			//console.log('#'+val.computer_name+" => "+val.status);
			if (val.status == '0'){
				$('#'+val.computer_name).removeClass('closed').addClass('open');
			}
			if (val.status == '1'){
				$('#'+val.computer_name).removeClass('open').addClass('closed');
			}
      if (val.status == '2'){
				$('#'+val.computer_name).addClass('help');
 			}else{
        $('#'+val.computer_name).removeClass('help');
      }
      if (val.status == '3'){
				$('#'+val.computer_name).addClass('OoO');
      }else{
        $('#'+val.computer_name).removeClass('OoO');}
		});
    
	}
	