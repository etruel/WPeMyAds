<?php
/*
Plugin Name: WPeMyAds
Plugin URI: http://www.netmdp.com/myads
Description: Ads server for WordPress
Version: 1.0
Author: etruel
Author URI: http://www.netmdp.com
*/

function etruel_AdServe_AddPages() {
	# Crea la tabla si no existe
	global $wpdb;
	$table_name = $wpdb->prefix . "adserve";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		etruel_AdServe_CreateTable();
	}else{
		etruel_altertable_ads();
	}
	//update zones
	etruel_Adserve_update_banners();
	# add submenu
	$mypage = add_menu_page('My Ads', 'My Ads', 'publish_posts',  'admanage', 'etruel_AdServe_Manage',plugin_dir_url( __FILE__ ) . 'files/Ad20.png');
	add_submenu_page('admanage', 'Ads Manager', 'Ads Manager', 'publish_posts', 'admanage', 'etruel_AdServe_Manage');
	add_submenu_page('admanage', 'Zones Preview', 'Zones Preview', 'publish_posts', 'zonemanage', 'etruel_AdServe_zones');
	// Dentro de Escritorio para que la vean todos
	add_submenu_page( 'index.php', 'My Ads Report', 'My Ads Report', 'read', 'adreport', 'etruel_AdServe_Dashboard');

}

function etruelmyads_load_scripts($hook) {	
	global $pagenow;
	wp_enqueue_script( 'jquery' );
	if ( 'widgets.php' == $pagenow ) {
		add_action( "admin_print_scripts", 'WPeMyAds_widget_css',9999 );	
	}
	if('dashboard_page_adreport' == $hook ) {
		wp_enqueue_style( 'myads-style', plugin_dir_url( __FILE__ ) . 'files/myads.css' );
		return;
	}
	if('toplevel_page_admanage' != $hook && 'my-ads_page_zonemanage' != $hook) //wp_die($hook); 
	return;
	wp_enqueue_script( 'jquery-form' );
	wp_enqueue_script( 'myads-scripts', plugin_dir_url( __FILE__ ) . 'files/myads.js', array( 'jquery' ), false, true );
	wp_enqueue_style( 'myads-style', plugin_dir_url( __FILE__ ) . 'files/myads.css' );
	wp_enqueue_style( 'oplugincss', plugin_dir_url( __FILE__ ) .'files/oplugins.css');
	wp_enqueue_style( 'datetimepickercss', plugin_dir_url( __FILE__ ) .'files/jquery.datetimepicker.css');	
	//script
	wp_enqueue_script( 'opluginjs', plugin_dir_url( __FILE__ ) .'files/oplugins.js');
	wp_enqueue_script('datetimepicker',plugin_dir_url(__FILE__).'files/jquery.datetimepicker.js',array("jquery"));

	add_action( "admin_print_scripts", 'AdServe_admin_head',9999 );	
}
add_action('admin_enqueue_scripts', 'etruelmyads_load_scripts');

/**
* Register WPeMyAds widget.
* Calls 'widgets_init' action after the etruel Backlinks widget has been registered.
*/
require_once( plugin_dir_path( __FILE__ ) . 'widget.php');
function WPeMyAdsInit() {
	register_widget('WPeMyAds');
}	
add_action('widgets_init', 'WPeMyAdsInit');

function WPeMyAds_widget_css() { 
	?>
<style type="text/css">
div[id*=wpemyads] .widget-top {background: white url('<?php echo plugin_dir_url( __FILE__ ) . 'files/Ad40n.png'; ?>')  no-repeat 4px center;}
div[id*=wpemyads] .widget-top .widget-title{padding-left: 28px;}
</style>
	<?php
}
	
