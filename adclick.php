<?php
@$nonce=$_REQUEST['_wpnonce'];
if ( !isset( $nonce ) ) {
	include('wp-includes/pluggable.php');
	if(!wp_verify_nonce($nonce, 'adclick-nonce') ) wp_die('Are you sure?'); 
}

if (!file_exists('../../../wp-config.php')) die ('wp-config.php not found');
require_once('../../../wp-config.php');

if (isset($_GET['id'])) {
	$id  = (int) sanitize_text_field($_GET['id']);
	$url = etruel_AdServe_BannerClick( $id );
	if ( !empty( $url )){
		Header("Location: " . $url);
	}else{
		Header("Location: ".get_settings('siteurl'));
	}
	echo $id, ' => ', $url;
    return 1;
}

# Add one click!
function etruel_AdServe_BannerClick($id) {
    global $wpdb,$table_prefix;
   	$table_name = $wpdb->prefix . "adserve";
    $query = "UPDATE $table_name SET  clicks=clicks+1 WHERE id=$id";
    $wpdb->query($query);
    return $wpdb->get_var("SELECT url FROM $table_name WHERE id=$id;");
}
?>