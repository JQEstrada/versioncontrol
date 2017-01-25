var changesPresent = false;

$(document).on('ready', function(){


	$('#searchPushBtn').click(function(){

		$('.loaderDivPush').show();
		$('#pushTable').hide();
		$('#searchPushBtn').hide();
		$('#pushFeedback').html("");	
		$('.credentialsDivPush').hide();		


		$.ajax({
	        url: "http://localhost/versioncontrol/fileapi/getFilesToPushAjax/" + projectId + ".json", //Previous commmitAjax
	        type: "post",       
	        dataType: 'json',
	        success: function (response) {

	        	console.log(response);
	        	filesToPushPresent = false;
	        	$('.loaderDivPush').hide();

				if(response.type == "success" && response.files.length) {

					filesToPushPresent = true;

					createTable(response);   

				
				} else {
					$('#pushFeedback').html(response.message);

					if(!response.files.length){
						$('#pushFeedback').show();
					}
				}

	        },
	        error: function(jqXHR, textStatus, errorThrown) {
	           console.log(textStatus, errorThrown);
	        }

	    });

	});


	$('#commitBtn, #refreshCommit').click(function(){

		$('.loaderDiv').show();
		$('.commitInfo').hide();
		$('.commitButtons').hide();
		$('#commitFeedback').html("");		
		$('.credentialsDiv').hide();
		$('.buttonsDivs').show();


		$.ajax({
	        url: "http://localhost/versioncontrol/fileapi/getChangesAjax/" + projectId + ".json", //Previous commmitAjax
	        type: "post",       
	        dataType: 'json',
	        success: function (response) {

	        	console.log(response);
	        	changesPresent = false;
	        	$('.loaderDiv').hide();

				if(response.type == "success" && response.modified.length) {

					changesPresent = true;

					createTree(response);   

					showCommitOptions();

					handshake_code = response.handshake_code;
				
				} else {
					$('#commitFeedback').html(response.message);

					if(!response.modified.length){
						$('.commitButtons').show();
					}
				}

	        },
	        error: function(jqXHR, textStatus, errorThrown) {
	           console.log(textStatus, errorThrown);
	        }

	    });
	});



	$('#confirmCommit').click(function(){

		var selectedFiles = getSelectedTree($('#jstree'));  

		var dataOptions = {"selectedFiles": selectedFiles, "pushFiles": false, "comments": $('#commit-comments').val() };

		if(!selectedFiles.length) { 
			alert("No files selected.");
			return;
		}



		$.ajax({
	        url: "http://localhost/versioncontrol/fileapi/confirmCommitAjax/" + handshake_code , //Previous commmitAjax
	        type: "post",       
	        dataType: 'json',
	        data: JSON.stringify(dataOptions),
	        success: function (response) {
	        	console.log(response);
				$('#commitBtn').click();

	        },
	        error: function(jqXHR, textStatus, errorThrown) {
	           console.log(textStatus, errorThrown);
	        }

	    });
	});

	$('#confirmPush').click(function() {

		if(!$('#ftp-server').val() || !$('#ftp-user-name').val() || !$('#ftp-password').val()) { 
			alert("FTP credentials incomplete.");
			return;
		}

		var selectedFiles = getSelectedTree($('#jstree')); 
		var dataOptions = {"selectedFiles": selectedFiles, "pushFiles": true, "ftpServer": $('#ftp-server').val(), "ftpUsername": $('#ftp-user-name').val(), "ftpPassword": $('#ftp-password').val()}; 

		$.ajax({
	        url: "http://localhost/versioncontrol/fileapi/confirmCommitAjax/" + handshake_code , //Previous commmitAjax
	        type: "post",       
	        dataType: 'json',
	        data: JSON.stringify(dataOptions),
	        success: function (response) {
	        	console.log(response);
				$('#commitBtn').click();

	        },
	        error: function(jqXHR, textStatus, errorThrown) {
	           console.log(textStatus, errorThrown);
	        }

	    });

	});


	$('#confirmIndividualPush').click(function() {


		$('.pushButton').hide();

		$('#pushLoader').show();

		if(!$('#ftp-server-push').val() || !$('#ftp-user-name-push').val() || !$('#ftp-password-push').val()) { 
			alert("FTP credentials incomplete.");
			return;
		}

		var selectedFiles = getSelectedPushFiles(); 
		var dataOptions = {"selectedFiles": selectedFiles, "ftpServer": $('#ftp-server-push').val(), "ftpUsername": $('#ftp-user-name-push').val(), "ftpPassword": $('#ftp-password-push').val()}; 

		if(!selectedFiles.length) { 

			alert("No files chosen");

			return;

		}

		$.ajax({
	        url: "http://localhost/versioncontrol/fileapi/pushFileListAjax/" + projectId + ".json" , 
	        type: "post",       
	        dataType: 'json',
	        data: JSON.stringify(dataOptions),
	        success: function (response) {
	        	console.log(response);
				$('#searchPushBtn').click();

	        },
	        error: function(jqXHR, textStatus, errorThrown) {
	           console.log(textStatus, errorThrown);
	        }

	    });

	});


	$('#pushBtn').click(function(){

		$('.credentialsDiv').show();

		$('.buttonsDivs').hide();

		adjustCommitInfoHeight();

		if(projectFtpServer) { $('#ftp-server').val(projectFtpServer); }

		if(projectFtpUser) { $('#ftp-user-name').val(projectFtpUser); }

	});

	$('#back').click(function(){
		$('#commitBtn').click();
	});


	$('#selectAllPushes').click(function() { toggleAllPushCheckBoxes(this) });

	// Initial Triggers

	$('#commitBtn').click();

	$('#searchPushBtn').click();

});