function AdServe_admin_head() { 
	?>

	<script type="text/javascript">
		function stripslashes (str) {
			stri = (str + '').replace(/\\(.?)/g, function (s, n1) {
				switch (n1) {
					case '\\': return '\\';
					case '0': return '\u0000';
					case '': return '';
					default: return n1;        
				}
			});
			return decodeURIComponent(stri.replace(/\+/g, ' '));
		}
		// Escapes single quote, double quotes and backslash characters in a string with backslashes  
		function addslashes (str) {
			return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
		}
		
		function showform(formid){
			//alert(jQuery(window).width());
			fleft = jQuery(window).width()/2 - jQuery(formid).width()/2 ;
			ftop = jQuery(window).height()/2 - jQuery(formid).height()/2 ;
			jQuery(formid).attr("style", 'left:'+fleft+"px;top:"+ ftop+"px;");
			//jQuery(formid).css("top", top);
			jQuery(formid).fadeIn();
		}
		
		function loadData(id, title, scriptcode, url, src, email, keywords, width, height, user,dateto, dateuntil,checkingdate){
			jQuery('#tableform .wrap h2').text('Edit Ad'); 
			document.getElementById('buttonsave').value='Save'; 
			document.getElementById('id').value = id; 
			document.getElementById('title').value = title; 
			document.getElementById('url').value = url; 
			document.getElementById('user').value = user; 
			document.getElementById('dateto').value = dateto;
			document.getElementById('dateuntil').value = dateuntil;
			if(checkingdate=='Y'){
				jQuery('#checkingdate').attr("checked",true);
				jQuery("#datefields_ads").show(500);
			}else{
				jQuery("#datefields_ads").hide(0);
				jQuery("#dateto").val("");
				jQuery("#dateuntil").val("");
			}
			if(scriptcode==1){
				jQuery(".isscript_src").html('<textarea id="src" name="src" cols=80 rows=5></textarea>');
			}else{
				jQuery(".isscript_src").html('<input type="text" id="src" name="src" value="" size="60">');
			}			
			document.getElementById('scriptcode').value = changeval(scriptcode);
			document.getElementById('src').value = stripslashes(unescape(src)); 
			document.getElementById('email').value = email; 
			document.getElementById('keywords').value = keywords;
			document.getElementById('width').value = width; 
			document.getElementById('height').value = height;
			showform("#tableform");
			return;
		};
		
		function changeval(newval){
			var sc = jQuery("#scriptcode");
			sc.val(newval);
			if(newval==1){
				sc.prop("checked", true);
				jQuery(".isscript").fadeOut("fast");
				jQuery("#uploadf").hide();
				jQuery(".isscript_src").html('<textarea id="src" name="src" cols=80 rows=5>'+document.getElementById('src').value +'</textarea>');
			}else{
				sc.prop("checked", false);
				jQuery(".isscript").fadeIn("slow");
				jQuery("#uploadf").show();
				scri = '';  //addslashes(document.getElementById('src').value);
				jQuery(".isscript_src").html('<input type="text" id="src" name="src" value="'+ scri +'" size="60">');
			}			
		}
		
		jQuery(document).ready( function($j) {

			$j("#closefrm").click(function() {
				jQuery('#uploadform').hide()
				jQuery('#tableform').fadeOut(); 
			});
			$j("#closeupload").click(function() {
				$j('#uploadform').fadeOut(); 
			});
			$j("#uploadf").click(function() {
				showform('#uploadform'); 
			});
			$j("#reset").click(function() {   //New ad
				jQuery('#uploadform').hide()
				$j('#adform')[0].reset();
				document.getElementById('buttonsave').value='Add'; 
				$j('#tableform .wrap h2').text('Add Ad'); 
				document.getElementById('id').value='0'; 
				document.getElementById('src').value=''; 
				changeval(0);
				$j("#title").focus();
				showform("#tableform");
			});
			
			$j("#scriptcode").click(function() {
				if ( true == $j(this).is(':checked')) {
					changeval(1);
				}else{
					changeval(0);
				}
			});

			//date piker
			jQuery('#dateto,#dateuntil').datetimepicker({
				 format:'Y-m-d H:m:s',
			     minDate: getFormattedDate(new Date())
					
			});
			jQuery('#checkingdate').click(function(){
				if(jQuery(this).is(':checked')){
					jQuery("#datefields_ads").show(500);	
				}else{
					jQuery("#datefields_ads").hide(0);
					jQuery("#dateto").val("");
					jQuery("#dateuntil").val("");
				}
			});

			function getFormattedDate(date) {
			    var day = date.getDate();
			    var month = date.getMonth() + 1;
			    var year = date.getFullYear().toString().slice(2);
			    return day + '-' + month + '-' + year;
			}

		});
	</script>
	<?php
}

