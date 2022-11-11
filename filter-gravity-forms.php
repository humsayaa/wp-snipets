<?php

// https://docs.gravityforms.com/gform_validation/
add_filter( 'gform_validation', 'custom_validation' );
function custom_validation( $validation_result ) {
    $form = $validation_result['form'];

    /**
     * check for spam in rgpost( 'input_4' ) // input--message
     */
    //$string   = strtolower($posted_message);
    $string   = mb_strtolower(rgpost( 'input_4' ), 'UTF-8'); // multi language
    $badwords = humsayaa_badwords();
    // https://gist.github.com/anthonybudd/9c32cc1698cb8b1d6144944a402e3c1c
    if(in_array(true,array_map(function($e)use($string){return strpos($string,$e)!==false;},$badwords))) {
        // set the form validation to false
        $error_failed_validation = true;
    }

    /**
     * check for spamy IPs
     */
    $list_ip = humsayaa_get_ip();
    $bad_ips = humsayaa_bad_ips();
    // https://gist.github.com/anthonybudd/9c32cc1698cb8b1d6144944a402e3c1c
    if(in_array(true,array_map(function($e)use($list_ip){return strpos($list_ip,$e)!==false;},$bad_ips))) {
        // set the form validation to false
        $error_failed_validation = true;
    }

    if ( $error_failed_validation == true ) {
        // set the form validation to false
        $validation_result['is_valid'] = false;

        //finding Field with ID of 4 and marking it as failed validation
        foreach( $form['fields'] as &$field ) {

            //NOTE: replace 4 with the field you would like to validate
            if ( $field->id == '4' ) {
                $field->failed_validation = true;
                $field->validation_message = 'There was an error sending your message, please try again later.';
                break;
            }
        }
    }
    
    //Assign modified $form object back to the validation result
    $validation_result['form'] = $form;
    return $validation_result;
  
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