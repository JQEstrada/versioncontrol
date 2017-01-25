var progressWidth = 1;
var progressInterval;
var currentTask = "Initializing Project Data...";
var progressAnimation = false;
var progressAnimationInterval;
var fileAvailable = false;

function startBarLoadingEffect(effectBar) {
	    	// Aninmate effect bar
    	var myEffectBar = effectBar;
    	var myEffectBarWidth = parseInt(myEffectBar.css('width').replace("%", "").replace("px", ""));
    	progressAnimationInterval = setInterval ( function(){ 
    		if(myEffectBarWidth >= 99) { myEffectBarWidth = 1; }
    		myEffectBarWidth += 1;
    		myEffectBar.css('width', myEffectBarWidth + "%");
    	}, 20);

    	
    	return true;
}

function registerProgress(limitWidth, progress, speed, successCallback) {	
    var elem = document.getElementById("myBar"); 
    clearInterval(progressInterval);
    progressInterval = setInterval(frame, speed);
    function frame() {

    	if(!progressAnimation) { progressAnimation = startBarLoadingEffect($('#myEffectBar')); }
    	// Handle progress
        if (progressWidth >= limitWidth) {
            clearInterval(progressInterval);
        } else {
            progressWidth = progressWidth + progress; 
            elem.style.width = progressWidth + '%'; 
        }
        if(progressWidth >= 100){
        	registerCurrentTask("Project successfuly created.<a href='/versioncontrol/projects'><i class='glyphicon glyphicon-edit'></i></a>");
        	clearInterval(progressAnimationInterval);
        	if(typeof successCallback != 'undefined'){ successCallback(); }
        }
    }
}


function registerCurrentTask(text) {

	$('.progressInfo').html(text);
	
}


function writeProjectProcedure(handshakeCode = "", due_procedure = "") {

	var addProjectForm = $('#addProjectForm');
	var postData = addProjectForm.serialize();
	registerCurrentTask(currentTask); 

	$.ajax({
        url: "http://localhost/versioncontrol/fileapi/process/",
        type: "post",       
        dataType: 'json', 
        data: postData,
        success: function (response) {
        	console.log(response);
        	if(response.type == "success"){
				if(response.fileAvailable == true) { fileAvailable = true; }
				if(response.procedure.stepsToGo){
					var progressMade = response.procedure.stepsToGo == 1 ? 90 : ((response.procedure.stepsCompleted / (response.procedure.stepsCompleted + response.procedure.stepsToGo))*200) - 10;				
					registerProgress(progressMade, 1, 50);
				} else {
					registerProgress(100, 1, 10, function(){ activateDownloadFile(fileAvailable, response.procedure.handshake_code); });
				}
				if(typeof(response.procedure)!="undefined" && typeof(response.procedure.due_procedure)!="undefined" && response.procedure.due_procedure != "") {  
					$("#handshake_code").val(response.procedure.handshake_code);
					currentTask = response.nextTask;
					writeProjectProcedure(response.handshake_code, response.due_procedure);
				} 
        	}
        	else {
        		clearInterval(progressInterval);
        		clearInterval(progressAnimationInterval);
        		registerCurrentTask("Error: " + response.message + "<i class='glyphicon glyphicon-remove' onClick='resetForm()'></i>");
        	}
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(errorThrown);
        }

    });
}

function activateDownloadFile(fileAvailable, handshake_code) {

	if(fileAvailable==true){		
		var downloadDiv = $('#downloadDiv');
		var downloadDivLink = $(downloadDiv.find('a')[0]);
		var downloadHref = downloadDivLink.prop('href');
		var newHref = downloadHref.replace('handshake', handshake_code);
		downloadDivLink.prop('href', newHref);
		downloadDiv.show();
	}
}

function getProcedureStepCount() {
	var stepCount = 3;

	if($('#ftp_transfer:checked').length > 0) { stepCount += 2; } 
	else if($('#zip_download:checked').length > 0) { 
		stepCount++; 
		if($('#mark_first_push:checked').length > 0) { stepCount++; }
	}

	return stepCount;
}

function validateForm() {

	var validity = { status: true, message: "All fields are valid." }

	if(!$('#addProjectForm')[0].checkValidity()){
		validity.status = false;
		validity.message = "Please fill all mandatory fields.";
	}
	else if($('#copy-first-version-to-repository-1').prop('checked') && !$('input[name="transfer_type"]:checked').length) {
		// If copty to repository selected and no send method selected
		validity.status = false;
		validity.message = "Please choose method of sending files to remote server.";
	} else if($('#ftp_transfer:checked').length && ($('#ftp-server').val() == "" || $('#ftp-user-name').val() == "" || $('#ftp-password').val() == "") ) {
		validity.status = false;
		validity.message = "Please fill FTP server information.";
	}

	return validity;
}

function resetForm() {
	registerCurrentTask("");
	$('#createBtn').show(); 
	$('#progressDiv').hide();
	$('#myProgress').show();
	$('#createBtn').show();	
}

$(document).on('ready', function(){

	$("input[name='copy_first_version_to_repository[]'").change(function() {
	    if(this.checked) {
	        $(".radioinputs").css("display", "inline-block");
	    } else {	    	
	        $(".radioinputs").hide();
	    }
	});

	$("#ignore_tmp").change(function() {
	    if(this.checked) {
	        $(".ignoreinputs").show();
	    } else {	    	
	        $(".ignoreinputs").hide();
	    }
	});

	$('#createBtn').click(function(){

		var formValidation = validateForm();
		if(!formValidation.status){
			registerCurrentTask("Error: " + formValidation.message + " <i class='glyphicon glyphicon-remove' onClick='resetForm()'></i>");
			$('#progressDiv').show();
			$('#myProgress').hide();
			$('#createBtn').hide();
			return;
		}

		$(this).hide(); 
		$('#progressDiv').show();
		$("#handshake_code").val("");
		writeProjectProcedure("","");	
		var stepCount = getProcedureStepCount();	
		registerProgress(100 / stepCount,0.1,100); // Start progress for first task (33%)
	});

	$('.transfer_type').change(function(){
		var transferType = $(this).val();
		if(transferType == "zip_download") {
			$('#fileTransferZip').show();
			$('#fileTransferFTP').hide();
		}
		if(transferType == "ftp_transfer") {
			$('#fileTransferZip').hide();
			$('#fileTransferFTP').show();
		}
	});


});

