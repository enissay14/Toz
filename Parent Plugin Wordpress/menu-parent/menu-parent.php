<?php
/**
Plugin Name: Menu Parent
Plugin URI: http://to-z.com
Description: Menu des parents pour gérer les infos de leurs enfants et de rester à jours avec les communications
Version: 0.1
Author: Yassine
Author URI: yassine@to-z.com
License: Tout droit réservé à TO-Z Limited
*/



add_action( 'plugins_loaded', 'slug_extend_safe_activate');

function slug_extend_safe_activate() {
	if ( defined( 'PODS_VERSION' ) ) {
		include_once( 'edit_info.php');
	}
	
}



