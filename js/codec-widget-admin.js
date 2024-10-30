jQuery(document).ready(function($) {
	//SET VARS:
	let cppp_connect_but = "<div id='codec_connect' class='codec_button'>1. Register</div>";
	let cpp_register_2 = "<div id='' class='codec_button_outset'>2. Customize</div>"
	let cpp_register_3= "<div id='' class='codec_button_outset getpaid'>3. Get Paid!</div>"
	let cpp_register_3_active= "<div id='codec_getpaid' class='codec_button getpaid ml-60'>3. Get Paid!</div>"
	let cpp_reguster_complited = "<div id='' class='codec_button_green'><i white class='fa fa-check'></i> 1. Register</div>"
	let cpp_register_2_active = "<div id='codec_customize' class='codec_button'>2. Customize</div>"
	let cpp_register_2_complited = "<div id='' class='codec_button_green ml-60'><i white class='fa fa-check'></i> 2. Customize</div>"
	let cpp_register_3_complited = "<div id='' class='codec_button_green ml-60'><i white class='fa fa-check'></i> 3. Get Paid</div>"
	let cppp_create_page_but = "<div id='codec_page_create' class='codec_button'><i class='fa fa-plus'></i> Create automatically</div>";
	let cppp_created_page_status = "A 'codec-news' page was created for you to enable CODEC to work on your site. Please don't modify or delete it!";
	let cppp_widget_not_created = "Status: not placed <i red class='fa fa-times'></i>"
	let spiner = "<i class='fa fa-spin fa-spinner'></i>"
	
	function cppp_page_status(action, pub) {
		data = {'action': 'cppp_page_status', pub: pub};
		$.post(ajaxurl, data, function(response) {
			if(action != 'create') {
				if(response == 'yes') {
					cppp_page_created();
				}
				if(response == 'no') {
					cppp_page_not_created();
				}
			}
			else {
				cppp_create_page(pub);
			}
		});
	}

	function cppp_create_page(pub) {
		data = {'action': 'cppp_create_page', 'pub': pub};
		$.post(ajaxurl, data, function(response) {
			if(response == 'created' || response == 'updated') {
				$('#cppp_page_status').html(cppp_created_page_status);
				cppp_page_created();
			}
		});
	}
//TODO: used also old the code
	$(document).on('click','#save_pal',function(){
		console.log('click paypall update!!!')
		var pay_email = $("input#paypal_email_input").val()
		data = {'action': 'cppp_paypal', val: pay_email};
		$.post(ajaxurl, data, function(resp) {
			$.post('https://admin.codecprime.com/api/wp_publisher/save_paypal', {'save_pal': pay_email, 'url': resp}).done(function(result) {
				//result = JSON.parse(result);
				$.post(ajaxurl, {'action': 'cppp_wizzard_finish'}, function (res){
					console.log('cpp_update paypoll:::', res)
					if(res && res.toString().indexOf('done!')>0) {
						$("input#paypal_email_input").val(pay_email)
					}
				})
			});
		});
	});
	
	$(document).on('click','#codec_page_create',function(){
		cppp_create_page();
	});

	token = '';

	function cppp_page_created() {
		$('div .alert-primary').removeClass('d-none')
		$('#cppp_page_status').html(cppp_created_page_status);
	}

	function cppp_page_not_created() {
		$('#cppp_page_status').html(cppp_create_page_but);
	}

	function codec_disconnected(){
		
		//cppp_loader_remove();
		$('#codec_connection_result').html(cppp_connect_but)
		$('#codec_connection_result').append(cpp_register_2)
		$('#codec_connection_result').append(cpp_register_3)
		$('div .alert-primary').addClass('d-none')
		$('#codec_con_result').removeAttr('green');
		$('#codec_con_result').attr('red','');
		$('#cppp_page_status').empty();
		data = {'action': 'cppp_empty_widgets'};
		$.post(ajaxurl, data, function(response) {
			$('#widget_status').html(cppp_widget_not_created);
			$('#widget_stat').removeAttr('green');
			$('#widget_stat').attr('red','');
		});
	}

	function codec_connected(){
		$('#codec_connection_result').html(cpp_reguster_complited)
		$('#codec_connection_result').append(cpp_register_2_active)
		$('#codec_connection_result').append(cpp_register_3)
		//$('#codec_connection_result').append(cppp_disconnect_but);

		//$('#codec_con_result').html("(DONE)");
		$('#codec_con_result').removeAttr('red');
		$('#codec_con_result').attr('green','');
	}

	function codec_paypall_connected(unit_count){
		$('#codec_connection_result').html(cpp_reguster_complited)
		$('#codec_connection_result').append(cpp_register_2_complited)
		$('#codec_connection_result').append(cpp_register_3_active)
	}
	function finish_all_connected(){
		$('#codec_connection_result').html(cpp_reguster_complited)
		$('#codec_connection_result').append(cpp_register_2_complited)
		$('#codec_connection_result').append(cpp_register_3_complited)
		setTimeout(()=>{
			$('.codec_button_green').remove()
			$('#codec_connection_result').append('<h2>Congratulations, setup is complete!</h2>')
			$('#codec_connection_result').append('<img width="70px" height="auto" src="'+cccp_plugins_url_img_like+'" />')

		},2000)
	}
	var chart

	function check_connection() {
		data = {'action': 'cppp_site_url'};
		$.post(ajaxurl, data, function(resppp) {
			console.log('check_connection:', resppp)
			if(resppp && resppp.toString().indexOf('auto_widget_true')>0){
				var auto_widget_flag=true
				var eml = resppp.split(',')
				resppp = resppp.split(',')[0]
				if(eml.length==3){
					var paypall_email = eml[2]
				}
			}
			$.post('https://admin.codecprime.com/api/wp_publisher/check', {url: resppp}).done(function(data) {
				//myArray = JSON.parse(data)
				error = data['error']
				//token = data['token']
				connect = data['connect']
				pub = data['pub']
				if(error) {
					codec_disconnected()
				} else {
					console.log('widget:::', auto_widget_flag)
					codec_connected();
					if(auto_widget_flag){
						codec_paypall_connected()
					}
					if(paypall_email){
						finish_all_connected()
					}
					cppp_page_status('', pub)
					$.post('https://admin.codecprime.com/api/wp_publisher/get_stats', {url: resppp}).done(function(result) {
						//start dashboard
						console.log('resp:::', result.data_for_graf)
						var data_graf = []
						var money_graf = []
						result.data_for_graf.forEach((item)=>{
							data_graf.push(item[0])
							money_graf.push(item[1])
						})
						console.log('data_graf', data_graf)
						console.log('money_graf', money_graf)
						setTimeout(()=>{
							console.log('graf start!!!')
							const ctx = document.getElementById('cppp_graf_year');
							const data = {
								labels: data_graf,
								datasets: [{
									label: 'your Earnings',
									data: money_graf,
									fill: false,
									borderColor: 'rgb(72, 192, 192)',
									tension: 0.1
								}]
							};
							chart = new Chart(ctx, {
								type: 'line',
								data: data,
							});
						}, 200)
						 cppp_get_earnings(result)
					});
				}
			});
		});
	}
	//update graf
	function addData(chart, label, newData) {
		chart.data.labels.push(label);
		chart.data.datasets.forEach((dataset) => {
			dataset.data.push(newData);
		});
		chart.update();
	}
	check_connection();
	//update money
	$(document).on('click','#update_publisher_earnings',function(){
		
		from = $('#datetimeFrom').val();
		to = $('#datetimeTo').val();
		
		console.log(from);
		console.log(to);
		data = {'action': 'cppp_site_url'};
		
		if(!from || !to) {
			cppp_short_mes("Start and end date are required", 'red'); 
		}
		
		if(from && to) {
			$('#cppp_earnings_status').html(spiner)
			$.post(ajaxurl, data, function(resppp_more) {
				let url = resppp_more.split(',')[0]
				$.post('https://admin.codecprime.com/api/wp_publisher/get_stats', { url: url, from: from, to: to}).done(function(result) {
					console.log('my mony, how match::', result)
					//[money: 0.14, date: '2023-11-01']
					var data_graf = []
					var money_graf = []
					result.data_for_graf.forEach((item)=>{
						data_graf.push(item[0])
						money_graf.push(item[1])
					})
					chart.data.labels = data_graf;
					chart.data.datasets.forEach((dataset) => {
						dataset.data = money_graf;
					});
					chart.update();
					cppp_get_earnings(result);
				});		
			});
		}
		
	});
	$(document).on('focus','#datetimeFrom',function(){
		$('#datetimeFrom').datepicker({dateFormat: 'mm/dd/yy'});
	})
	$(document).on('focus','#datetimeTo',function(){
		$('#datetimeTo').datepicker({dateFormat: 'mm/dd/yy'});
	})
	function cppp_get_earnings(result){
		$('.error_text').remove();
		//result = JSON.parse(result);
		//console.log(result);
		error = result.error;
		if(error) {
			res = error;
			$('.cppp_earnings').remove();
			$('#cppp_earnings_status').html('error: '+res);
		} else {
			$('.earnings_block').show(500);
			res = result.result;
			$('#cppp_earnings_status').html(res);
		}
	}

	$(document).on('click','#codec_disconnect',function(){
		th = $(this);
		th.after (" " + cppp_fa_spinner);
		data = {'action': 'cppp_site_url'};
		$.post(ajaxurl, data, function(response) {
			cod_info = response
			$.post('https://admin.codecprime.com/api/wp_publisher/disconnected', { 'url': cod_info}).done(function(result) {
				//result = JSON.parse(result);
				cppp_loader_remove();
				if(result.status == 'disconnected')  {
					codec_disconnected();
				}
			}).fail(function(request,status,errorThrown) {});
		});
	});

	$(document).on('click','#codec_connect',function(){
		message_text = "";
		message_text += "<p>Contact person name<span red>*</span><br>";
		message_text += "<input id='codec_name' class='form-control' name='name' placeholder='Your name' style='width:50%;'></p>";
		message_text += "<p>Email<span red>*</span><br>";
		message_text += "<input id='codec_email' class='form-control' name='email' placeholder='your@email.com' style='width:50%;'></p>";
		message_text += "<p><span red>*</span> <span gray>All fields are required</span></p>";
		message_text += "<p gray>By clicking 'Connect' button below, you confirm that you read and accepted CODEC Prime <a target='_blank' href='https://codecprime.com/terms-publisher/'>Publisher Terms</a> & <a target='_blank' href='https://codecprime.com/privacy-policy/'>Privacy Policy</a>.</p>";
		message_text += "<div id='codec_pub_id' class='btn btn-primary confirm codec_button_sender'> Register</div>";
		cppp_message_show('Connect your site', message_text);
	})
	$(document).on('click','#codec_customize',function(){
		message_text = ""
		message_text +="<p class='fw-bold fs-6'>The more units the higher your CPM and revenue. (6 or 3).</p> "
		message_text += '<br><div align="center"><div class="btn-group " role="group" aria-label="Basic radio toggle button group">' +
			'<input type="radio" class="btn-check" name="btnradio" id="units_6" autocomplete="off" checked>' +
			'<label class="btn btn-outline-primary" for="units_6">6 Units </label>' +
			'<input type="radio" class="btn-check" name="btnradio" id="units_3" autocomplete="off">' +
			'<label class="btn btn-outline-primary" for="units_3">3 Units</label>' +
			'</div></div><br>'
		message_text += "<div id='codec_widget_save' class='btn btn-primary confirm codec_button_sender'> Save units</div>";
		cppp_message_show('How many ad units to show:', message_text);
	});
	$(document).on('click','#codec_getpaid',function(){
		message_text = ""
		message_text +="<div class='text-center'><img width='120px' src='//www.paypalobjects.com/digitalassets/c/website/logo/full-text/pp_fc_hl.svg'/>" +
			"<p class='fw-bold fs-6 mt-2'>PayPal email (to receive CODEC payments):" +
			"</p></div>" +
			"<p><input autocomplete='off' id='paypal_email_input' class='form-control' value='' placeholder='Type your PayPal email'>"
			'<br>'
		message_text += "<div id='codec_paypall_save' class='btn btn-primary confirm codec_button_sender'> Save and finish</div>";
		cppp_message_show('Step 3. Get Paid!', message_text);
	})
	//new publisher
	$(document).on('click','#codec_pub_id',function(){
		th = $(this)
		cppp_hide_errors()
		m = $('#codec_email').val()
		spin = th.find('.fa-spin').length
		n = $('#codec_name').val();
		//p = $('#codec_phone').val();
		cod_info = {m:m,n:n,p:''};
		good = 1;
		if(!m || !n) {
			cppp_error_before(th, "Please fill out all fields in the form.");
			good = '';
		}
		if(spin) good = '';
		valid = cppp_check_mail(m);
		if(!valid) cppp_error_before(th, "Email not valid");
		if(valid && good) {
			th.append(" " + cppp_fa_spinner);		
			data = {'action': 'cppp_site_url', 'status': 'connected'};
			cppp_message_hide()
			$('#codec_connect').append(spiner)
			$.post(ajaxurl, data, function(response) {
				cod_info['u'] = response;
				//console.log('token:', token)
				$.post('https://admin.codecprime.com/api/wp_publisher/adduser', {'my_token': token, 'info': cod_info}).done(function(result) {
					//result = JSON.parse(result);
					cppp_loader_remove();
					res = result['result'];
					error = result['error'];
					pub = result['pub'];					
					if(!error)
					if(res == 'connected' || res == 'created') {
						//cppp_message_hide();
						$('.fa-spin').remove()
						codec_connected();
						cppp_page_status('create', pub);
					}
					if(error) {
						th = $('#codec_pub_id');
						cppp_error_before(th, error);
					}
				});
			});		
		}
	})

	//Auto widget save
	$(document).on('click', '#codec_widget_save', function (){
		cppp_hide_errors()
		let un6 = $("#units_6:checked").val()
		let un3 = $("#units_3:checked").val()
		var data = null
		cppp_message_hide()
		$('#codec_customize').append(spiner)
		if(un6=='on'){
			console.log('UNITS 6')
			data = {'action': 'cppp_auto_widget_enable', 'units': 'u6'};
		}else if(un3=='on'){
			console.log('UNITS 3')
			data = {'action': 'cppp_auto_widget_enable', 'units': 'u3'};
		}
		$.post(ajaxurl, data, function(resp) {
			if(resp && resp!=undefined){
				$('.fa-spin').remove()
				codec_paypall_connected(resp)
			}
		})
	})
	//Add paypall and finish settings
	$(document).on('click', '#codec_paypall_save', function (){
		cppp_hide_errors()
		var pay_email = $("input#paypal_email_input").val()
		console.log('pay_email::', pay_email)
		var th = $(this);
		var valid = cppp_check_mail(pay_email);
		if(!valid) cppp_error_before(th, "Email not valid");
		cppp_message_hide()
		$('#codec_getpaid').append(spiner)
		//th.append(" " + cppp_fa_spinner);
		data = {'action': 'cppp_paypal', val: pay_email};
		$.post(ajaxurl, data, function(resp) {
			$.post('https://admin.codecprime.com/api/wp_publisher/save_paypal', {'save_pal': pay_email, 'url': resp}).done(function(result) {
				//result = JSON.parse(result);
				$('.fa-spin').remove()
				finish_all_connected()
				$.post(ajaxurl, {'action': 'cppp_wizzard_finish'}, function (res){
					console.log('cpp_wizzard:::', res)
					if(res && res.toString().indexOf('done!')>0) {
						console.log('reload!!!')
						setTimeout(()=>{
							document.location.reload()
						},5000)
					}
				})
			});
		});

	})

});