/**
 * Checks if a particular user has a role. 
 * Returns true if a match was found.
 *
 * @param string $role Role name.
 * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
 * @return bool
 */
if( !function_exists('etruel_check_user_role') ) {
 function etruel_check_user_role( $role, $user_id = null ) {
 
    if ( is_numeric( $user_id ) )
	$user = get_userdata( $user_id );
    else
        $user = wp_get_current_user();
 
    if ( empty( $user ) )
	return false;
 
    return in_array( $role, (array) $user->roles );
}
}

function get_etruel_AdServe_zones() {
	global $wpdb;
	$table_name = $wpdb->prefix . "adserve";
//	$adzones = get_option('etruel_adzones');
	
	$qry = $wpdb->get_results("SELECT DISTINCT keywords, width , height FROM $table_name ORDER BY keywords ASC, width DESC, height DESC;");
	return $qry;
}


function etruel_AdServe_zones() {
	$qry = get_etruel_AdServe_zones();
	print "<div class='wrap'><h2>Ads Zones</h2>
	<table class='widefat'><thead><tr><th scope='col'>Zones</th><th scope='col'>Width</th><th scope='col'>Height</th></tr></thead>";
	print "<tbody id='the-list'>";
	$first=false;
	$i=0;
	foreach ($qry as $rk) {
		$first=true;
		print "<tr id='fila". $i++ . "'>";
		print "<td>".$rk->keywords."</td>\n";
		print "<td>".$rk->width."</td>\n";
		print "<td>".$rk->height."</td>\n";
		print "</tr>";
	}
	print "</table>"; 	
	
}

