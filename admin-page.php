<?php

if (!defined('ABSPATH')) exit;

//Styles
$date = date('ymdhis');
$codec_admin_css = plugins_url('css/codec-admin.css', __FILE__);
wp_enqueue_style('codec_admin_css', $codec_admin_css, [], $date);
$codec_message_css = plugins_url('css/codec-message.css', __FILE__);
wp_enqueue_style('codec_message_css', $codec_message_css, [], $date);
$awesome = '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css';
wp_enqueue_style('codec_awesome_css', $awesome, [], cppp_widget_version);
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_style('jquery-ui-datepicker');
wp_enqueue_style('jqueryuicss', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', [], "$date");
$first_start_cppp = get_option('first_start_codecp');
//Content
$wizzard = get_option('cppp_wizzard_finish_flag');
$date_from = date('m/01/Y');
$date_now = date('m/d/Y');
$paypal = get_option('cppp_paypal');
$flag_disable_auto_widget = get_option('cppp_disable_auto_widget');
$units_num = get_option('cppp_auto_widget_units');

$get_url = get_site_url();
$get_url = str_replace('https://','',$get_url);
$get_url = str_replace('http://','',$get_url);

//active widget
$cppp_widget = get_option('widget_cppp_widget');
$count = 0;

if($cppp_widget)
    foreach($cppp_widget as $widg) {
        $title = isset($widg['title']) ? $widg['title']:0;
        if($title) $count++;
    }
?>
<script>var cccp_plugins_url_img_like = '<?php echo plugins_url('images/like.png', __FILE__); ?>'</script>
    <link href="//static.epochtimes.de/css/bootstrap-5.3.2-min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="//static.epochtimes.de/js/bootstrap-5.3.2-min.js"></script>
    <script src="<?=plugins_url('js/chart.js', __FILE__) ?>"></script>
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="https://admin.codecprime.com/codec-logo2.png" width="240" height="auto" />
                <div class="small_text">Clean Content, Premium Payments</div>
            </a>
        <div class="col-md-offset-8 col-md-1 text-center">
            <a href="mailto:support@codecprime.com">
                <img src="https://codecprime.com/wp-content/uploads/elementor/thumbs/63-Call-Center-qehlf0awsqz97gei14n1ysmo08ycj8lt0wt7z2ornk.png" />
                <br />Support
            </a>
        </div>
        </div>
    </nav>

<?php if(empty($wizzard)): //wizzard! ?>
    <div class="row py-3 text-center">
        <div class="alert alert-primary d-none" role="alert"><div id='cppp_page_status'><i class='fa fa-spin fa-spinner'></i></div></div>
        <div id='codec_con_result' class="text-center"></div>
        <div id='codec_connection_result' class="mb-3"><i class='fa fa-spin fa-spinner'></i></div>
        <hr>
    </div>
<?php else: // dashboard ?>
    <div class="row py-3 text-center">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"><b>Dashboard</b></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Settings</button>
        </li>
    </ul>
        <?php if($flag_disable_auto_widget && $count==0): ?>
            <div class="w-alert alert alert-warning mt-2" role="alert">Widget disabled!<br> You are not earning anything at this time! To start receiving income, go to settings and enable auto widget</div>
        <?php endif; ?>
    <div class="tab-content mt-3" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class='earnings_block'>
                <h2>My Earnings</h2>
                <div class='cppp_show_earnings d-flex justify-content-center'>
                    <label class="form-label">From: </label><input id="datetimeFrom" class="form-control" size="10" value="<?=$date_from?>" required style="width:20%" />
                    <label class="form-label">To: </label><input id="datetimeTo" class="form-control" size="10" value="<?=$date_now?>" required style="width:20%" />
                    <button id="update_publisher_earnings" class="btn btn-secondary btn-sm"><i class="fa fa-refresh"></i> Update</button>
                </div>
                <br>
                <div style='margin-bottom:20px;' id='cppp_earnings_status'>Loading...</div>
                <div>
                    <canvas id="cppp_graf_year"></canvas>
                </div>

            </div>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <h2>Settings</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-title"><h3 class="text-left">Widget type</h3><p class="grey">(More units – more revenue)</p></div>
                        <div class="card-body">
                            <div align="center">
                                <div class="units_number_settings btn-group " role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="btnradio" id="units_6_dash" autocomplete="off"<?php if($units_num=='u6'){echo 'checked'; } ?>>
                                    <label class="btn btn-outline-primary" for="units_6_dash">6 Units </label>
                                    <input type="radio" class="btn-check" name="btnradio" id="units_3_dash" autocomplete="off"<?php if($units_num=='u3'){echo 'checked'; } ?>>
                                    <label class="btn btn-outline-primary" for="units_3_dash">3 Units</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-title"><h3 class="text-left">Payout account</h3></div>
                        <div class="text-center"><img width='120px' src='//www.paypalobjects.com/digitalassets/c/website/logo/full-text/pp_fc_hl.svg'/></div>
                        <div class="card-body d-flex">
                            <input autocomplete='off' id='paypal_email_input' class='form-control' value='<?=$paypal?>' placeholder='Type your PayPal email'>
                            <button id='save_pal' class="btn btn-warning btn-sm">Update</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-title"><h3 class="text-left">Advanced</h3></div>
                        <div class="card-body">
                            <input type="checkbox" class="btn-check" id="btn-check" checked autocomplete="off">
                            <?php if($flag_disable_auto_widget): ?>
                                <label class="btn btn-success showing-disable btn-lg" for="btn-check">Enable</label>
                                <p class="grey d-none">(Not recommended)</p>
                            <?php else: ?>
                                <label class="btn btn-warning showing-disable" for="btn-check">Manual Setup</label>
                                <p class="grey">(Not recommended)</p>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $('.units_number_settings').on('change', function (){
                let un6 = $('#units_6_dash:checked').val()
                let un3 = $('#units_3_dash:checked').val()

                if(un6=='on'){
                    console.log('UNITS 6')
                    data = {'action': 'cppp_auto_widget_enable', 'units': 'u6'};
                }else if(un3=='on'){
                    console.log('UNITS 3')
                    data = {'action': 'cppp_auto_widget_enable', 'units': 'u3'};
                }
                $.post(ajaxurl, data, function(resp) {
                    if(resp && resp!=undefined){
                    }
                })
            })
            var auto_widget_flag = '<?php echo $flag_disable_auto_widget; ?>'
            if(auto_widget_flag){ $('input#btn-check').prop('checked', false) }
            $('.showing-disable').on('click', function (){
                console.log('click button')
                let dis = $('input#btn-check:checked').length
                var self = this
                    if (dis) { //disable
                        if(confirm("Are you sure you want to disable the widget for now?")) {
                            $.post(ajaxurl, {action: 'cppp_disable_auto_widget', dis_widget: 1}, function (resp){
                                if(resp) {
                                    $(self).html('Enable')
                                    $(self).removeClass('btn-warning')
                                    $(self).addClass('btn-success')
                                    $(self).addClass('btn-lg')
                                    $('p.grey').hide()
                                }
                            })

                        }else { $('input#btn-check').prop('checked', false); }
                    } else {
                        $.post(ajaxurl, {action: 'cppp_disable_auto_widget', dis_widget: 0}, function (resp) {
                            console.log('disab_auto_w::', resp)
                            if(resp) {
                                $(self).html('Disable')
                                $(self).removeClass('btn-success')
                                $(self).addClass('btn-warning')
                                $('p.grey').show()
                                $('.w-alert').hide(200)
                            }
                        })
                    }
            })
        </script>
    </div>
    </div>
<?php endif;?>
    <div class="row justify-content-center mt-5">
        <h4>Next Generation Monetizing System</h4>
        <div class="col-md-4">
        <div class="card shadow" style="width: 18rem;">
            <div class="card-body">
                <div class="text-center">
                <img src="https://codecprime.com/wp-content/uploads/elementor/thumbs/megaphone-qehlf18qzl0ntr08hm63izbfhcasprtoph0nh9tiio.png" />
                </div>
                <h5 class="card-title text-center mt-2">Not Blocked By AdBlockers</h5>
                <p class="card-text text-center text-grey">CODEC content is not blocked by any current ad-blockers on the market.<br>
                    Monetize 30%+ More!</p>
                <div class="text-center"><a href="https://codecprime.com/publishers-monetization/#adblockers" target="_blank" rel="nofollow" class="btn btn-outline-primary">Learn more</a></div>
            </div>
        </div>
        </div>
        <div class="col-md-4">
        <div class="card shadow" style="width: 18rem;">
            <div class="card-body">
                <div class="text-center">
                    <img src="https://codecprime.com/wp-content/uploads/elementor/thumbs/1071-Currency-qehlf18qzl0ntr08hm63izbfhcasprtoph0nh9tiio.png" />
                </div>
                <h5 class="card-title text-center mt-2">Exclusive Premium Demand</h5>
                <p class="card-text text-center text-grey">Supplied by the CODEC DSP from direct relationships with advertisers and agencies.<br>
                    Premium $4 - $12 CPM!</p>
                <div class="text-center"><a href="https://codecprime.com/publishers-monetization/#cpm" target="_blank" rel="nofollow" class="btn btn-outline-primary">Learn more</a></div>
            </div>
        </div>
        </div>
        <div class="col-md-4">
        <div class="card shadow" style="width: 18rem;">
            <div class="card-body">
                <div class="text-center">
                    <img src="https://codecprime.com/wp-content/uploads/elementor/thumbs/Codec-Pages-on-Different-Websites-Iphone-qehlf18qzl0ntr08hm63izbfhcasprtoph0nh9tiio.png" />
                </div>
                <h5 class="card-title text-center mt-2">Ads With Dignity</h5>
                <p class="card-text text-center text-grey">How many times you felt ashamed for the content of those native ads on your site?<br> Never compromise again!</p>
                <div class="text-center"><a href="https://codecprime.com/publishers-monetization/#noscam" target="_blank" rel="nofollow" class="btn btn-outline-primary">Learn more</a></div>
            </div>
        </div>
        </div>
    </div>
</div>
<?php

//Code
$site = site_url();
$widgets_link = "$site/wp-admin/widgets.php";

//Scripts
$js = plugins_url('js/codec-message.js', __FILE__);
$js2 = plugins_url('js/codec-widget-admin.js', __FILE__);
wp_enqueue_script('Codec message script', $js, ['jquery'], time(), true);
wp_enqueue_script('Codec widget script', $js2, ['jquery'], time(), true);