function clearTables(){
	$("#pushTable .dataRow").remove();
}

function createTable(data) {


	clearTables();

	var files = data.files;
	var additionTable = $('#pushTable tbody');

	for(var key in files) {

		var file = files[key];


		additionTable.append('<tr class="dataRow">\
							<td>' + file.path + '</td>\
							<td>' + file.name + '</td>\
							<td>' + file.extension + '</td>\
							<td>' + file.last_modified + '</td>\
							<td><input type="checkbox" class = "filePushCheck" name = "filePushCheck" value = "' + file.id + '" /></td></tr>');
	}
	
	additionTable.append('<tr class="dataRow"><td colspan="5" style="text-align:center"><button class="pushButton" id="pushSelected" style="width:200px">Push Selected</button><button class="pushButton" id="ignoreAll" style="width:200px">Ignore All</button><button class="pushButton" id="downloadAll" style="width:200px">Download All</button><img id="pushLoader" src="/versioncontrol/img/ajax-loader.gif" /></td></tr>');


	$("#pushTable").show();

	$('#backPush').click(function(){

		$('.credentialsDivPush').hide();

		$('#searchPushBtn').click();

	});

	$('#pushSelected').click(function(){ 

		$('#pushTable .dataRow').last().remove();

		$('.credentialsDivPush').show();

		if(projectFtpServer) { $('#ftp-server-push').val(projectFtpServer); }

		if(projectFtpUser) { $('#ftp-user-name-push').val(projectFtpUser); }

	});


	$('#ignoreAll').click(function() {

		$('.pushButton').hide();

		$('#pushLoader').show();

		$.ajax({
	        url: "http://localhost/versioncontrol/fileapi/ignoreAllAjax/" + projectId + ".json" , 
	        type: "post",       
	        dataType: 'json',
	        success: function (response) {
	        	console.log(response);
				$('#searchPushBtn').click();

	        },
	        error: function(jqXHR, textStatus, errorThrown) {
	           console.log(textStatus, errorThrown);
	        }

	    });

	});


	$('#downloadAll').click(function() {

		$('.pushButton').hide();

		$('#pushLoader').show();

		$.ajax({
	        url: "http://localhost/versioncontrol/fileapi/downloadAllAjax/" + projectId + ".json" , 
	        type: "post",       
	        dataType: 'json',
	        success: function (response) {
	        	console.log(response);
	        	if(response.type == "success"){

					window.location.href = 'http://localhost/versioncontrol/fileapi/download-files/' + response.projectid;
					$('#searchPushBtn').click();

	        	}

	        },
	        error: function(jqXHR, textStatus, errorThrown) {
	           console.log(textStatus, errorThrown);
	        }

	    });

	});
}

function toggleAllPushCheckBoxes(toggleCheckBox) {

	var toggleCheckBoxChecked = $(toggleCheckBox).prop('checked');

	var filePushesChecks = $('.filePushCheck');

	filePushesChecks.each(function() {

		var filePush = $(this);
		filePush.prop('checked', toggleCheckBoxChecked);

	});

}


function getSelectedPushFiles() {

	var filePushIds = [];

	$('.filePushCheck:checked').each(function(){

		filePushIds.push($(this).val());

	});

	return filePushIds;

}


function initializeEmptyTree(treeId) {

	window['jsTreeStructure'] = []; // JS complete treeId structure, array of arrays

	var tree = $(treeId);
	tree.jstree("destroy").empty();
	tree.jstree({
	    'core' : {
	        'check_callback' : true
	    },

		types: {
		"root": {
		  "icon" : "glyphicon glyphicon-leaf"
		},
		"child_added": {
		  "icon" : "glyphicon glyphicon-plus"
		},
		"child_modified": {
		  "icon" : "glyphicon glyphicon-pencil"
		},
		"child_deleted": {
		  "icon" : "glyphicon glyphicon-trash"
		},
		"default" : {
		}
		},

	    'plugins': ['types', 'checkbox'],

	});

	return tree;
}


function createTree(data) {

	console.log(data);
	var modifiedFiles = data.modified;

	var jsTree = initializeEmptyTree('#jstree');

	for(var key in modifiedFiles) {

		var modifiedFile = getFixedPathFile(modifiedFiles[key], data.projectPath);

		var pathCreated = createPathInTree(modifiedFile.relativePath, jsTree, jsTreeStructure);
		
		jsTreeStructure = pathCreated.jsTreeStructure;

		var fileCreated = createFile(modifiedFiles[key].fileid, modifiedFiles[key].action, pathCreated.baseFolder, modifiedFile.name + "." + modifiedFile.extension, jsTree, jsTreeStructure);

	}

}

