$('#feedback-div').hide();

function create_user_db() {
        $.ajax({
            url: 'db_create.php',
            type: 'POST',
	    data: '',
        });
}

function upload_file() {
        $.ajax({
            url: 'index.php?action=upload',
            type: 'POST',
	    data: '',
        });
}

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

function submission() {
	var feedback = document.getElementById("feedback-conf");
	var content_type = document.getElementById("content_type");
	var ad_type = document.getElementById("ad_type");
	var start_date = document.getElementById("start_date");
	var end_date = document.getElementById("end_date");
	var fileToUpload = document.getElementById("fileToUpload");

	if ($(content_type).val() && $(ad_type).val() && $(start_date).val() && $(end_date).val() && $(fileToUpload).val() != '') {
//		feedback.innerHTML = $(content_type).val() + "</br>" + $(ad_type).val() + "</br>" + $(start_date).val() + "</br>" + $(end_date).val() + "</br>" + $(fileToUpload).val();
		feedback.innerHTML = "";
	        upload_file($(content_type).val(),$(ad_type).val(),$(start_date).val(),$(end_date).val(),$(fileToUpload).val());

	} else {
		feedback.innerHTML = "Please fill in all fields";
	}

}

$(document).ready(function(){
});
