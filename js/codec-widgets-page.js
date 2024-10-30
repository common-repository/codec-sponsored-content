jQuery(document).ready(function($) {

$(document).on('widget-added', function(event, widget){
	widget_id = $(widget).attr('id');
	codec = cppp_strpos(widget_id, '_cppp_widget-', 0);
	widget_id = widget_id.substr(codec+1);
	if(codec) {
		i = {'id': widget_id, 'type': 'vertical', 'posts': 3};
		data = {'action': 'cppp_site_url'};
		$.post(ajaxurl, data, function(response) {
		$.post('https://codecprime.com/partner/api/widget/get-code/check', {method: 'get_code', u: response}).done(function(data) {
			myArray = JSON.parse(data);
			error = myArray['error'];
			token = myArray['token'];
			if(token) {
				i['u'] = response;
				$.post('https://codecprime.com/partner/api/user/publisher/method', {token: token, method: 'update_widget', i: i}).done(function(){});
			}
		});
		});
	}
});

$(document).on('change','.cppp_widget_types label',function(){
	th = $(this);
	text = th.text();
	if(text == 'Horizontal') {
		th.parent().parent().find('.widget_post').html('<option value="3">3</option><option value="4">4</option><option value="6">6</option>');
	}
	if(text == 'Vertical') {
		th.parent().parent().find('.widget_post').html('<option value="3">3</option><option value="4">4</option>');
	}
});

$(document).on('widget-updated', function(event, widget){
	th = $(this);
	th_id = $(widget).attr('id');
	codec = cppp_strpos(th_id, '_cppp_widget-', 0);
	th = $('#'+th_id);
	th_id = th_id.substr(codec+1);
	if(codec) {
		widg = th.find('.cppp_widget_types input:checked').val();
		posts = th.find('.widget_post').val();
		if(typeof posts == 'undefined') posts = 3;
		if(posts == 'Select') posts = 3;
		data = {'action': 'cppp_site_url'};
		$.post(ajaxurl, data, function(response) {
		i = {'id': th_id, 'type': widg, 'posts': posts};
		$.post('https://codecprime.com/partner/api/widget/get-code/check', {method: 'get_code', u: response}).done(function(data) {
			myArray = JSON.parse(data);
			error = myArray['error'];
			token = myArray['token'];
			if(token) {
				i['u'] = response;
				$.post('https://codecprime.com/partner/api/user/publisher/method', {token: token, method: 'update_widget', i: i}).done(function(data) {
				});
			}
		});
		});
	}
});

function cppp_strpos(haystack, needle, offset) {
	var i = (haystack+'').indexOf(needle, (offset || 0));
	if(i === 0) i = 1;
	return i === -1 ? false : i;
}


});