function createFile(fileId, actionType, baseFolderId, fileName, jsTree, jsTreeStructure) {

	var currentNode = baseFolderId == "" ? null : jsTree.jstree(true).get_node("[id='" + baseFolderId + "']");		

	var newNodeId = jsTree.jstree("create_node", currentNode, { type: "child_" + actionType, text: fileName, data: { fileId: fileId } }, "last", function (node) {

	    var i = 0;

	    for(var name in this._model.data){

			if(i == Object.keys(this._model.data).length - 1){ 

			    jsTreeStructure.push({ name: fileName, id: name, depthLevel: -1, type: "file" });

				return name;
				
				break;
			}

			i++;

		}

	});	
	
	return { jsTreeStructure: jsTreeStructure, baseFolderId: newNodeId };
}

function createPathInTree(fileStruct, jsTree, jsTreeStructure) {

	var depth = 0; // Level of depth for each folder to be created
	var baseFolder = ""; // Position of the first existing folder in the new path. Default, root folder
	var foldersToCreate = [];
	var foldersNeedCreation = false;

	for(var key in fileStruct) {

		var folder = fileStruct[key];
		foldersNeedCreation = false;

		if(!jsTreeStructure.length) {  
			// There is no path in tree structure yet, and this
			// is the first level of depth, so create first folder
			// in root and flag folders for creation

			var result = createFolder(baseFolder, folder, depth, jsTree, jsTreeStructure);
			baseFolder = result.baseFolderId;
			jsTreeStructure = result.jsTreeStructure;
			foldersNeedCreation = true;
		} 
		else {

			var folderExists = false;

			if(!foldersNeedCreation) { // If it is not known if next folder needs creation

				for(var keyStruct in jsTreeStructure){

					var path = jsTreeStructure[keyStruct];	

					// If path in structure is the same as folder being searched
					// set this folder as already existing base folder for next
					// creation

					if(path.name == folder && path.depthLevel == depth) {
						// Folder already exists, change first existing folder to this
						baseFolder = path.id;
						folderExists = true;
						break;
					}
			
				}


			}

			if(!folderExists) {

				var result = createFolder(baseFolder, folder, depth, jsTree, jsTreeStructure);
				baseFolder = result.baseFolderId;
				jsTreeStructure = result.jsTreeStructure;
				foldersNeedCreation = true;
			
			}

		}

		depth++;		
		
	}	

	return {baseFolder: baseFolder, jsTreeStructure: jsTreeStructure};

}


/*
@param jsTreeStructure: array of paths of tree structures. A tree structure is composed by {file/folder name, depth level, node id} 
*/
function createFolder(baseFolderId, folder, folderDepth, jsTree, jsTreeStructure){

	var currentNode = null; // root

	if(baseFolderId) { // If current path is not root, get folder
		
		//currentNode = getNodeById(jsTreeStructure, baseFolderName, depth);	
		currentNode = jsTree.jstree(true).get_node("[id='" + baseFolderId + "']");	

	}		

	var newNodeId = jsTree.jstree("create_node", currentNode, { text: folder, state: {opened: true} }, "last", function (node) {

	    var i = 0;

	    for(var name in this._model.data){

			if(i == Object.keys(this._model.data).length - 1){ 

			    jsTreeStructure.push({ name: folder, id: name, depthLevel: folderDepth, type: "folder" });

				return name;
				
				break;
			}

			i++;

		}

	});	
	
	return { jsTreeStructure: jsTreeStructure, baseFolderId: newNodeId };

}

function getFixedPathFile(file, projectPath) {

		var modifiedFile = file;
		var absolutePath = modifiedFile.path;
		var relativePath = absolutePath.replace("file:///" + projectPath, "");
		//if(relativePath==""){ relativePath = "\\"; }
		modifiedFile["relativePath"] = relativePath.split("\\"); // Remove array first blank position
		modifiedFile["relativePath"].shift();

		return modifiedFile;
}

function getSelectedTree (jstree) {

	var selectedNodes = jstree.jstree().get_selected(true);
	var selectedFiles = [];

	for(var key in selectedNodes) {
		var selectedNode = selectedNodes[key];
		console.log(selectedNode);
		if(selectedNode.type.indexOf('child_') != -1) {
			selectedFiles.push(selectedNode.data.fileId);
		}
	}
	
	return selectedFiles;

}

function showCommitOptions() {
				
	$('.commitButtons').hide();

	$('.commitInfo').show();

	adjustCommitInfoHeight();

}

function adjustCommitInfoHeight(){
	
	var leftCommitHeight = $('.leftColumn').height();

	var rightCommitHeight = $('.rightColumn').height();

	var commitOptionsHeight = $('.commitCommentsDiv').height();

	var commitHeight = rightCommitHeight > leftCommitHeight ? rightCommitHeight : leftCommitHeight;

	$('.commitInfo').height(commitHeight + commitOptionsHeight);

	$('.rightColumn').css("left", $('.leftColumn').width() + 'px');

}