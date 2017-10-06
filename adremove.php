<?php
if (!file_exists('../../../wp-config.php')) die ('wp-config.php not found');
require_once('../../../wp-config.php');

if (isset($_GET['id'])) {
	Header("Location: ".etruel_AdServe_Bannerdelete(sanitize_text_field($_GET['id'])));
    return 1;
}


# Delete banner!
function etruel_AdServe_Bannerdelete($id) {
    global $wpdb,$table_prefix;
   	$table_name = $wpdb->prefix . "adserve";
   	$query = "DELETE FROM $table_name WHERE id=$id";
    $wpdb->query($query);
    return get_settings('siteurl')."/wp-admin/admin.php?page=admanage";
}

?>
