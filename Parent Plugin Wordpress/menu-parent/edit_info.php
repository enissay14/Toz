<?php

add_filter('pods_api_pre_save_pod_item', 'my_pre_save_function', 10, 2); 
add_action( 'wp_enqueue_scripts', 'menu_parent_add_my_stylesheet' );

/**
*@description body of parent page
*@author TOZ limited yassine
*
***/
function menu_parent_view(){
	
	//get current loged user
	global $current_user;
	
	get_currentuserinfo();
	
	if ('' == $current_user->ID) {
		return 'nobody is logged in';
	}
	else {
		$user = pods(‘user’, $current_user->ID);		//load parent (user pod)
		
		$out = '<div class="container-fluid">';
		$out .= '<div class="row">';
		
		$out .= '<div id="section" class="col-md-12">';
			$out .= '<hgroup class="page-title">';
			$out .= '<h2> Hello ' . $user->field( 'name' ) . ' !</h2>';
			$out .= "<h5>All your children's bulletins and communications </h5>	</hgroup>";
		
			$out .= do_shortcode(menu_parent_block_students($user));
			
		$out .= '</div>';
		
		
		$out .= '<div class="row">';
			$out .= '<div id="section" class="col-md-12">';
			
				// parent information
				$out .= '<br/>';
				$out .= '<hgroup class="page-title">';
				$out .= '<h2> Your Information </h2>';
				
				
				$out .= '<ul>';
				$out .= '<li><div class="left"> Current Adress : </div> '
					.'<div class="right">'
					.'  ' . $user->field( 'current_adress_line_1' ) .'
					. ' . $user->field( 'current_adress_line_2' ) .'
					. ' . $user->field( 'current_adress_line_3' ) . ''
					.'</li>';
				if ($user->field( 'second_adress_line_1' )) {
					$out .= '<li><div class="left"> Second Adress : </div> '
						.'<div class="right">'
						.' <p> ' . $user->field( 'second_adress_line_1' ) . '
						. ' . $user->field( 'second_adress_line_2' ) . '
						. ' . $user->field( 'second_adress_line_3' ) . '</p>'
						.'</li>';
				}
				
				$out .= '<li><div class="left"> Current telephone number : </div> '
						.'<div class="right"> ' . $user->field( 'contact_1' ) . '</div>'
						.'</li>';
				
				if ($user->field( 'contact_2' )) {
					$out .= '<li><div class="left"> Second telephone number :  </div> '
						.'<div class="right"> ' . $user->field( 'contact_2' ) . '</div>'
						.'</li>';
				}
				$out .= '</ul>';
				
				$out .= '<p>TO UPDATE YOUR CONTACT INFORMATION OR T ADD A NEW ADRESS OR TELEPHONE NUMBER PLEASE USE TE FORM BELOW</p>';
				$out .= '</hgroup>';
				
				
// 				$out .= '<br/>';
// 				$fields = array('adress_1','adress_2','country','contact_1','contact_2'); //fields to edit
// 				$out .= $user->form($fields);	
			$out .= '</div>';
			
			$out .= '<div id="section" class="col-md-12">';
				//contact form
				$out .= '<hgroup class="page-title">';
				$out .= '<h2> Contact Form </h2>';
				$out .= '<h5>ANY THOUGHTS OR SUGGESTIONS</h5>	</hgroup>';
				$out .= do_shortcode('[contact-form-7 id="200" title="Contact form 1"]');
			$out .= '</div>';
		$out .= '</div>';
		
		$out .= '</div>';
		$out .= '</div>';
		
		return $out ;
	}
	
}


