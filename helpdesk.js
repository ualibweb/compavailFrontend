	function updateMap(data){
    var sound = $('<embed id="bingbong" height="0" width="0" src="/inc/compavail/bingbong.mp3" />');
    	
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
        $('body').append(sound);	
 			}else{
        $('#'+val.computer_name).removeClass('help');
      }
      if (val.status == '3'){
				$('#'+val.computer_name).addClass('OoO');
      }else{
        $('#'+val.computer_name).removeClass('OoO');}
		});
    
	}