function etruel_AdServe_Manage() {
	global $wpdb, $user_email, $user_login;
	$table_name = $wpdb->prefix . "adserve";
    
	# Tabla OVERVIEW
    //$lastmonth = date('Ym', mktime(0, 0, 0, date("m")-1 , date("d") - 1, date("Y")));
    //$yesterday = date('Ymd', time()-86400);
	?>	
	<div class='wrap'>
		<h2>My Ads</h2>  <input type='button' class='button' name='reset' id='reset' value='New'>
	<div id="side-info-column" class="inner-sidebar">
		<?php include( plugin_dir_path( __FILE__ ) . 'myplugins.php');	?>
	</div>	
	<?php
	print "<div id='table-content'><table class='widefat AdsTable'><thead><tr><th scope='col'>Site</th><th scope='col'>Zones</th><th scope='col'>Active</th><th scope='col'>Impressions</th><th scope='col'>Clicks</th><th scope='col'>Ratio</th><th scope='col'>Credits</th>";
	if(etruel_check_user_role('administrator') ) print "<th scope='col'>".__('User')."</th>";
	print"<th scope='col'>Actions</th>
	<th>A/D</th>
	</tr></thead>";
	print "<tbody id='the-list'>";
	$qry = $wpdb->get_results("SELECT * FROM $table_name ORDER BY active DESC, credits DESC;");
	
	$first=false;
	foreach ($qry as $rk) {
		$first=true;
		$user = ($rk->user !=null && $rk->user!="") ? $rk->user : $user_login ;  // si no tiene usuario le asigno el actual
		//$user = (etruel_check_user_role('administrator')) ? $user : $user ;
		$script = $rk->src;
	//	$vardateto = date_i18n('d/m/Y H:m:s',strtotime($rk->dateto)); 
	//	$vardateuntil = date_i18n('d/m/Y H:m:s',strtotime($rk->dateuntil));  
		$vardateto = $rk->dateto;
		$vardateuntil = $rk->dateuntil;
		$editform="loadData('{$rk->id}', '{$rk->title}', '{$rk->scriptcode}', '{$rk->url}', '$script', '{$rk->email}', '{$rk->keywords}', '{$rk->width}', '{$rk->height}','$user','{$vardateto}','{$vardateuntil}','{$rk->checkingdate}');";
		print "<tr id='fila{$rk->id}' class='adfila'>";
		$tdtitle = "<td>";
		if ($rk->url === '') {
			$tdtitle .= "<strong>{$rk->title}</strong>";
		}else{
			$tdtitle .= "<a target='_Blank' title='". __('open site on new window') ."' href='{$rk->url}'><strong>{$rk->title}</strong></a>";
		}
		if(etruel_check_user_role('administrator') || $rk->user == $user_login || $user ==$user_login )
			$tdtitle .= "<br /><a class='adedit' title='".__('edit')."' onclick=\"".$editform."\">".__('edit')."</a>";
		else 
			$tdtitle .=  "<br />" . __("user"). ": <b>$user</b>";
		$tdtitle .= "</td>";
		echo $tdtitle;
		print "<td>".$rk->keywords."</td>\n";
#		print "<td>".$rk->weight."</td>\n";
		print "<td>".etruel_iif($rk->active == 1,"Yes","No")."</td>\n";
		
		print "<td>".$rk->impressions."</td>\n";
		print "<td>".$rk->clicks."</td>\n";
		print "<td>".number_format($rk->clicks/($rk->impressions+1)*100,1)." %</td>\n";

		print "<td>".$rk->credits."</td>\n";
		if(etruel_check_user_role('administrator'))  print "<td>$user</th>";

		if(etruel_check_user_role('administrator') || $rk->user == $user_login || $user ==$user_login ) {
			print "<td><a class='adaction' title='edit' onclick=\"".$editform."\"><img src='" .plugin_dir_url( __FILE__ ) . "files/edit.gif'></a>";
			$url= plugin_dir_url( __FILE__ ) . "adremove.php?id=$rk->id";
			print "<a class='adaction' href=$url onclick=\"return confirm(".__('Are you sure you want to delete?').")\">";
			print "<img src='" .plugin_dir_url( __FILE__ ) . "files/delete.gif'></a></td>\n";
		}else{
			print "<td><a style='border:0px;' title='' onclick='return false;'><img src='" .plugin_dir_url( __FILE__ ) . "files/noedit.gif' style='border: 0px solid #AB6400; margin:0; padding:0;'></a> ";
			print "<a onclick=\"return false;\" style='border:0px;'>";
			print "<img src='" .plugin_dir_url( __FILE__ ) . "files/nodelete.gif' style='border: 0px solid #AB6400; margin:0; padding:0;'></a></td>\n";
		}
		if($rk->active!=1){
			$estatus_message = 'Activate';
			$value_estatus = 1;
		}else{
			$estatus_message = 'Desactivate';
			$value_estatus = 0;
		}		
		$url_active = plugin_dir_url( __FILE__ ) . "adchangestatus.php?id=$rk->id&active=$value_estatus";
		?>
			<td><a href="<?php echo $url_active;  ?>"><?php echo $estatus_message; ?></a></td>
		<?php 
		print "</tr>";
	}
	print "</table></div>";
	$nonce = wp_create_nonce  ('adsave-nonce');
	$imgnonce = wp_create_nonce  ('adimg-nonce');
	?>
	<div id="tableform" style="display: <?php echo (!$first)?"block":"none";  ?>;">
   	<table><tr>
	<td>
		<div class="wrap"><div id="closefrm" title="Cancel and close">[x]</div>
		<center><h2><?php echo (!$first)?"Add":"Edit";  ?> Ad</h2></center>
		<form name="adform" id="adform" method="POST" action="<?php echo plugin_dir_url( __FILE__ ) . "adsave.php"; ?>">
		<input type="hidden" name="id" id="id" value="">
		<table>
			<tr>
				<td><span class="number" style="font-size:14px;">1</span></td>
				<td><span style="color:#1ABC9C; font-size:14px; font-weight:bold;">Ad Details</span></td>
			</tr>
			<tr><td>Title</td><td><input type="text" name="title" id="title" value="" size="60"></td></tr>
			<tr><td><label for="scriptcode">Script</label></td><td><input class="checkbox" type="checkbox" name="scriptcode" value="0" id="scriptcode"/> </td></tr> 
			<tr class="isscript"><td>Url</td><td><input type="text" name="url" id="url" value="" size="60"></td></tr>
			<tr><td>*Src</td><td><span class="isscript_src"><input type="text" id="src" name="src" value="" size="60"></span><input type='button' class='button' id='uploadf' value='Upload'>
				<span>Ej: http://domain.com/image.jpg|.png|.gif|.swf</span></td></tr>
			<!--properties-->
			<tr>
				<td><span class="number" style="font-size:14px;">2</span></td>
				<td style="color:#1ABC9C; font-size:14px; font-weight:bold;">Ad properties</td>
			</tr>
			<tr><td>Zones *</td><td><input type="text" name="keywords" id="keywords" value="" size="30"> <?php
					$zones = array();
					$xzones=get_etruel_AdServe_zones();
					$doSelect= "<select name=\"selectzone\" id='selectzone'>\n";
					$doSelect.="\t\t\t<option value='0'> </option>\n";
					$jsz="";
					foreach ($xzones as $rk) {
						$doSelect.="\t\t\t<option value='{$rk->keywords}' ";
						$doSelect.=">".$rk->keywords."</option>\n";
		
						$zones[$rk->keywords]['width'] = $rk->width;
						$zones[$rk->keywords]['height'] = $rk->height;
						$jsz .= "\t\t\tjzone['{$rk->keywords}'] =  [ '{$rk->width}' , '{$rk->height}' ];\n";
					}
					$doSelect.="\t\t\t</select>";
					echo $doSelect;
			?></td></tr>
			<tr><td>Ancho *</td><td><input type="text" name="width" id="width" value=""></td></tr>  			
			<tr><td>*Alto </td><td><input type="text" name="height" id="height" value=""></td></tr>   
			<?php if(etruel_check_user_role('administrator') ) : ?>
				<tr><td>User</td><td>
				<?php 
					$userobj = get_user_by( 'login', $user );
					$userid = $userobj->ID;
					etruel_wp_dropdown_users(array('show'=>'user_login', 'name' => 'user', 'selected' =>$userid,'include_selected' => true )); 
				?></td></tr>
			<?php else: ?>
				<tr><td></td><td><input  type="hidden" name="user" value="<?php echo $user; ?>" id="user"></td></tr>
			<?php endif; ?>
			<tr><td>e-mail</td><td><input type="text" name="email" id="email" value="" size="40"></td></tr>
			<tr>
				<td><b>Checking Date:</b></td>
				<td><input type="checkbox" value="Y" id="checkingdate" name="checkingdate"></td>
			</tr>
			<tr id="datefields_ads" style="display:none;">
				<td>Date To:</td>
				<td>
					<input class="fieldate time-input" type="text" name="dateto" id="dateto">
					<span>Date Until</span>
					<input class="fieldate time-input" type="text" name="dateuntil" id="dateuntil">
				</td>
			</tr>
			<tr><td><input  type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" id="_wpnonce">
			</td></tr>
			<tr><td><input  class="button-primary"type="submit" name="vai" value="Add" id="buttonsave"></td></tr>
		</table>
	</form>

	<div id="uploadform" style="display: none;"><div id="closeupload" title="Cancel and close">[x]</div>
		<form action="<?php echo plugin_dir_url( __FILE__ ) . "processupload.php"; ?>" method="post" enctype="multipart/form-data" id="MyUploadForm">
			<input name="FileInput" id="FileInput" type="file" />
			<input type="submit"  id="submit-btn" value="Upload" />
			<input  type="hidden" name="_imgnonce" value="<?php echo $imgnonce; ?>" id="_imgnonce">
			<img src="<?php echo plugin_dir_url( __FILE__ ); ?>files/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/>
		</form>
		<div id="progressbox" ><div id="progressbar"></div ><div id="statustxt">0%</div></div>
		<div id="output"></div>
	</div>
	
	<script>
		jQuery(function($){
			var jzone = [];
<?php echo $jsz; ?>
			$("#selectzone").change(function(){
				 $("#keywords").val( $(this).val() );
				  $("#width").val( jzone[$(this).val()][0] );
				  $("#height").val( jzone[$(this).val()][1] );
			});
		});
	</script>
	</div></td></tr>
	</table>
	</div>
	</div>
<?php
}