/**
*@description block displayng all students in tabs 
*@author TOZ limited yassine
*@arg 1 arg user
*
***/
function menu_parent_block_students($user){
	
	//calculate the timestamp of this year starting date 01-09-[current year -1]
	$Y = (int) getdate(strtotime ("-1 years"))['year'];
	$date = new DateTime();
	$date->setDate($Y, 9, 1);
	$thisyear =  $date->getTimestamp();


	//Using shortcode [tabs tab1="tab1_header" tab2="tab2_header" ...] to display headers of tabs
	$count = 1;
	
	$out .= '<div class="row">';
	$out .= '<div class="col-md-12">';
	
		
	$out = '[tabs ';
	foreach ( $user->field('students') as $std ) {
		$student = pods('student', $std['ID']);
		$out .= 'tab'.$count.'="'.$student->display('full_name').'" '; 
		$count ++ ;
	}
	$out .=' ]';
	
	//loop on all students of parent
	$count = 1 ;
	foreach ( $user->field('students') as $std ) {
		
		//Using shortcode [tab tab_number="1"] content-in-tab-1 [/tab] to display content of the tab
		$out  .= '[tab tab_number="'. $count .'"]';
		
		$out .= '<div class="row">';
			$out .= '<div id="student-info" class="col-md-12" >';
				//load student pod & display his/her info
				$student = pods('student', $std['ID']);
				$out  .= '<span><p>Student : <h4>'.$student->display('full_name'). '</h4></p></span>';
				$out  .= '<span><p>Teacher : <h4>'.$student->display('class'). '</h4> </p></span>';
				$out  .= '<span><p class="student-info">Class : <h4>'.$student->display('level'). '</h4></p></span>';
			$out .= '</div>';
		
			/*$out .= '<div class="col-md-6" hidden>';
				//edit student fullname
				$fields = array('full_name');				//fields to edit
				$out  .= '<h4> Edit Student name </h4>';
				$out  .= $student->form($fields);			//call edit form
			$out .= '</div>'*/;
		$out .= '</div>';
		$out .= '<hr class="seperator">';
		$out .= '<div class="row">';
			$out .= '<div class="col-md-6">';
				
				//block communication
				$out .= '<div id="block-comm">';
				$out  .= menu_parent_student_communications($student->display('class'),$student->display('level'),$thisyear);
				$out .= '</div>';
				
				//block bulletins
				$out .= '<div id="block-bull">';
				$out  .= menu_parent_student_bulletins($student->display('post_title'),$student->display('full_name'),$thisyear);
				$out .= '</div>';
					
			$out .= '</div>';
		
			$out .= '<div class="col-md-6">';
				
				//block activities
				$out .= '<div id="block-activ">';
				$out .= menu_parent_activities();
				$out .= '</div>';
				
			$out .= '</div>';
		$out .= '</div>';
		
		//closing sortcode [tab]
		$out  .= '[/tab]';
		
		$count ++ ;
		
	} 
	
	
	$out  .= '[/tabs]';
	
	
	$out .= '</div>';
	$out .= '</div>';
	return $out;
}

