<?php
@$nonce=$_REQUEST['_wpnonce'];
if ( !isset( $nonce ) ) {
	include('wp-includes/pluggable.php');
	if(!wp_verify_nonce($nonce, 'adsave-nonce') ) wp_die('Are you sure?'); 
}

if (!file_exists('../../../wp-config.php')) die ('wp-config.php not found');
require_once('../../../wp-config.php');

if (isset($_POST['id'])) {
    Header("Location: ".etruel_AdServe_Bannersave(sanitize_text_field($_POST['id'])));
    //echo etruel_AdServe_Bannersave($_POST['id']);
    return 1;
}


# Save banner!
function etruel_AdServe_Bannersave($id) {
    global $wpdb,$table_prefix;
    $table_name = $wpdb->prefix . "adserve";
    $scriptcode = (isset($_POST['scriptcode'])) ? 1 : 0 ;
    $src = (!empty(sanitize_text_field($_POST['src'])))? sanitize_text_field($_POST['src']):'';
    $width = (!empty(sanitize_text_field($_POST['width']))) ? sanitize_text_field($_POST['width']):null;
    $height = (!empty(sanitize_text_field($_POST['height']))) ? sanitize_text_field($_POST['height']):null;

	if(sanitize_text_field($_POST['checkingdate'])=='Y'){
        $vardateto = isset($_POST['dateto']) ? date_i18n('Y-m-d H:i:s',strtotime(sanitize_text_field($_POST['dateto']))) : ''; 
        $vardateuntil = isset($_POST['dateuntil']) ? date_i18n('Y-m-d H:i:s',strtotime(sanitize_text_field($_POST['dateuntil']))) : '';
	}else{
        $vardateto = null;
        $vardateuntil = null;
    }
    if ($scriptcode){
		$ok = true;
	}else{
		$ext =  substr($src, strlen($src)-3,3 );

		if (($ext !== 'swf') || ($width != null) && ($height !== null))
			$ok = true;
		else
			$ok = false;
	}
    if ($ok) {
        $height = is_null($height) ? 'null' : $height;
        $width  = is_null($width)  ? $height : $width;
        if($id > 0) {  // update
            $query = "  UPDATE $table_name
                        SET
                            title		='".sanitize_text_field($_POST['title'])."',
                            scriptcode 	= ".$scriptcode.",
                            url			='".sanitize_text_field($_POST['url'])."',
                            src			='".urlencode($src)."',
                            user		='".sanitize_text_field($_POST['user'])."',
                            email		='".sanitize_text_field($_POST['email'])."',
                            keywords	='".sanitize_text_field($_POST['keywords'])."',
                            width		= ".$width.",
                            height		= ".$height.",
                            dateto      = '".$vardateto."',
                            dateuntil   = '".$vardateuntil."',
                            checkingdate  = '".sanitize_text_field($_POST['checkingdate'])."'

                        WHERE id=$id";
        } else {       // insert
            $query = "  INSERT INTO $table_name".
                " (title, scriptcode, url, src, email,credits, keywords, impressions, clicks, width, height,dateto,dateuntil,checkingdate) " .
                "VALUES ('".sanitize_text_field($_POST['title'])."', '".$scriptcode."', '".sanitize_text_field($_POST['url'])."', '".urlencode($src)."', '".sanitize_text_field($_POST['email'])."',-1,'"
                    .sanitize_text_field($_POST['keywords'])."',0, 0, $width, $height, '".$vardateto."', '".$vardateuntil."','".sanitize_text_field($_POST['checkingdate'])."')";
        }
		//echo $query."<br>";
        $wpdb->query($query);
    }

    return get_settings('siteurl')."/wp-admin/admin.php?page=admanage";
}
?>