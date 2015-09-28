<?php
if (!file_exists('../../../wp-config.php')) die ('wp-config.php not found');
require_once('../../../wp-config.php');

@$nonce=$_REQUEST['_imgnonce'];
if ( !isset( $nonce ) ) {
	include(ABSPATH . 'wp-includes/pluggable.php');
	if(!wp_verify_nonce($nonce, 'adimg-nonce') ) wp_die('Are you sure?'); 
}
//die(print_r($_REQUEST));

if(isset($_FILES["FileInput"]) && $_FILES["FileInput"]["error"]== UPLOAD_ERR_OK) {
    ############ Edit settings ##############
	$upload_dir = wp_upload_dir();
	$foldername = "WPeMyAds";
    $UploadDirectory = $upload_dir['basedir']  ."/". $foldername . "/";  //specify upload directory ends with / (slash)
	if(!file_exists($UploadDirectory)) {
        mkdir($UploadDirectory , 0755);
    }
	$UploadURL = $upload_dir['baseurl']  ."/". $foldername . "/";  //specify upload directory ends with / (slash)

    ##########################################
    
    /*
    Note : You will run into errors or blank page if "memory_limit" or "upload_max_filesize" is set to low in "php.ini". 
    Open "php.ini" file, and search for "memory_limit" or "upload_max_filesize" limit 
    and set them adequately, also check "post_max_size".
    */
    
    //check if this is an ajax request
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
        die();
    }
    
    //Is file size is less than allowed size.
    if ($_FILES["FileInput"]["size"] > 5242880) {
        die("File size is too big!");
    }
    
    //allowed file type Server side check
    switch(strtolower($_FILES['FileInput']['type']))   {
            //allowed file types
            case 'image/png': 
            case 'image/gif': 
            case 'image/jpeg': 
            case 'image/pjpeg':
            case 'application/x-shockwave-flash':
            // case 'text/plain':
            // case 'text/html': //html file
            // case 'application/x-zip-compressed':
            // case 'application/pdf':
            // case 'application/msword':
            // case 'application/vnd.ms-excel':
            case 'video/mp4':
                break;
            default:
                die('Unsupported File!'); //output error
    }
	
    $i = 1;
    $File_Name = strtolower($_FILES['FileInput']['name']);
    $File_Ext     = strrchr($File_Name, '.');    //Will return .JPEG   
	while (file_exists( $UploadDirectory. $File_Name )) {
		$File_Name = substr($File_Name, 0, strlen($File_Name)-strlen($File_Ext));
		$File_Name = $File_Name."[$i]".$File_Ext;
		$i++;
	}
	
    if(move_uploaded_file($_FILES['FileInput']['tmp_name'], $UploadDirectory.$File_Name ))   {
        // do other stuff 
        die('Success! File Uploaded. -> '. $UploadURL . $File_Name );
    }else{
        die('error uploading File!');
    }
    
} else {
    die('Something wrong with upload! Is "upload_max_filesize" set correctly?');
}


?>