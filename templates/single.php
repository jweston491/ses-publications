



<?php
  		 	 
   	$authors_meta = get_post_meta(get_the_ID(), '_authors', TRUE);
	$sesauthorsdd_meta = get_post_meta(get_the_ID(), '_sesauthorsdd', TRUE); 
    $issuepages_meta = get_post_meta(get_the_ID(), '_issue_pages', TRUE);
    $redirectURL_meta = get_post_meta(get_the_ID(), '_redirect_to', TRUE);
	$short_title_meta = get_post_meta(get_the_ID(), '_short_title', TRUE);
  //  $ses_author_list =  wp_get_object_terms( get_the_ID(), 'sesauthors' );	
    $ses_journals_list =  wp_get_object_terms( get_the_ID(), 'journals' );	
    $ses_yearspublished_list =  wp_get_object_terms( get_the_ID(), 'yearspublished' );	
    $allrelated_sesauthors = get_post_meta(get_the_ID(),'related_sesauthors',true);	
			
            
//          echo '<p>'. $authors_meta . ' and ';
//		  echo '<p>'. $sesauthorsdd_meta . '. || ';
		  echo '<p>';
		  
		 
			
			if (!empty($allrelated_sesauthors)) {
			   if ( ! is_wp_error( $allrelated_sesauthors ) ) {
				
				$allrelated_sesauthors_array = json_decode($allrelated_sesauthors);
				$numItems = count($allrelated_sesauthors_array);
				$i = 0;
				 foreach($allrelated_sesauthors_array as $allrelated_sesauthor){
				  $sesauthorterm =  get_term($allrelated_sesauthor, 'nonsesauthors');
				  $sesauthorparent = get_term($sesauthorterm->parent, 'nonsesauthors');
    			   if ($sesauthorparent->name == 'SES') {
	//			   if (substr($sesauthorterm->name,-4) == '-SES') {
        //			echo 'parent '.   $sesauthorparent->name;
			
//					echo '<b>' . substr($sesauthorterm->name,0,-4) . '</b>';
					echo '<b>' . $sesauthorterm->name . '</b>';
				   }
				   else {
					   echo $sesauthorterm->name;
				   }
				   
					// echo '<a href="?the_nonsesauthor=' . $sesauthorterm->slug . '">' . $sesauthorterm->name . '</a>'; 
					if (++$i != $numItems) {echo ', ';}
				  } // foreach $allrelated_sesauthors_array
				 } // end not wp_error
			    } //end of !empty
				else {
				 echo $sesauthorsdd_meta . ' ++ ';
				}
		  
			  
//			   if (! empty( $ses_author_list)){			
//			  if ( ! is_wp_error( $ses_author_list ) ) {
//				   $numItems = count($ses_author_list);
//				   $i = 0;
 //                  foreach ( $ses_author_list as $ses_author) {
//					   echo '<b>' . $ses_author->name . '</b>'; 
//					    if (++$i != $numItems) {echo ', ';}
//				   } // end foreach
//			  } // end not wp_error
//			} // end if not empty
			  
			  echo  '. ';
			  
			   if (! empty( $ses_yearspublished_list)){
			  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                   foreach ( $ses_yearspublished_list as $ses_yearpublished) {
					   echo '' . $ses_yearpublished->name . ''; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty 
			   
			   
			  echo '. ' . get_the_title() . ' <i>';
			  
			   if (! empty( $ses_journals_list)){
			  if ( ! is_wp_error( $ses_journals_list ) ) {
                   foreach ( $ses_journals_list as $ses_journal) {
					   echo '' . $ses_journal->name . ''; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			   
			   echo '</i>. '. $issuepages_meta . '.</p><p>'; 
			   
			   if ($redirectURL_meta != '') 
			   {
			   
			   echo '<b> URL: </b>' . '<a href="' . $redirectURL_meta . '">'. $redirectURL_meta . '</a>'.  '</p>';
			   }
		 
		   // return $pub_fields_string;
	           echo '<HR><p></p><a href="' . get_site_url() .'/publication/">Return to Publications</a>';
			//    echo '<HR><p></p><a href="#" onclick="window.history.go(-1); return false;">Return to Publications</a>';
	//		   echo '<p>SES Faculty Involved: ';
			   
    // 	     if (! empty( $ses_author_list)){			
	//		  if ( ! is_wp_error( $ses_author_list ) ) {
	//			   $numItems = count($ses_author_list);
	//			   $i = 0;
     //              foreach ( $ses_author_list as $ses_author) {
	//				   echo '<b>' . $ses_author->name . '</b>'; 
	//				    if (++$i != $numItems) {echo ', ';}
	//			   } // end foreach
	//		  } // end not wp_error
	//		} // end if not empty
			   
	//	   echo '</p>';
			   