function etruel_wp_dropdown_users( $args = '' ) {
    $defaults = array(
        'show_option_all' => '', 'show_option_none' => '', 'hide_if_only_one_author' => '',
        'orderby' => 'display_name', 'order' => 'ASC',
        'include' => '', 'exclude' => '', 'multi' => 0,
        'show' => 'display_name', 'echo' => 1,
        'selected' => 0, 'name' => 'user', 'class' => '', 'id' => '',
        'blog_id' => $GLOBALS['blog_id'], 'who' => '', 'include_selected' => false,
        'option_none_value' => -1
    );
 
    $defaults['selected'] = is_author() ? get_query_var( 'author' ) : 0;
 
    $r = wp_parse_args( $args, $defaults );
    $show = $r['show'];
    $show_option_all = $r['show_option_all'];
    $show_option_none = $r['show_option_none'];
    $option_none_value = $r['option_none_value'];
 
    $query_args = wp_array_slice_assoc( $r, array( 'blog_id', 'include', 'exclude', 'orderby', 'order', 'who' ) );
    $query_args['fields'] = array( 'ID', 'user_login', $show );
    $users = get_users( $query_args );
 
    $output = '';
    if ( ! empty( $users ) && ( empty( $r['hide_if_only_one_author'] ) || count( $users ) > 1 ) ) {
        $name = esc_attr( $r['name'] );
        if ( $r['multi'] && ! $r['id'] ) {
            $id = '';
        } else {
            $id = $r['id'] ? " id='" . esc_attr( $r['id'] ) . "'" : " id='$name'";
        }
        $output = "<select name='{$name}'{$id} class='" . $r['class'] . "'>\n";
		$found_selected = false;
        foreach ( (array) $users as $user ) {
            $user->ID = (int) $user->ID;
            $_selected = selected( $user->ID, $r['selected'], false );
            if ( $_selected ) {
                $found_selected = true;
            }
            $display = ! empty( $user->$show ) ? $user->$show : '('. $user->user_login . ')';
            $output .= "\t<option value='$user->user_login'$_selected>" . esc_html( $display ) . "</option>\n";
        }
 
        if ( $r['include_selected'] && ! $found_selected && ( $r['selected'] > 0 ) ) {
            $user = get_userdata( $r['selected'] );
            $_selected = selected( $user->ID, $r['selected'], false );
            $display = ! empty( $user->$show ) ? $user->$show : '('. $user->user_login . ')';
            $output .= "\t<option value='$user->user_login'$_selected>" . esc_html( $display ) . "</option>\n";
        }
        $output .= "</select>";
    }
	$html = apply_filters( 'wp_dropdown_users', $output );
    if ( $r['echo'] ) {
        echo $html;
    }
    return $html;
}

