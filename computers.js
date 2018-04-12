$(document).ready(function(){
	$('head').append('<meta name="viewport" content="width=1000px">');
	
	$('.comps-nav-btn').each(function(i, v){
		if ($(this).hasClass('linked')){
			$(this).click(function(){
				var loc = $(this).attr('scope') == 0 ? $(this).attr('id') : $(this).attr('scope')+','+$(this).attr('id');
				location.href = '/computers?query=' + $(this).attr('rel') + '&loc=' + loc + '&view=' + Computers.view;
			});
			$(this).keydown(function(e){		
				if (e.keyCode == 13){
					var loc = $(this).attr('scope') == 0 ? $(this).attr('id') : $(this).attr('scope')+','+$(this).attr('id');
					location.href = '/computers?query=' + $(this).attr('rel') + '&loc=' + loc + '&view=' + Computers.view;		
				}
			});
		}
	});
	
	$('#back').click(function(){
		location.href = '/computers?query=nav&loc=' + $(this).attr('rel') + '&view=' + Computers.view;
	});
	
	//---> ajax page updates
	setInterval(update, 8000);
	function update(){
		
		$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
			console.log(textStatus);
			console.log(errorThrown);
			console.log(XMLHttpRequest.responseText);
		  }});
		
		$.getJSON('/inc/compavail/Computers.php', {query: Computers.q, loc: Computers.loc}, function(data){
			
			if (Computers.q == 'map_update'){
				updateMap(data);
			}
			else{
				updateNav(data);
			}
				
		});
	}
	
	function updateNav(data){
		$.each(data, function(key, val){
			$('#'+val.id+' .pc_a').html(val.pc_a);
			$('#'+val.id+' .mac_a').html(val.mac_a);
		});
		
	}
	

  
});