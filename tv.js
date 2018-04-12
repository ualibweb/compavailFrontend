	function updateMap(data){
		$.each(data, function(key, val){
			//console.log('#'+val.computer_name+" => "+val.status);
			if (val.status == '0'){
				$('#'+val.computer_name).removeClass('closed').addClass('open');
			}
			if (val.status == '1'){
				$('#'+val.computer_name).removeClass('open').addClass('closed');
			}
		});
    
	}


document.fullscreenEnabled = document.fullscreenEnabled || document.mozFullScreenEnabled || document.documentElement.webkitRequestFullScreen;

function requestFullscreen(element) {
    if (element.requestFullscreen) {
        element.requestFullscreen();
    } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
    } else if (element.webkitRequestFullScreen) {
        element.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
    }
}

if (document.fullscreenEnabled) {
    requestFullscreen(document.documentElement);
}
