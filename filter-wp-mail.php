<?php

function filter_wp_mail( $args ) {
	/**
	 * check for spam in $args['message']
	 */
	//$string   = strtolower($posted_message);
	$string   = mb_strtolower($args['message'], 'UTF-8'); // multi language
	$badwords = humsayaa_badwords();
	// https://gist.github.com/anthonybudd/9c32cc1698cb8b1d6144944a402e3c1c
	if(in_array(true,array_map(function($e)use($string){return strpos($string,$e)!==false;},$badwords))) {
		// Modify the options here
		$custom_mail = array(
			'to'          => '', // $args['to'],
			'message'     => '', // $args['message'],
			//    'subject'     => $args['subject'],
			//    'headers'     => $args['headers'],
			//    'attachments' => $args['attachments'],
		);
	}

	/**
	 * check for spamy IPs
	 */
	$list_ip = humsayaa_get_ip();
	$bad_ips = humsayaa_bad_ips();
	// https://gist.github.com/anthonybudd/9c32cc1698cb8b1d6144944a402e3c1c
	if(in_array(true,array_map(function($e)use($list_ip){return strpos($list_ip,$e)!==false;},$bad_ips))) {
		// Modify the options here
		$custom_mail = array(
			'to'          => '', // $args['to'],
			'message'     => '', // $args['message'],
			//    'subject'     => $args['subject'],
			//    'headers'     => $args['headers'],
			//    'attachments' => $args['attachments'],
	 	);
	}

	// Return the value to the original function to send the email
    return $custom_mail;
}

/**
 * https://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php
 * 
 * @return string $ip
 */
function humsayaa_get_ip() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
}

/**
 * declare list of bad words/phrases comma seperated.
 *
 * @var array / string
 */
function humsayaa_badwords() {
	$badwords = array(
		'bad word 1', 'bad word 2', 'another bad word',
	);
	$badwords = array_map('strtolower', $badwords);

	return $badwords;
}

/**
 * declare list of bad ips comma seperated.
 *
 * @var array / string
 */
function humsayaa_bad_ips() {
	$bad_ips = array (
		'1.169.105.3', '2.94.5.4',
	);

	return $bad_ips;
}