<?php
if (!file_exists('../../../wp-config.php')) die ('wp-config.php not found');
require_once('../../../wp-config.php');

if (isset($_GET['id'])) {
	Header("Location: ".etruel_AdServe_BannerChangeStatus(sanitize_text_field($_GET['id']),sanitize_text_field($_GET['active'])));
    return 1;
}


# Delete banner!
function etruel_AdServe_BannerChangeStatus($id,$active) {
    global $wpdb,$table_prefix;
   	$table_name = $wpdb->prefix . "adserve";
   	$query = "UPDATE $table_name SET active=$active  WHERE id=$id";
    $wpdb->query($query);
    return get_settings('siteurl')."/wp-admin/admin.php?page=admanage";
}




?>
