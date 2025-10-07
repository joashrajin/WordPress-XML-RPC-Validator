<?php
require_once 'commons.php';

$xml_rpc_validator_utils->logging_buffer = ''; //reset the logging buffer on each ajax request

// check security
check_ajax_referer( "xml-rpc-ajax-nonce" );

$site_url = isset($_POST['site_url']) ? $_POST['site_url'] : '';
$site_url = esc_url($site_url);

$xmlrpc_url = isset($_POST['xmlrpc_url']) ? $_POST['xmlrpc_url'] : '';
$xmlrpc_url = esc_url($xmlrpc_url);

$client = new Blog_Validator($site_url);
$client->xmlrpc_endpoint_URL = $xmlrpc_url;
	
$user_login = strip_tags(stripslashes($_POST['user_login']));
$user_pass = strip_tags(stripslashes($_POST['user_pass']));
$client->setWPCredential($user_login, $user_pass);

//Set the UserAgent
$user_agent_selected = esc_attr( $_REQUEST['user_agent'] );
$client -> setUserAgent( $user_agent_selected );

$enable_401_auth = ! empty( $_POST['enable_401_auth'] );
if($enable_401_auth) {
	xml_rpc_validator_logIO("O", "HTTP auth enabled");
	$HTTP_auth_user_login = strip_tags(stripslashes($_POST['HTTP_auth_user_login']));
	$HTTP_auth_user_pass = strip_tags(stripslashes($_POST['HTTP_auth_user_pass']));
	$client -> setHTTPCredential($HTTP_auth_user_login, $HTTP_auth_user_pass);
}

$method_name = isset($_POST['method_name']) ? $_POST['method_name'] : '';
if ( empty( $method_name ) ) echo json_encode( array("error", 'Internal Error, please try later.' ) );

if( 'check_wp_version' == $method_name) {
	//do not check the WP version on WP.COM
	if ( strripos( $xmlrpc_url, 'wordpress.com/xmlrpc.php') !== false ) {
		$result = true;
	} else {
		$result = $client->execute_call('wp.getOptions');
		$result = $client->check_wp_version( $result );
	}
} elseif ( 'wp.getComments' == $method_name) {
	$result = $client->execute_call($method_name,  array('offset' => 0, 'number' => 10) );
} elseif ( 'metaWeblog.newMediaObject' == $method_name) {
	$pictureData = file_get_contents( constant( 'XMLRPC_VALIDATOR_PLUGIN_DIR' ).'/'.'test_picture.jpg' );
	$result = $client->execute_call($method_name,  array('name' => mt_rand().'.jpg', 'type' => 'image/jpg', 'bits' => new IXR_Base64( $pictureData ) ) );
} else {
	$result = $client->execute_call($method_name);
}

if( is_wp_error( $result ) ) {
	$error_msgs = $xml_rpc_validator_utils->printErrors($result);
	echo json_encode( array("error", $error_msgs, $xml_rpc_validator_utils->logging_buffer ) );
} else {
	echo json_encode( array("ok", $xml_rpc_validator_utils->logging_buffer ) );
}
?>