/**
*@description communications block in  student block
*@author TOZ limited yassine
*@arg 2 arg: student class & student level
*
***/
function menu_parent_student_communications($student_class,$student_level,$thisyear) {
	//how many communications to show
	$limit = 3 ;
	
	//load all communications
	$params = array(
		'orderby' => 't.post_date DESC',  
		); 
	$communications = pods('communication', $params);
	$comm_class = '';
	$comm_level = '';
	
	$out = '<h4>Communications: </h4>';
	
	$out .= '<div class="table-responsive">';
	$out .= '<table class="table table-condensed table-hover">';
	$out .= '<thead>';
	$out .= '<tr>';
	$out .= '<td>Event</td>';
	$out .= '<td>Date</td>';
	$out .= '<td>Need Response</td>';
	$out .= '</tr>';
	$out .= '</thead>';
	
	$out .= '<tbody>';
	$count = 1;
	while ( $communications->fetch()  ) {
		
		if($count <= $limit && strtotime($communications->field('post_date')) > $thisyear){
			$comm_class = $communications->display( 'class' ) ;
			$comm_level = $communications->display( 'level' ) ;
			
			//communication for all 
			if( $comm_class == '' && $comm_level == '' ){
				$out .= '<tr>';
				$out .= '<td><a href="'. $communications->display( 'communication' ). '" target="_blank">' . $communications->display( 'post_title' ) . ' (All)</a></td>';
				$out .= '<td>'. explode(" ", $communications->display( 'post_date' ) , 2)[0]. '</td>';
				if( $communications->display( 'need_response' ) ) {
					$out .= '<td class="ui-helper-center"><img src="'.plugins_url('/images/check_box.png', __FILE__) .'"></img></td>';
				}else{
					$out .= '<td></td>';
				}
				$out .= '</tr>';
				$count++;
			}
			
			//communication for class
			if( $comm_class === $student_class ){ 
				$out .= '<tr>';
				$out .= '<td><a href="'. $communications->display( 'communication' ). '" target="_blank">' . $communications->display( 'post_title' ) . ' ('.$comm_class.')</a></td>';	
				$out .= '<td>'. explode(" ", $communications->display( 'post_date' ) , 2)[0]. '</td>';
				if( $communications->display( 'need_response' ) ) {
					$out .= '<td class="ui-helper-center"><img src="'.plugins_url('/images/check_box.png', __FILE__) .'"></img></td>';
				}else{
					$out .= '<td></td>';
				}
				$out .= '</tr>';
				$count++;
			}
			
			//communication for level
			if( $comm_level === $student_level){
				$out .= '<tr>';
				$out .= '<td><a href="'. $communications->display( 'communication' ). '" target="_blank">' . $communications->display( 'post_title' ) . ' ('.$comm_level.')</a></td>';
				$out .= '<td>'. explode(" ", $communications->display( 'post_date' ) , 2)[0]. '</td>';
				if( $communications->display( 'need_response' ) ) {
					$out .= '<td class="ui-helper-center"><img src="'.plugins_url('/images/check_box.png', __FILE__) .'"></img></td>';
				}else{
					$out .= '<td></td>';
				}
				$out .= '</tr>';
				$count++;
			}
		}
	} 
	$out .= '</tbody>';
	$out .= '</table>';
	$out .= '</div>';
	
	$out .= '<p><a href="#allCommunicationModal'.$student_class.''.$student_level.'">show all...</a></p>';
	
	//bulletin modal
	$out .= '<div id="allCommunicationModal'.$student_class.''.$student_level.'" class="modalDialog">';
	$out .= '<div>';
		$out .= '<a href="#close" title="Close" class="close">X</a>';
			//define parameters for communications search
			//look for all communications
			$params = array(
				'orderby' => 't.post_date DESC',
				); 
			$communications = pods('communication', $params);
			
			$out .= '<h4>Communications: </h4>';
			
			$out .= '<div class="table-responsive">';
			$out .= '<table class="table table-condensed table-hover">';
			$out .= '<thead>';
			$out .= '<tr>';
			$out .= '<td>Event</td>';
			$out .= '<td>Date</td>';
			$out .= '<td>Need Response</td>';
			$out .= '</tr>';
			$out .= '</thead>';
			
			$out .= '<tbody>';
			
			while ( $communications->fetch()) {
			
				if(strtotime($communications->field('post_date')) > $thisyear){
					$comm_class = $communications->display( 'class' ) ;
					$comm_level = $communications->display( 'level' ) ;
					
					//communication for all 
					if( $comm_class == '' && $comm_level == '' ){
						$out .= '<tr>';
						$out .= '<td><a href="'. $communications->display( 'communication' ). '" target="_blank">' . $communications->display( 'post_title' ) . ' (All)</a></td>';
						$out .= '<td>'. explode(" ", $communications->display( 'post_date' ) , 2)[0]. '</td>';
						if( $communications->display( 'need_response' ) ) {
							$out .= '<td class="ui-helper-center"><img src="'.plugins_url('/images/check_box.png', __FILE__) .'"></img></td>';
						}else{
							$out .= '<td></td>';
						}
						$out .= '</tr>';
					}
					
					//communication for class
					if( $comm_class === $student_class ){ 
						$out .= '<tr>';
						$out .= '<td><a href="'. $communications->display( 'communication' ). '" target="_blank">' . $communications->display( 'post_title' ) . ' ('.$comm_class.')</a></td>';	
						$out .= '<td>'. explode(" ", $communications->display( 'post_date' ) , 2)[0]. '</td>';
						if( $communications->display( 'need_response' ) ) {
							$out .= '<td class="ui-helper-center"><img src="'.plugins_url('/images/check_box.png', __FILE__) .'"></img></td>';
						}else{
							$out .= '<td></td>';
						}
						$out .= '</tr>';
					}
					
					//communication for level
					if( $comm_level === $student_level){
						$out .= '<tr>';
						$out .= '<td><a href="'. $communications->display( 'communication' ). '" target="_blank">' . $communications->display( 'post_title' ) . ' ('.$comm_level.')</a></td>';
						$out .= '<td>'. explode(" ", $communications->display( 'post_date' ) , 2)[0]. '</td>';
						if( $communications->display( 'need_response' ) ) {
							$out .= '<td class="ui-helper-center"><img src="'.plugins_url('/images/check_box.png', __FILE__) .'"></img></td>';
						}else{
							$out .= '<td></td>';
						}
						$out .= '</tr>';
					}
				}
			} 
			$out .= '</tbody>';
			$out .= '</table>';
			$out .= '</div>';
		$out .= '</div>';
	$out .= '</div>';
	
	
	return $out;
}
/**
*@description bulletins block in  student block
*@author TOZ limited yassine
*@arg 3 arg student id & student name & the current year
*
***/
function menu_parent_student_bulletins($student_id,$student_name,$thisyear) {
	
	//how many bulletins to show
	$limit = 3 ;
	
	//define parameters for bulletins search
	//last 3 bulletins
	$params = array(
		'where' => 'student.post_title = ' . (int) $student_id  . '',
	);

	//search in communications pod
	$bulletins = pods('bulletin', $params); 
	
	
		$out = '<h4> '.$student_name.'\'s Communications & Reports: </h4>';
		
		$out .= '<div class="table-responsive">';
		$out .= '<table class="table table-condensed table-hover">';
		$out .= '<thead>';
		$out .= '<tr>';
		$out .= '<td>Event</td>';
		$out .= '<td>Date</td>';
		$out .= '<td>Download</td>';
		$out .= '</tr>';
		$out .= '</thead>';
		
		$out .= '<tbody>';
			
		//loop through communications results
		$count = 1;
		if ( 0 < $bulletins->total() ) {
			while ( $bulletins->fetch() ) {
				if($count <= $limit && strtotime($bulletins->field('post_date')) > $thisyear){
					$out .= '<tr>';
					$out .= '<td>'.$bulletins->display( 'post_title' ) . '</td>';
					$out .= '<td>'. explode(" ", $bulletins->display( 'post_date' ) , 2)[0]. '</td>';
					$out .= '<td><a href="'. $bulletins->display( 'bulletin' ). '" target="_blank">link</a></td>';
					$out .= '</tr>';
					$count++;
				}
			}
			$out .= '</tbody>';
			$out .= '</table>';
			$out .= '</div>';
		} 

		$out .= '<p><a href="#allBulletinModal'.$student_id.'">show all...</a></p>';
 		
		//bulletin modal
		$out .= '<div id="allBulletinModal'.$student_id.'" class="modalDialog">';
		$out .= '<div>';
			$out .= '<a href="#close" title="Close" class="close">X</a>';
				//define parameters for bulletins search
				//look for all bulletins
				$params = array(
					'where' => 'student.post_title = ' . (int) $student_id  . '',
				);

				//search in communications pod
				$bulletins = pods('bulletin', $params); 
				
				
				$out .= '<h4> Bulletins: </h4>';
				
				$out .= '<div class="table-responsive">';
				$out .= '<table class="table table-condensed table-hover">';
				$out .= '<thead>';
				$out .= '<tr>';
				$out .= '<td>Event</td>';
				$out .= '<td>Date</td>';
				$out .= '<td>Download</td>';
				$out .= '</tr>';
				$out .= '</thead>';
				
				$out .= '<tbody>';
				
				//loop through communications results
				if ( 0 < $bulletins->total() ) {
					while ( $bulletins->fetch()) {
						if(strtotime($bulletins->field('post_date')) > $thisyear){
							$out .= '<tr>';
							$out .= '<td>'.$bulletins->display( 'post_title' ) . '</td>';
							$out .= '<td>'. explode(" ", $bulletins->display( 'post_date' ) , 2)[0]. '</td>';
							$out .= '<td><a href="'. $bulletins->display( 'bulletin' ). '" target="_blank">link</a></td>';
							$out .= '</tr>';
						}
					}
					$out .= '</tbody>';
					$out .= '</table>';
					$out .= '</div>';
				} 
			$out .= '</div>';
		$out .= '</div>';

	return $out;
}