//change status banner
function etruel_AdServe_changeStatus($id) {
    global $wpdb,$table_prefix;
   	$table_name = $wpdb->prefix . "adserve";
   	$query = "UPDATE $table_name SET active=0  WHERE id=$id";
    $wpdb->query($query);
}



function etruel_AdServe_Dashboard() {
	global $wpdb;
	global $user_email;
	global $user_login;
	$table_name = $wpdb->prefix . "adserve";
	print "<div class='wrap'><h2>Your Ads</h2><table class='widefat tabreport'><thead><tr><th scope='col'>Site</th><th scope='col'>Zones</th><th scope='col'>Active</th><th scope='col'>Impressions</th><th scope='col'>Clicks</th><th scope='col'>Ratio</th><th scope='col'>Credits</th></tr></thead>";
	print "<tbody id='the-list'>";
	$qry = $wpdb->get_results("SELECT * FROM $table_name WHERE user='$user_login' ORDER BY active DESC, credits DESC;");
	foreach ($qry as $rk) {
		$src = urldecode($rk->src);
		$ext =  substr($src, strlen($src)-3,3 );
		$urlbase = get_option('siteurl') . '/';
		if(substr($src, 0 ,4 ) == 'http'){
			$urlimg = $src;
		}else{
			$urlimg = $urlbase . $src;
		}
		print "<tr>";
		print "<td style='align:center;text-align:center;'><a href='".$rk->url."'><strong>".$rk->title."</strong></a><br /><img class='myadpreview' border=0 src='".$urlimg."'></td>";
		print "<td>".$rk->keywords."</td>\n";
		print "<td>".etruel_iif($rk->active == 1,"Yes","No")."</td>\n";
		print "<td>".$rk->impressions."</td>\n";
		print "<td>".$rk->clicks."</td>\n";
		print "<td>".number_format($rk->clicks/($rk->impressions+1)*100,1)." %</td>\n";
		print "<td>".$rk->credits."</td>\n";
		print "</tr>";
	}
    print "</tbody></table></div>";
}

