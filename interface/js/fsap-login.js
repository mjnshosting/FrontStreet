$('#feedback-div').hide();

function create_user_db() {
        $.ajax({
            url: 'db_create.php',
            type: 'POST',
	    data: '',
        });
}

function edit_ad(id) {
	var toggletd = document.getElementById("toggle-td-" + id);
	var contentTd = document.getElementById("content-td-" + id);
	var adTd = document.getElementById("ad-td-" + id);
	var durationTd = document.getElementById("duration-td-" + id);
	var startTd = document.getElementById("start-td-" + id);
	var endTd = document.getElementById("end-td-" + id);
	var feedbackTd = document.getElementById("feedback-td-" + id);

        $.ajax({
            url: 'index.php?action=edit',
            type: 'POST',
	    data: {id : id},
            success: function (data) {
		document.getElementById("tr-" + id).innerHTML = data;
            }
        });
}

function save_ad(id) {
	var toggletd = document.getElementById("toggle-td-" + id);
	var content_type = document.getElementById("content_type-" + id);
	var ad_type = document.getElementById("ad_type-" + id);
	var duration = document.getElementById("duration-" + id);
	var start_date = document.getElementById("start_date-" + id);
	var end_date = document.getElementById("end_date-" + id);
	var feedbackTd = document.getElementById("feedback-td-" + id);

	if ($(content_type).val() && $(ad_type).val() && $(start_date).val() && $(end_date).val() != '') {
		if ($(start_date).val() < $(end_date).val()) {
                        if (parseInt($(duration).val(),10) < 10 || !$.isNumeric($(duration).val()) ) {
                                document.getElementById("duration-" + id).value = 10;
                        } else if (parseInt($(duration).val(),10) > 30) {
                                document.getElementById("duration-" + id).value = 30;
                        }

                          $.ajax({
                            url: 'index.php?action=save',
                            type: 'POST',
                            data: {id: id, content_type: content_type.value, ad_type: ad_type.value, duration: duration.value, start_date: start_date.value, end_date: end_date.value},
                            success: function (data) {
				document.getElementById("tr-" + id).innerHTML = data;
                            }
                          });
		} else {
			feedbackTd.innerHTML = "<p id='feedback-" + id + "' style='color:red; font-size: 14px;'>Correct dates</p>";
		}
	} else {
       		feedbackTd.innerHTML = "<p id='feedback-" + id + "' style='color:red; font-size: 14px;'>Fill in blanks</p>";
	}
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
			    success: function (data) {
				$('#feedback-conf').html(data);
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