/**
*@description activities block in  student block
*@author TOZ limited yassine
*
***/
function menu_parent_activities() {
	
	$cat_id = 2; //ID of activities category 
	$out ='<h4>This week\'s activity </h4>';
	
	$args = array( 'category' => 2,
			'numberposts' => 1,
			);
	
	$recent_posts = wp_get_recent_posts( $args );
	
	foreach( $recent_posts as $recent ){
// 		var_dump($recent);
		$out .= '<div class="post">';
		$out .= '<h2>' .  $recent["post_title"] .'</h2>';
		$out .= '<h5>' .  explode(" ", $recent['post_modified'] , 2)[0].' </h5>';
		$out .= '<div>' .  truncate($recent['post_content'],800,'...</div>') .' </div>';
		$out .= '<a href="' . get_permalink($recent["ID"]) . '"> Read more... </a>';
		$out .= '</div>';
	}

	return $out;
	
}

/**
*@description listen to form submission and notify admin of changed fields
*@author TOZ limited yassine
*@arg 2 original to Pods Plugin (catched after form submission) 
*
***/
function my_pre_save_function($pieces, $is_new_item) { 		

	add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	
	//Check to see if request was submitted from a specific front-end WordPress page
	if(  substr( $_POST[_podsfix__pods_location], 0, 7 ) === "/parent") {
	
		global $current_user;
		get_currentuserinfo();
		$user = pods(‘user’, $current_user->ID);
		
		//Fields we want to check against
		$fields = array(
			'full_name',
			'contact_1',
			'contact_2',
			'adress_1',
			'adress_2'
		);
		
		$member_pod = pods( 'members', $pieces['params']->id );
		
		$changed_fields = array();
		
		$i = 0;
		
		//Loop through fields and check if submitted data is different from the DB
		//Save Old/New Value pairs in $changed_fields if different
		
		foreach($fields as $single_field) {
			//Check if submitted data is different from current data
			if($member_pod->field($single_field) != $_POST['_podsfix_pods_field_'.$single_field]) {
				$changed_fields[$i]['New Value'] = $_POST['_podsfix_pods_field_'.$single_field];
				
				$i++;
			}
		}
		
		//Build HTML String for Email
		$html_string = "<p> L'utilisateur ". $user->field( 'name' ) . " a modifié le champs : </p>";
		
		//Loop through $changed_fields and add to Email Message String
		foreach($changed_fields as $changed_field) {
			$html_string .= '<ul><li>New Value: '.$changed_field['New Value'].'</li></ul>';			
		}
		error_log( $html_string);
		//Send Email
 		$admin_email = get_option('admin_email');
 		error_log($admin_email);
 		wp_mail( $admin_email, 'Member Updated', $html_string );
	}
	remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
}

/**
*@description define content type for email
*@author TOZ limited yassine
*
***/
function set_html_content_type() {
	return 'text/html';
}

add_action('admin_menu', 'remove_pods_menu', 11);

/**
*@description show pods menu just to admin user
*@author TOZ limited yassine
*
***/
function remove_pods_menu ()  {
	get_currentuserinfo() ;
	global $user_level;
	if ($user_level < 10){
		define('PODS_DISABLE_ADMIN_MENU', true);
		define('PODS_DISABLE_CONTENT_MENU', true);
	}
}

/**
 * Enqueue plugin style-file
 */
function menu_parent_add_my_stylesheet() {
    wp_register_style( 'prefix-style', plugins_url('/css/edit-info.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}

/**
*@description truncate string and add '...' at the end
*@author TOZ limited yassine
*@arg 3 string to truncate & number of characters to keep & string to append at the end of te truncated string 
*
***/
function truncate($string, $width, $etc = ' ..')
{
    $wrapped = explode('$trun$', wordwrap($string, $width, '$trun$', false), 2);
    return $wrapped[0] . (isset($wrapped[1]) ? $etc : '');
}