function etruel_AdServe_CreateTable() {
	global $wpdb, $wp_db_version;
	$table_name = $wpdb->prefix . "adserve";
	$sql_createtable = "CREATE TABLE " . $table_name . " (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	user text,
	active tinyint,
	date text,
	title text,
	scriptcode boolean,
	url text,
	src text,
    height int,
    width int,
	email text,
	credits int,
	impressions int,
	keywords text,
	weight tinyint,
	clicks int,
	dateto timestamp,
	dateuntil timestamp,
	UNIQUE KEY id (id)
	);";
	$page = 'wp-admin/includes/upgrade.php';  
	require_once(ABSPATH . $page);
	dbDelta($sql_createtable);
}

function etruel_altertable_ads(){
	global $wpdb, $wp_db_version;
	$table_name = $wpdb->prefix."adserve";
	$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
	WHERE table_name = '$table_name' AND column_name = 'dateuntil'"  );
	if(empty($row)){
	   $wpdb->query("ALTER TABLE  wp_adserve ADD dateto timestamp NULL");
	   $wpdb->query("ALTER TABLE wp_adserve ADD dateuntil timestamp NULL");
	   $wpdb->query("ALTER TABLE wp_adserve ADD checkingdate char(1) NULL");
	}
}
function etruel_Adserve_update_banners(){
	global $wpdb, $wp_db_version;
	$table_name = $wpdb->prefix."adserve";
	$datetemp = date_i18n('Y-m-d H:i:s');
	
	//Activate Date To
	$wpdb->query("UPDATE $table_name SET active=1 WHERE checkingdate='Y'  AND UNIX_TIMESTAMP('".$datetemp."') >= UNIX_TIMESTAMP(dateto) ");
	//Desactivate limit date until
	$wpdb->query("UPDATE $table_name SET active=0 WHERE checkingdate='Y' AND UNIX_TIMESTAMP('".$datetemp."') >= UNIX_TIMESTAMP(dateuntil)");
	$wpdb->query("UPDATE $table_name SET active=0 WHERE checkingdate='Y' AND UNIX_TIMESTAMP('".$datetemp."') < UNIX_TIMESTAMP(dateto)");
}	

