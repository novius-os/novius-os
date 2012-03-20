// initialise menu
jQuery(document).ready(function() {
    jQuery('#header ul').superfish();
    jQuery('#header_home ul').superfish();
    jQuery('#sidebar ul').superfish({
        'animation' : {height : 'show'},   // slide-down effect without fade-in
        delay       : 500,
        autoArrows  : true
    });
    auto_height_content();
});

function auto_height_content(){
	var  screenH = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
        //Non-IE
        screenH = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        //IE 6+ in 'standards compliant mode'
        screenH = document.documentElement.clientHeight;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        //IE 300 compatible
        screenH = document.body.clientHeight;
    }
	var $height_content = $('#container').height()+$('#footer').height();
	if($height_content < screenH)
	{
		var min_height = screenH - $('#footer').height();
		$('#container').css({minHeight:min_height+'px'});
	}
}