jQuery(document).keyup(function(e) {
	if (e.keyCode == 27) { // escape key maps to keycode `27`
		cppp_message_hide();
	}
});

jQuery(document).on('click', '#cppp_messagebox .close_i', function( event ) {
	cppp_message_hide();
});		

jQuery(document).on('keypress', '#cppp_messagebox input', function( event ) {
	if(event.which == 13 ){
		jQuery('#cppp_messagebox .codec_button.confirm').trigger('click');
	}
});

function cppp_message_hide() {	
	message_box = jQuery('#cppp_messagebox');
	message_box.remove();
}

function cppp_message_show(h3, p, cl = null) {
	
	len = jQuery('#cppp_messagebox').length;
	if(!len) {jQuery('body').append("<div class='"+cl+"' id='cppp_messagebox' types='0'></div>");}
	else {
		jQuery('#cppp_messagebox').addClass(cl);
	}
	message_box = jQuery('#cppp_messagebox');
	message_box.empty();
	message_box.fadeIn();
	message_box.append("<h3 id='message_headline' class='py-2'>"+h3+"</h3><i class='fa fa-times close_i'></i><div id='message_text'>"+p+"</div>");
	jQuery('#cppp_messagebox input:eq(0)').focus();
}

function cppp_message_error(description) {
	return "<p class='error_text'>"+description+"</p>";
}
function cppp_message_success(description) {
	return "<p class='success'>"+description+"</p>";
}

function cppp_error_before(th, description) {
	th_error = "<p class='error_text'>"+description+"</p>";
	th.before(th_error);
	return th_error;
}

function cppp_error_after(th, description) {
	th_error = "<p class='error_text'>"+description+"</p>";
	th.after(th_error);
	return th_error;
}

function cppp_success_before(th, description) {
	th.before("<p class='success'>"+description+"</p>");
}

function cppp_success_after(th, description) {
	th.after("<p class='success'>"+description+"</p>");
}

cppp_fa_spinner = "<i class='fa fa-spin fa-spinner'></i>";
cppp_fa_p_spinner = "<p class='spinner'>"+cppp_fa_spinner+"</p>";

function cppp_loader_remove() {
	jQuery('.fa-spinner').remove();
	jQuery('p.spinner').remove();
}

function cppp_hide_errors() {
	jQuery('p.error_text').remove();
	
}
function cppp_hide_success() {	
	jQuery('p.success').remove();
}

jQuery(document).on('click','.close_mes',function(){
	th = jQuery(this);
	th.parent().hide(500);
	th.parent().remove();
});
 
function cppp_short_mes(what, cl = '', time = 5000){
	rand = Math.floor(Math.random() * 10000000000000) + 1;
	//console.log(rand);
	message_text = "<div style='display:none;' id='short_mes_"+rand+"' class='short_mes "+cl+"'><i class='close_mes fa fa-times'></i>"+what+"</div>";
	len = jQuery('#short_messagex_box').length;
	if(len) {
		jQuery('#short_messagex_box').append(message_text);
		jQuery('#short_mes_'+rand).show(500);
	}
	else {
		jQuery('body').append("<div id='short_messagex_box'>"+message_text+"</div>");
		jQuery('#short_mes_'+rand).show(500);
	}
	
	setTimeout(function(){
		th_message = jQuery('#short_messagex_box div:eq(0)');
		th_message.hide(500);
		th_message.remove();
	}, time);
}


function cppp_valid_link(textval) {
	var urlregex = /^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/;
	return urlregex.test(textval);
}	

function cppp_checkPwd(str) {
	if (str.length < 6) {
		return message_error("The password is too short");
	} else if (str.length > 20) {
		return message_error("The password is too long");
	} else if (str.search(/\d/) == -1) {
		return message_error("The password should contain at least one number");
	} else if (str.search(/[a-zA-Z]/) == -1) {
		return message_error("The password should contain at least one Latin letter");}
	else if (str.search(/[A-Z]/) == -1) {
		return message_error("The password should contain at least one capital letter");
	} else if (str.search(/[\^\!\@\#\$\%\^\&\*\(\)\_\+]/) == -1) {
		return message_error("The password should countain at least one special character: ^, !, @, #, $, %, ^, &, *, (, ), _, +");
	}
	else { return '';}
}

function cppp_check_mail(mail) { 
	emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	matches = mail.match(emailReg);
	if (!matches)
	{
		return '';
	}
	else {
		return 1;
	}
}