function etruel_iif($expression, $returntrue, $returnfalse = '') {
    return ($expression ? $returntrue : $returnfalse);
} 

function etruel_AdServe_GetBanner($zone='') {
	etruel_Adserve_update_banners();
	if($zone==='') return '';
	global $wpdb;
	global $userdata;
	$table_name = $wpdb->prefix . "adserve";
	$ret="";
	# get banner
	$wherecond="((credits = -1) or (credits > 0)) AND (active = 1) AND (concat(keywords,' ') LIKE '%".$zone." %')";
	$numrows = $wpdb->get_var("SELECT count(id) FROM $table_name WHERE $wherecond;");
	if($numrows > 0) {
		usleep(2000);
		$bannum = mt_rand(1, $numrows)-1;
	    if ($bannum>=0) {
			$rk = $wpdb->get_row("SELECT * FROM $table_name WHERE $wherecond ORDER BY RAND( ) LIMIT 1 OFFSET $bannum;");
			$src = urldecode($rk->src);
			$ret.="\n\n<!-- Begin AdServe code : banner:$zone-$bannum/$numrows -->\n";
			if( $rk->scriptcode ) {
				//$ret.= $src;  // imprime el texto literal grabado
				$ret.= stripslashes($src)."\n";
			}else{ //es una imagen o flash
				$nonce= wp_create_nonce  ('adclick-nonce');
				$ext =  substr($src, strlen($src)-3,3 );
				$urlbase = get_option('siteurl') . '/';
				if(substr($src, 0 ,4 ) == 'http'){
					$urlimg = $src;
				}else{
					$urlimg = $urlbase . $src;
				}
				if (( strcasecmp("jpg", $ext) == 0) || ( strcasecmp("gif", $ext) == 0) || ( strcasecmp("png", $ext) == 0)){

						$ret.="<a target='_blank' href='". plugin_dir_url( __FILE__ ) . "adclick.php?id={$rk->id}&_wpnonce={$nonce}' style='margin:0px;border:0px;'><img src='". $urlimg . "' alt='$rk->title' /></a>";
				}else{
					$ret = '<script type="text/javascript">swfobject.embedSWF("'. $urlimg. '", "'.$zone.'", "'.$rk->width.'", "'.$rk->height.'", "9.0.0", "expressInstall.swf");</script><div id="'.$zone.'"><p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p></div>';
				}
			}
			$ret.="\n<!-- End AdServe code -->\n\n";
			get_currentuserinfo();
			if($userdata->user_login != $rk->user) {
				if($rk->credits > 0) {
					$results = $wpdb->query( "update $table_name set credits=credits-1 where id=$rk->id" );
				}
				$results = $wpdb->query( "update $table_name set impressions=impressions+1 where id=$rk->id" );
			}
		}
	}
	return $ret;
}

function AdServe($zone='') {
	print etruel_AdServe_GetBanner($zone);
}


function etruel_AdServe_Filter($the_content) {
	while($p=strpos($the_content, "[!AdServe")) {
		$pend=strpos($the_content, "!]",$p);
		$zone=substr($the_content,$p+10,$pend-$p-10);
		$the_content=str_replace("[!AdServe:$zone!]",etruel_AdServe_GetBanner($zone),$the_content);
	}
	return $the_content;
}

add_action('admin_menu', 'etruel_AdServe_AddPages');
add_filter('the_content', 'etruel_AdServe_Filter', 99);

?>
