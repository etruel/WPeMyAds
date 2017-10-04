<?php
@$nonce=$_REQUEST['_wpnonce'];
if ( !isset( $nonce ) ) {
	include('wp-includes/pluggable.php');
	if(!wp_verify_nonce($nonce, 'adsave-nonce') ) wp_die('Are you sure?'); 
}

if (!file_exists('../../../wp-config.php')) die ('wp-config.php not found');
require_once('../../../wp-config.php');

if (isset($_POST['id'])) {
    Header("Location: ".etruel_AdServe_Bannersave($_POST['id']));
    //echo etruel_AdServe_Bannersave($_POST['id']);
    return 1;
}


# Save banner!
function etruel_AdServe_Bannersave($id) {
    global $wpdb,$table_prefix;
    $table_name = $wpdb->prefix . "adserve";
    $scriptcode = (isset($_POST['scriptcode'])) ? 1 : 0 ;
    $src = (!empty($_POST['src']))?$_POST['src']:'';
    $width = (!empty($_POST['width']))?$_POST['width']:null;
    $height = (!empty($_POST['height']))?$_POST['height']:null;

	if(sanitize_text_field($_POST['checkingdate'])=='Y'){
        $vardateto = isset($_POST['dateto']) ? date_i18n('Y-m-d H:i:s',strtotime($_POST['dateto'])) : ''; 
        $vardateuntil = isset($_POST['dateuntil']) ? date_i18n('Y-m-d H:i:s',strtotime($_POST['dateuntil'])) : '';
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
                            title		='".$_POST['title']."',
                            scriptcode 	= ".$scriptcode.",
                            url			='".$_POST['url']."',
                            src			='".urlencode($src)."',
                            user		='".$_POST['user']."',
                            email		='".$_POST['email']."',
                            keywords	='".$_POST['keywords']."',
                            width		= ".$width.",
                            height		= ".$height.",
                            dateto      = '".$vardateto."',
                            dateuntil   = '".$vardateuntil."',
                            checkingdate  = '".$_POST['checkingdate']."'

                        WHERE id=$id";
        } else {       // insert
            $query = "  INSERT INTO $table_name".
                " (title, scriptcode, url, src, email,credits, keywords, impressions, clicks, width, height,dateto,dateuntil,checkingdate) " .
                "VALUES ('".$_POST['title']."', '".$scriptcode."', '".$_POST['url']."', '".urlencode($src)."', '".$_POST['email']."',-1,'"
                    .$_POST['keywords']."',0, 0, $width, $height, '".$vardateto."', '".$vardateuntil."','".$_POST['checkingdate']."')";
        }
		//echo $query."<br>";
        $wpdb->query($query);
    }

    return get_settings('siteurl')."/wp-admin/admin.php?page=admanage";
}
?>