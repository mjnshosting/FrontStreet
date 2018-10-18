$('#feedback-div').hide();

function create_user_db() {
        $.ajax({
            url: 'db_create.php',
            type: 'POST',
	    data: '',
        });
}

$("form#uploadForm").submit(function(event){
	event.preventDefault();
	var feedback = document.getElementById("feedback-conf");
  	var formData = new FormData($(this)[0]);

//	if ($(content_type).val() && $(ad_type).val() && $(start_date).val() && $(end_date).val() && $(fileToUpload).val() != '') {
	  $.ajax({
	    url: 'index.php?action=upload',
	    type: 'POST',
	    data: formData,
	    cache: false,
	    contentType: false,
	    processData: false,
	    success: function (returnFormData) {
			$('#feedback-conf').html(returnFormData);

	    }
	  });
/**
	} else {
		feedback.innerHTML = "Please fill in all fields";
	}
**/


	  return false;
	});


function genRegular(x) {
	var regularchar = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	var text = "";

	for (var i = 0; i < x; i++)
		text += regularchar.charAt(Math.floor(Math.random() * regularchar.length));
	return text;
}

function auto_generate_input(page) {

//For registration page
	if (page == 0) {
		var autoGenName = "fsap" + genRegular(3);
		var autoGenPass = "fsappw" + genRegular(4);
		document.getElementById("login_input_username").value = autoGenName;
		document.getElementById("login_input_password_new").setAttribute("type", "text");
		document.getElementById("login_input_password_repeat").setAttribute("type", "text");
		document.getElementById("login_input_password_new").value = autoGenPass;
		document.getElementById("login_input_password_repeat").value = autoGenPass;
	}

//For configuration page
	if (page == 1) {
		var autoGenName = "gbru_" + genRegular(8);
		var autoGenPass = "gbrpw_" + genRegular(15);
		var autoGenDB = "gbrdb_" + genRegular(8);
		document.getElementById("config_input_gb_db_user").value = autoGenName;
		document.getElementById("config_input_gb_db_password").setAttribute("type", "text");
		document.getElementById("config_input_gb_db_password_repeat").setAttribute("type", "text");
		document.getElementById("config_input_gb_db_password").value = autoGenPass;
		document.getElementById("config_input_gb_db_password_repeat").value = autoGenPass;
		document.getElementById("config_input_gb_db_name").value = autoGenDB;
	}
}

$(document).ready(function(){
});
