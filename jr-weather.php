<?php
/*
Plugin Name: JR Weather
Plugin URI: http://www.jakeruston.co.uk/2009/11/wordpress-plugin-jr-weather/
Description: Displays the current weather in your desired city.
Version: 1.5.6
Author: Jake Ruston
Author URI: http://www.jakeruston.co.uk
*/

/*  Copyright 2010 Jake Ruston - the.escapist22@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$pluginname="weather";

// Hook for adding admin menus
add_action('admin_menu', 'jr_weather_add_pages');

// action function for above hook
function jr_weather_add_pages() {
    add_options_page('JR Weather', 'JR Weather', 'administrator', 'jr_weather', 'jr_weather_options_page');
}

if (!function_exists("_iscurlinstalled")) {
function _iscurlinstalled() {
if (in_array ('curl', get_loaded_extensions())) {
return true;
} else {
return false;
}
}
}

if (!function_exists("jr_show_notices")) {
function jr_show_notices() {
echo "<div id='warning' class='updated fade'><b>Ouch! You currently do not have cURL enabled on your server. This will affect the operations of your plugins.</b></div>";
}
}

if (!_iscurlinstalled()) {
add_action("admin_notices", "jr_show_notices");

} else {
if (!defined("ch"))
{
function setupch()
{
$ch = curl_init();
$c = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
return($ch);
}
define("ch", setupch());
}

if (!function_exists("curl_get_contents")) {
function curl_get_contents($url)
{
$c = curl_setopt(ch, CURLOPT_URL, $url);
return(curl_exec(ch));
}
}
}

if (!function_exists("jr_weather_refresh")) {
function jr_weather_refresh() {
update_option("jr_submitted_weather", "0");
}
}

register_activation_hook(__FILE__,'weather_choice');

function weather_choice () {
if (get_option("jr_weather_links_choice")=="") {

if (_iscurlinstalled()) {
$pname="jr_weather";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_weather", "1");
wp_schedule_single_event(time()+172800, 'jr_weather_refresh');
} else {
$content = "Powered by <a href='http://arcade.xeromi.com'>Free Online Games</a> and <a href='http://directory.xeromi.com'>General Web Directory</a>.";
}

if ($content!="") {
$content=utf8_encode($content);
update_option("jr_weather_links_choice", $content);
}
}

if (get_option("jr_weather_link_personal")=="") {
$content = curl_get_contents("http://www.jakeruston.co.uk/p_pluginslink4.php");

update_option("jr_weather_link_personal", $content);
}
}

// jr_weather_options_page() displays the page content for the Test Options submenu
function jr_weather_options_page() {

    // variables for the field and option names 
    $opt_name = 'mt_weather_header';
	$opt_name_2 = 'mt_weather_color';
    $opt_name_3 = 'mt_weather_city';
	$opt_name_4 = 'mt_weather_header2';
    $opt_name_6 = 'mt_weather_plugin_support';
	$opt_name_7 = 'mt_weather_temp';
	$opt_name_8 = 'mt_weather_wind';
    $hidden_field_name = 'mt_weather_submit_hidden';
    $data_field_name = 'mt_weather_header';
	$data_field_name_2 = 'mt_weather_color';
    $data_field_name_3 = 'mt_weather_type';
	$data_field_name_4 = 'mt_weather_header2';
    $data_field_name_6 = 'mt_weather_plugin_support';
	$data_field_name_7 = 'mt_weather_temp';
	$data_field_name_8 = 'mt_weather_wind';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );
	$opt_val_2 = get_option( $opt_name_2 );
    $opt_val_3 = get_option( $opt_name_3 );
	$opt_val_4 = get_option( $opt_name_4 );
    $opt_val_6 = get_option($opt_name_6);
	$opt_val_7 = get_option($opt_name_7);
	$opt_val_8 = get_option($opt_name_8);
    
if (!$_POST['feedback']=='') {
$my_email1="the.escapist22@gmail.com";
$plugin_name="JR Weather";
$blog_url_feedback=get_bloginfo('url');
$user_email=$_POST['email'];
$user_email=stripslashes($user_email);
$subject=$_POST['subject'];
$subject=stripslashes($subject);
$name=$_POST['name'];
$name=stripslashes($name);
$response=$_POST['response'];
$response=stripslashes($response);
$category=$_POST['category'];
$category=stripslashes($category);
if ($response=="Yes") {
$response="REQUIRED: ";
}
$feedback_feedback=$_POST['feedback'];
$feedback_feedback=stripslashes($feedback_feedback);
if ($user_email=="") {
$headers1 = "From: feedback@jakeruston.co.uk";
} else {
$headers1 = "From: $user_email";
}
$emailsubject1=$response.$plugin_name." - ".$category." - ".$subject;
$emailmessage1="Blog: $blog_url_feedback\n\nUser Name: $name\n\nUser E-Mail: $user_email\n\nMessage: $feedback_feedback";
mail($my_email1,$emailsubject1,$emailmessage1,$headers1);
?>

<div class="updated"><p><strong><?php _e('Feedback Sent!', 'mt_trans_domain' ); ?></strong></p></div>

<?php
}

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];
		$opt_val_2 = $_POST[ $data_field_name_2 ];
        $opt_val_3 = $_POST[ $data_field_name_3 ];
		$opt_val_4 = $_POST[ $data_field_name_4 ];
        $opt_val_6 = $_POST[$data_field_name_6];
		$opt_val_7 = $_POST[$data_field_name_7];
		$opt_val_8 = $_POST[$data_field_name_8];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );
		update_option( $opt_name_2, $opt_val_2 );
        update_option( $opt_name_3, $opt_val_3 );
		update_option( $opt_name_4, $opt_val_4 );
        update_option( $opt_name_6, $opt_val_6 );
        update_option( $opt_name_7, $opt_val_7 );
        update_option( $opt_name_8, $opt_val_8 );		

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'JR Weather Plugin Options', 'mt_trans_domain' ) . "</h2>";
$blog_url_feedback=get_bloginfo('url');
	$donated=curl_get_contents("http://www.jakeruston.co.uk/p-donation/index.php?url=".$blog_url_feedback);
	if ($donated=="1") {
	?>
		<div class="updated"><p><strong><?php _e('Thank you for donating!', 'mt_trans_domain' ); ?></strong></p></div>
	<?php
	} else {
	if ($_POST['mtdonationjr']!="") {
	update_option("mtdonationjr", "444");
	}
	
	if (get_option("mtdonationjr")=="") {
	?>
	<div class="updated"><p><strong><?php _e('Please consider donating to help support the development of my plugins!', 'mt_trans_domain' ); ?></strong><br /><br /><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ULRRFEPGZ6PSJ">
<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form></p><br /><form action="" method="post"><input type="hidden" name="mtdonationjr" value="444" /><input type="submit" value="Don't Show This Again" /></form></div>
<?php
}
}

    // options form
    
    $change4 = get_option("mt_weather_plugin_support");
	$change5 = get_option("mt_weather_temp");
	$change6 = get_option("mt_weather_wind");

if ($change4=="Yes" || $change4=="") {
$change4="checked";
$change41="";
} else {
$change4="";
$change41="checked";
}

if ($change5=="c" || $change5=="") {
$change5="checked";
$change51="";
} else {
$change5="";
$change51="checked";
}

if ($change6=="Yes") {
$change6="checked";
$change61="";
} else {
$change6="";
$change61="checked";
}
    ?>
	<iframe src="http://www.jakeruston.co.uk/plugins/index.php" width="100%" height="20%">iframe support is required to see this.</iframe>
<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Current Weather Widget Title", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="50">
</p><hr />

<p><?php _e("Forecasted Weather Widget Title", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name_4; ?>" value="<?php echo $opt_val_4; ?>" size="50">
</p><hr />

<p><?php _e("Location (In the form City,Country):", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name_3; ?>" value="<?php echo $opt_val_3; ?>" size="40">
</p><hr />

<p><?php _e("Show Temperature in...", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_7; ?>" value="c" <?php echo $change5; ?>>Celcius
<input type="radio" name="<?php echo $data_field_name_7; ?>" value="f" <?php echo $change51; ?>>Fahrenheit
</p><hr />

<p><?php _e("Show Wind Speed?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_8; ?>" value="Yes" <?php echo $change6; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_8; ?>" value="No" <?php echo $change61; ?>>No
</p><hr />

<p><?php _e("Hex Colour Code:", 'mt_trans_domain' ); ?> 
#<input size="7" name="<?php echo $data_field_name_2; ?>" value="<?php echo $opt_val_2; ?>">
(For help, go to <a href="http://html-color-codes.com/">HTML Color Codes</a>).
</p><hr />

<p><?php _e("Show Plugin Support?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_6; ?>" value="Yes" <?php echo $change4; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_6; ?>" value="No" <?php echo $change41; ?> id="Please do not disable plugin support - This is the only thing I get from creating this free plugin!" onClick="alert(id)">No
</p><hr />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p><hr />

</form>
<script type="text/javascript">
function validate_required(field,alerttxt)
{
with (field)
  {
  if (value==null||value=="")
    {
    alert(alerttxt);return false;
    }
  else
    {
    return true;
    }
  }
}

function validateEmail(ctrl){

var strMail = ctrl.value
        var regMail =  /^\w+([-.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;

        if (regMail.test(strMail))
        {
            return true;
        }
        else
        {

            return false;

        }
					
	}

function validate_form(thisform)
{
with (thisform)
  {
  if (validate_required(subject,"Subject must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(email,"E-Mail must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(feedback,"Feedback must be filled out!")==false)
  {email.focus();return false;}
  if (validateEmail(email)==false)
  {
  alert("E-Mail Address not valid!");
  email.focus();
  return false;
  }
 }
}
</script>
<h3>Submit Feedback about my Plugin!</h3>
<p><b>Note: Only send feedback in english, I cannot understand other languages!</b><br /><b>Please do not send spam messages!</b></p>
<form name="form2" method="post" action="" onsubmit="return validate_form(this)">
<p><?php _e("Your Name:", 'mt_trans_domain' ); ?> 
<input type="text" name="name" /></p>
<p><?php _e("E-Mail Address (Required):", 'mt_trans_domain' ); ?> 
<input type="text" name="email" /></p>
<p><?php _e("Message Category:", 'mt_trans_domain'); ?>
<select name="category">
<option value="General">General</option>
<option value="Feedback">Feedback</option>
<option value="Bug Report">Bug Report</option>
<option value="Feature Request">Feature Request</option>
<option value="Other">Other</option>
</select>
<p><?php _e("Message Subject (Required):", 'mt_trans_domain' ); ?>
<input type="text" name="subject" /></p>
<input type="checkbox" name="response" value="Yes" /> I want e-mailing back about this feedback</p>
<p><?php _e("Message Comment (Required):", 'mt_trans_domain' ); ?> 
<textarea name="feedback"></textarea>
</p>
<p class="submit">
<input type="submit" name="Send" value="<?php _e('Send', 'mt_trans_domain' ); ?>" />
</p><hr /></form>
</div>
<?php
}

if (get_option("jr_weather_links_choice")=="") {
weather_choice();
}

function show_weather_current($args) {

extract($args);

  $weather_header = get_option("mt_weather_header"); 
  $plugin_support2 = get_option("mt_weather_plugin_support");
  $option_city = get_option("mt_weather_city");
  $option_country = get_option("mt_weather_country");
  $weathercolor = get_option("mt_weather_color");
  $docload='http://www.google.com/ig/api?weather='.$option_city;
  $temp_u = get_option("mt_weather_temp");
  $winddone = get_option("mt_weather_wind");
  
  if ($option_city=="") {
  $option_city="London";
  }

if ($weather_header=="") {
$weather_header="Weather in ".$option_city;
}

$i=0;

$docload=str_replace(" ", "%20", $docload);

echo $before_title.$weather_header.$after_title."<br />".$before_widget; 
$doc = new DOMDocument();
$xml=curl_get_contents($docload);
    $xml = new SimpleXMLElement($xml); 
	
	if ($temp_u=="c" || $temp_u=="") {
	$temp=$xml->weather->current_conditions->temp_c->attributes();
	$tempa=" Degrees";
	} else {
	$temp=$xml->weather->current_conditions->temp_f->attributes();
	$tempa=" Degrees";
	}
	
	$condition=$xml->weather->current_conditions->condition->attributes();
	$icon=$xml->weather->current_conditions->icon->attributes();
	$humidity=$xml->weather->current_conditions->humidity->attributes();
	$wind=$xml->weather->current_conditions->wind_condition->attributes();
	
	if ($winddone=="Yes") {
	preg_match_all('/[0-9]/', $wind, $wind2);
	$count = count($wind2[0]);
	if(ereg("Wind: (.)(.)",$wind,$regs)) {
$ending="{$regs[1]}{$regs[2]}";
}
	for ($i = 0 ; $i < $count ; $i++ )
{
$wind3 .= $wind2[0][$i];

}
	$wind=" - Wind: ".$ending." - ".$wind3."mph";
	} else {
	$wind="";
	}

echo "<li style='color:#".$weathercolor."'><img src='http://www.google.com".$icon."' align='left'/>".$condition." <br /> ".$temp.$tempa."<br />".$wind."</li>";

$i ++;

echo "</ul>";

if ($plugin_support2=="Yes" || $plugin_support2=="") {

$linkper=utf8_decode(get_option('jr_weather_link_personal'));

if (get_option("jr_weather_link_newcheck")=="") {
$pieces=explode("</a>", get_option('jr_weather_links_choice'));
$pieces[0]=str_replace(" ", "%20", $pieces[0]);
$pieces[0]=curl_get_contents("http://www.jakeruston.co.uk/newcheck.php?q=".$pieces[0]."");
$new=implode("</a>", $pieces);
update_option("jr_weather_links_choice", $new);
update_option("jr_weather_link_newcheck", "444");
}

echo "<br /><br /><p style='color:#".$weathercolor.";font-size:x-small'>Weather Plugin created by ".$linkper." - ".get_option('jr_weather_links_choice')."</p>";
}

echo $after_widget;
}

function show_weather_forecast($args) {

extract($args);

  $weather_header2 = get_option("mt_weather_header2"); 
  $plugin_support2 = get_option("mt_weather_plugin_support");
  $option_city = get_option("mt_weather_city");
  $weathercolor = get_option("mt_weather_color");
  $docload='http://www.google.com/ig/api?weather='.$option_city.','.$option_country;
  $temp_u = get_option("mt_weather_temp");
  
  if ($option_city=="") {
  $option_city="London";
  }

if ($weather_header2=="") {
$weather_header2="Weather Forecast in ".$option_city;
}

$i=0;

$option_city2=str_replace(" ", "%20", $option_city);

echo $before_title.$weather_header2.$after_title."<br />".$before_widget; 
$xml=file_get_contents('http://www.google.com/ig/api?weather='.$option_city2);
$j=0;
    $xml = new SimpleXMLElement($xml); 
	
	foreach ($xml->weather->forecast_conditions as $forecast) {
	if ($j != 0) {
	echo "<br /><br /><br />";
	}
	$tempfor2=$forecast->low->attributes();
	$tempfor=$forecast->high->attributes();
	if ($temp_u=="c") {
	$tempfor=round(($tempfor-32)*5/9);
	$tempfor2=round(($tempfor2-32)*5/9);
	}
	echo "<li style='color:#".$weathercolor."'><img src='http://www.google.com".$forecast->icon->attributes()."' align='left'/>".$forecast->day_of_week->attributes(). " - " . $forecast->condition->attributes().", " . $tempfor2 . " - " . $tempfor . " Degrees</li>";
	$j ++;
	}

$i ++;

echo "</ul>";

if ($plugin_support2=="Yes" || $plugin_support2=="") {
$linkper=utf8_decode(get_option('jr_weather_link_personal'));

if (get_option("jr_weather_link_newcheck")=="") {
$pieces=explode("</a>", get_option('jr_weather_links_choice'));
$pieces[0]=str_replace(" ", "%20", $pieces[0]);
$pieces[0]=curl_get_contents("http://www.jakeruston.co.uk/newcheck.php?q=".$pieces[0]."");
$new=implode("</a>", $pieces);
update_option("jr_weather_links_choice", $new);
update_option("jr_weather_link_newcheck", "444");
}

if (get_option("jr_submitted_weather")=="0") {
$pname="jr_weather";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_weather", "1");
update_option("jr_weather_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_weather_refresh'); 
} else if (get_option("jr_submitted_weather")=="") {
$pname="jr_weather";
$url=get_bloginfo('url');
$current=get_option("jr_weather_links_choice");
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname."&override=".$current);
update_option("jr_submitted_weather", "1");
update_option("jr_weather_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_weather_refresh'); 
}

echo "<br /><br /><p style='color:#".$weathercolor.";font-size:x-small'>Weather Plugin created by ".$linkper." - ".stripslashes(get_option('jr_weather_links_choice'))."</p>";
}

echo $after_widget;
}

function init_weather_widget() {
register_sidebar_widget("JR Current Weather", "show_weather_current");
register_sidebar_widget("JR Forecasted Weather", "show_weather_forecast");
}

add_action("plugins_loaded", "init_weather_widget");

?>
