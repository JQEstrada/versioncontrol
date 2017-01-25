$("#ignore_tmp").change(function() {
    if(this.checked) {
        $(".ignoreinputs").show();
    } else {	    	
        $(".ignoreinputs").hide();
    }
});

$(document).on('ready', function(){

	var ignoreTempCheck = $("#ignore_tmp");

	if(ignoreTempCheck.val()) {

		ignoreTempCheck.trigger('change');

	}

	$('div.required').removeClass('required');
	$('label').css('font-weight', 'bold');

});