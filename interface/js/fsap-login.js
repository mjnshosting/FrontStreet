$('#feedback-div').hide();

function create_user_db() {
        $.ajax({
            url: 'db_create.php',
            type: 'POST',
	    data: '',
        });
}

function delete_ad(id) {

        $.ajax({
            url: 'index.php?action=remove',
            type: 'POST',
	    data: {id : id},
	    success: function () {
		location.reload();
	    }
        });
}

$("form#uploadForm").submit(function(event){
	event.preventDefault();
	var feedback = document.getElementById("feedback-conf");
	var content_type = document.getElementById("content_type");
	var ad_type = document.getElementById("ad_type");
	var duration = document.getElementById("duration");
	var start_date = document.getElementById("start_date");
	var end_date = document.getElementById("end_date");
	var fileToUpload = document.getElementById("fileToUpload");

	if ($(content_type).val() && $(ad_type).val() && $(start_date).val() && $(end_date).val() && $(fileToUpload).val() != '') {
		if ($(start_date).val() < $(end_date).val()) {
			if (parseInt($(duration).val(),10) < 10 || !$.isNumeric($(duration).val()) ) {
				document.getElementById("duration").value = 10;
			} else if (parseInt($(duration).val(),10) > 30) {
				document.getElementById("duration").value = 30;
			}
			  var formData = new FormData($(this)[0]);
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
		} else {
			feedback.innerHTML = "Please correct the date range.";
		}
	} else {
		feedback.innerHTML = "Please fill in all fields";
	}
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
}

$(document).ready(function(){
});
