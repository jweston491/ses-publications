<?php
/**
* Plugin Name: SES Publications
* Plugin URI:  https://github.com/jweston491/ses-publications
* Description: Adds SES Publications Post Type
* Version:     0.1.5
* Author:      CAHNRS Communications, Don Pierce
* Author URI:  http://cahnrs.wsu.edu/communications/
* License:     Copyright Washington State University
* License URI: http://copyright.wsu.edu
*/

class CAHNRSWP_SESPUB_Init {
	
	private static $instance = null;
	
	public static function get_instance(){
		
		if( null == self::$instance ) {
			
			self::$instance = new self;
			
		} 
		
		return self::$instance;
		
	} // end get_instance
	
	private function __construct(){
		
		define( 'CAHNRSWPRFPURL' , plugin_dir_url( __FILE__ ) ); // PLUGIN BASE URL
		
		define( 'CAHNRSWPRFPDIR' , plugin_dir_path( __FILE__ ) ); // DIRECTORY PATH
		
		add_action( 'init', array( $this , 'add_custom_post_type' ) );
		
		add_action( 'init', array( $this ,'add_custom_taxonomies' ) );

		// add_action('single_template', array($this, 'cahnrs_pub_template'));
		add_action('the_content', array($this, 'cahnrs_pub_template'));
		
        add_shortcode( 'sespubslist', array($this, 'cahnrswp_display_ses_publications' ));
		
		
//	add_filter('posts_clauses', array( $this , 'cahnrswp_sespubs_clauses_with_tax', 10 ,2 ) );

//		add_action('init', array( $this , 'cahnrswp_register_display_ses_pubs') );
		
		add_action( 'edit_form_after_title', array( $this , 'cahnrswp_edit_form_after_title' ) );
		
		add_action( 'init', array( $this, 'cahnrswp_init' ), 1 );
		
		add_action( 'save_post', array( $this , 'cahnrswp_save_post' ) );
		
		
//		add_action( 'template_redirect', array( $this , 'cahnrswp_template_redirect' ) );
		     
//	    add_filter('the_title', array( $this , 'cahnrswp_short_title' ) );
			
		add_filter('the_permalink', array( $this , 'cahnrswp_the_permalink' ) );
		
//  	add_action( 'the_content', array( $this, 'cahnrswp_display_wsuwp_people' ) );		

//		add_filter('the_content', array( $this , 'cahnrswp_pub_fields' ) );
		
//		add_filter( 'page_attributes_dropdown_pages_args', 'sespub_attributes_dropdown_pages_args' );
		
		
		
	} // end constructor
	
		
	public function cahnrswp_init(){
 
        $clauses = '';	
        $wp_query = null;						
		
	} // end cahnrswp_init


	public function cahnrs_pub_template( $content ) {

		global $post;

		if ( 'publication' === $post->post_type ) {
			ob_start();
			include dirname( __FILE__ ) . '/templates/single.php';
			$single_template = ob_get_clean();
			return $single_template;
		}
		return $content;
	}
	
   // Request all profiles with tag school-of-economic-sciences 	

   public function cahnrswp_people_request($people_tag){
   
   $response = wp_remote_get( 'https://people.wsu.edu/wp-json/posts/?type=wsuwp_people_profile&tag=' . $people_tag ,array('sslverify'=> false));
     try {
      // Note that we decode the body's response since it's the actual JSON feed
      $json = json_decode($response['body']);
      
      } catch ( Exception $ex ) {
    	$json = null;   
     } //end try/catch
	return $json;
							
  } // end cahnrswp_people_request



   // display cahnrswp_display_wsuwp_people
 public function cahnrswp_display_wsuwp_people($content){
	  // If we're on a single post or page...
        if ( is_single() ) {
            // ...attempt to make a response to wsuwp_people. Note that you should replace the tag here!
			
	            if ( null == ( $json_response = $this->cahnrswp_people_request('school-of-economic-sciences') ) ) {
				
				// ...display a message that the request failed
                               $html = '<div id="cahnrswp-wsuwp-people">';
 $html .= 'There was a problem communicating with the People Profiles..';
 $html .= '</div>				<!-- /#cahnrswp-wsuwp-people -->';
 
			} else {
			
               $html = '
<div id="cahnrswp-wsuwp-people">';
foreach($json_response as $item) {
 $html .= 'Faculty Name ' . $this->$item->title  . ' link to profile';
}
 $html .= '</div>
<!-- /#cahnrswp-wsuwp-people -->';				
			} //end else of if/else
			
			 $content .= $html;

		}//end if/else
   } //end cahnrswp_display_wsuwp_people
	
 
 public function cahnrswp_pub_fields( $content ){
 
 $pub_fields_string ='';		 				
 global $post;
		 
  if( 'publication' == $post->post_type ) {
  		 	 
   	$authors_meta = get_post_meta(get_the_ID(), '_authors', TRUE);
	$sesauthorsdd_meta = get_post_meta(get_the_ID(), '_sesauthorsdd', TRUE);
    $issuepages_meta = get_post_meta(get_the_ID(), '_issue_pages', TRUE);
    $redirectURL_meta = get_post_meta(get_the_ID(), '_redirect_to', TRUE);
    $ses_author_list =  wp_get_object_terms( get_the_ID(), 'sesauthors' );	
    $nonses_author_list =  wp_get_object_terms( get_the_ID(), 'nonsesauthors' );
	
    $ses_journals_list =  wp_get_object_terms( get_the_ID(), 'journals' );	
    $ses_yearspublished_list =  wp_get_object_terms( get_the_ID(), 'yearspublished' );	
			
            
    $pub_fields_string .= '<br>'.$issuepages_meta.'</br>';		 							 
    $pub_fields_string .='<br>'.$redirectURL_meta.'</br>';
			 
             if (! empty( $ses_author_list)){			
			  if ( ! is_wp_error( $ses_author_list ) ) {
                   foreach ( $ses_author_list as $ses_author) {
					   $pub_fields_string .= '<br>WSU Author(s): <b>' . $ses_author->name . '</b></br>'; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty			 
 
		  
		   $pub_fields_string .= '<br>Non-WSU Author(s) <b>'.$authors_meta.'</b></br>';
		  
		   if (! empty( $ses_journals_list)){
			  if ( ! is_wp_error( $ses_journals_list ) ) {
                   foreach ( $ses_journals_list as $ses_journal) {
					  $pub_fields_string .= '<br>Jounnal Name: <b>' . $ses_journal->name . '</b></br>'; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
		  
 
			 
		if (! empty( $ses_yearspublished_list)){
			  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                   foreach ( $ses_yearspublished_list as $ses_yearpublished) {
					   $pub_fields_string .= '<p>Years Published: <b>(' . $ses_yearpublished->name . ')</b></p> '; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty	 

		 
		    return $pub_fields_string;
		
		 } // if publication 
		 
  } // end cahnrswp_pub_fields	
	
	
	
public function sespub_attributes_dropdown_pages_args( $dropdown_args, $post ) {
	
        if ( 'publication' == $post->post_type ) {
                $dropdown_args['post_type'] = 'wsuwp_personnel_directory';
        }
        return $dropdown_args;}
	
	public function cahnrswp_edit_form_after_title(){ 
		
		global $post;
		
		$sespub_model = new CAHNRSWP_SESPUB_model(); 
		
		$sespub_model->set_sespub( $post->ID );
		
		$page_view = new CAHNRSWP_SESPUB_view( $this , $sespub_model );
		
		$page_view->output_editor();
		
	} // end add_editor_form
	
	public function add_custom_post_type(){
		
		$labels = array(
			'name'               => _x( 'SES Publications', 'post type general name', 'ses-Pubs' ),
			'singular_name'      => _x( 'SES Publication', 'post type singular name', 'ses-Pubs' ),
			'menu_name'          => _x( 'SES Publications', 'admin menu', 'ses-Pubs' ),
			'name_admin_bar'     => _x( 'SES Publication', 'add new on admin bar', 'ses-Pubs' ),
			'add_new'            => _x( 'Add New', 'Publication', 'ses-Pubs' ),
			'add_new_item'       => __( 'Add New SES Publication', 'ses-Pubs' ),
			'new_item'           => __( 'New SES Publication', 'ses-Pubs' ),
			'edit_item'          => __( 'Edit SES Publication', 'ses-Pubs' ),
			'view_item'          => __( 'View SES Publication', 'ses-Pubs' ),
			'all_items'          => __( 'All SES Publications', 'ses-Pubs' ),
			'search_items'       => __( 'Search SES Publications', 'ses-Pubs' ),
			'parent_item_colon'  => __( 'Parent SES Publications:', 'ses-Pubs' ),
			'not_found'          => __( 'No SES Publications found.', 'ses-Pubs' ),
			'not_found_in_trash' => __( 'No SES Publications found in Trash.', 'ses-Pubs' )
		); // end $labels
	
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'publication' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
//			'taxonomies' => array( 'sesauthor', 'journal', 'yearspublished', 'category' ),
			'taxonomies' => array( 'sesauthor','journal', 'yearspublished'),
//			'supports'           => array( 'title', 'thumbnail', 'excerpt' , 'editor')
			'supports'           => array( 'title', 'thumbnail', 'excerpt')			
		); // end $args
	
		register_post_type( 'publication', $args );
		
	} // end add_custom_post_type
	
	public function cahnrswp_save_post( $post_id ){
		
		$sespub_model = new CAHNRSWP_SESPUB_model(); 
		
		$sespub_model->save_sespub( $post_id );
		
	} // end cahnrswp_save_post
	
	public function cahnrswp_template_redirect(){
		
		 global $post;
		 
		 if( 'publication' == $post->post_type && is_singular() ){
			 
			 $meta = \get_post_meta( $post->ID , '_redirect_to' , true );
			 
			 if( $meta ){
				 
				 \wp_redirect( $meta , 302 );
				 
			 } // end if $meta
			 
		 } // end if post_type
		 
	 } // end cahnrswp_template_redirect
	 
	 public function cahnrswp_short_title( $title ){
		
		 global $post;
		 
	if(( 'publication' == $post->post_type ) AND in_the_loop()){
 
		     $meta = get_post_meta( $post->ID , '_short_title' , true );
	
			 if (( $meta != '')) {  
			    
				  $title = $meta;
				  
			 } 
			 else {
				$title = $post->post_title;
			 }
			 
			 // end if $meta
	
		 } // if post_type
		 
			 return $title; 
		 
	} // end cahnrswp_short_title
	
  public function add_custom_taxonomies() {
  // Add new "Journal Name" taxonomy to custom post 
  
  
  //Register Author taxomony

/*
 register_taxonomy('sesauthors', 'publication', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
	'archive_layout' => 'full',
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'SES Authors', 'taxonomy general name' ),
      'singular_name' => _x( 'SES Author', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search SES Authors' ),
      'all_items' => __( 'All SES Authors' ),
      'parent_item' => __( 'Parent SES Author' ),
      'parent_item_colon' => __( 'Parent SES Author:' ),
      'edit_item' => __( 'Edit SES Author' ),
      'update_item' => __( 'Update SES Author' ),
      'add_new_item' => __( 'Add New SES Author' ),
      'new_item_name' => __( 'New SES Author Name' ),
      'menu_name' => __( 'SES Authors' ),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'sesauthors', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/authors/"
      'hierarchical' => true // This will allow URL's like "/authors/authorname/subtopic/"
    ),
  ));  
 */ 
  
   register_taxonomy('nonsesauthors', 'publication', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
	'archive_layout' => 'full',
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'Related SES Authors', 'taxonomy general name' ),
      'singular_name' => _x( 'Related SES Author', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Related SES Authors' ),
      'all_items' => __( 'All Related SES Authors' ),
      'parent_item' => __( 'Parent Related SES Author' ),
      'parent_item_colon' => __( 'Parent Related SES Author:' ),
      'edit_item' => __( 'Edit Related SES Author' ),
      'update_item' => __( 'Update Related SES Author' ),
      'add_new_item' => __( 'Add New Related SES Author' ),
      'new_item_name' => __( 'New Related SES Author Name' ),
      'menu_name' => __( 'Related SES Authors' ),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'nonsesauthors', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/authors/"
      'hierarchical' => true // This will allow URL's like "/authors/authorname/subtopic/"
    ),
  ));  

  register_taxonomy('journals', 'publication', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
	'archive_layout' => 'full',
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'Journals', 'taxonomy general name' ),
      'singular_name' => _x( 'Journal', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Journals' ),
      'all_items' => __( 'All Journal' ),
      'parent_item' => __( 'Parent Journal' ),
      'parent_item_colon' => __( 'Parent Journal:' ),
      'edit_item' => __( 'Edit Journal' ),
      'update_item' => __( 'Update Journal' ),
      'add_new_item' => __( 'Add New Journal' ),
      'new_item_name' => __( 'New Journal Name' ),
      'menu_name' => __( 'Journals' ),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'journals', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/journals/"
      'hierarchical' => true // This will allow URL's like "/journals/journalname/subtopic/"
    ),
  ));  
 
 
  
  //Register Year Published taxomony
 
 register_taxonomy('yearspublished', 'publication', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
	'archive_layout' => 'full',
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'Years Published', 'taxonomy general name' ),
      'singular_name' => _x( 'Year Published', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Years Published' ),
      'all_items' => __( 'All Years Published' ),
      'parent_item' => __( 'Parent Year Published' ),
      'parent_item_colon' => __( 'Parent Year Published:' ),
      'edit_item' => __( 'Edit Year Published' ),
      'update_item' => __( 'Update Year Published' ),
      'add_new_item' => __( 'Add New Year Published' ),
      'new_item_name' => __( 'New Year Published' ),
      'menu_name' => __( 'Years Published' ),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'yearspublished', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/authors/"
      'hierarchical' => false // This will allow URL's like "/authors/authorname/subtopic/"
    ),
  )); 
  
} // end add_custome_taxonomies

// sort by custom taxonomies defined for publication content type 

public function cahnrswp_sespubs_clauses_with_tax( $clauses, $wp_query ) {
	global $wpdb;

  //array of sortable taxonomies
//   $taxonomies = array('yearspublished', 'journals','sesauthors','nonsesauthors');
     $taxonomies = array('yearspublished', 'journals','nonsesauthors');

  if (isset($wp_query->query['orderby']) && in_array($wp_query->query['orderby'], $taxonomies)) {
  $clauses['join'] .= "
     LEFT OUTER JOIN {$wpdb->term_relationships} AS rel2 ON {$wpdb->posts}.ID = rel2.object_id
     LEFT OUTER JOIN {$wpdb->term_taxonomy} AS tax2 ON rel2.term_taxonomy_id = tax2.term_taxonomy_id
     LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
  ";
  $clauses['where'] .= " AND (taxonomy = '{$wp_query->query['orderby']}' OR taxonomy IS NULL)";
  $clauses['groupby'] = "rel2.object_id";
  $clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
  $clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
}
// var_dump($clauses);
return $clauses;
}



public function cahnrswp_display_ses_publications($atts){
	
 extract(shortcode_atts(array(
      'sesdisplay' => 'top',
   ), $atts));
   

   
//$the_journal ='american-journal-of-agricultural-economics';
//$the_sesauthor ='c-r-shumway';
//$the_yearpublished = '2000';
$the_journal ='';
$the_sesauthor ='';
$the_nonsesauthor='';
$the_yearpublished = '';
$my_orderby_var = '';
//$the_order_var = 'ASC';

//$the_journal = $wp_query->query_vars['the_journal'];
//$the_sesauthor = $wp_query->query_vars['the_sesauthor'];
//$the_yearpublished = $wp_query->query_vars['the_yearpublished'];

$the_journal = $_GET['the_journal'];
$the_sesauthor = $_GET['the_sesauthor'];
$the_nonsesauthor = $_GET['the_nonsesauthor'];
$the_yearpublished = $_GET['the_yearpublished'];
$my_orderby_var = $_GET['the_orderby'];

//$the_order_var = $_GET['the_order'];
//$the_order_var = $_GET['the_order'] == 'DESC' ? 'DESC' : 'ASC';

//$the_order_var = empty($_GET['the_order'])?'ASC':(($the_order_var=='ASC')?'DESC':'');

$the_order_var = isset($_GET["the_order"]) ? $_GET["the_order"] : 'DESC';
$neworder = $the_order_var ? 'DESC' : 'ASC';

if ( is_null ($my_orderby_var) ) {
  $my_orderby_var = 'yearspublished' ;	
}



if ( is_null ($the_order_var) ) {
//  $new_order_var = 'DESC' ;	
//  $the_order_var = 'DESC' ;
}

//$the_order_var =( $my_order_var == 'DESC') ?  'ASC' : 'DESC';

//$the_order_var =( $my_order_var == 'ASC') ?  'DESC' : 'ACS';

//var_dump($the_order_var);


//$my_orderby_var = 'taxonomy.yearspublished' ;
//$my_orderby_var = 'taxonomy.sesauthors' ;
//$my_orderby_var = 'taxonomy.journal' ;
//$my_orderby_var = 'journals' ;
//$my_orderby_var = 'sesauthors' ;
//$my_orderby_var = 'yearspublished' ; 


//$term_slug = get_query_var('term');
//$tax_terms = get_terms($my_orderby_var, 
//		  array (
//		        'orderby' => 'name',
//				'order' => 'ASC',
//				'hide_empty' => 1,
//				'fields' => 'all', 
//				)
//	      );

				
//$taxonomyName = get_query_var($my_orderby_var);
//$current_term = get_term_by('slug', $term_slug, $taxonomyName );
$string ='';  
$my_query = null;
//echo $taxonomyName;
add_filter('posts_clauses', array( $this , 'cahnrswp_sespubs_clauses_with_tax' ), 10, 2 );

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$type = 'publication';
$args=array(
  'post_type' => $type,
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'caller_get_posts'=> 1,
  'orderby' => $my_orderby_var,
  'order' => $the_order_var,
  'paged' => $paged
//   'tax_query' => array (
//	   array (
//	   'taxonomy' =>'journals',
//	   'field'=> 'slug',
//       'terms'=>$the_journal,
//       ),
//	   array (
//	   'taxonomy' =>'sesauthors',
//       'field'=> 'slug',
//       'terms'=>$the_sesauthor,
//       ),
//	   array (
//	   'taxonomy' =>'yearspublished',
//       'field'=> 'slug',
//       'terms'=>$the_yearpublished,
//       ),
//	   'relation' => 'OR'
//	)  
  
  );

if ( ! is_null($the_journal) || ! is_null($the_sesauthor) || ! is_null($the_nonsesauthor) || ! is_null($the_yearpublished)) {
 
 $args['tax_query'] = array (
	   array (
	   'taxonomy' =>'journals',
	   'field'=> 'slug',
       'terms'=>$the_journal,
       ),
	   array (
	   'taxonomy' =>'sesauthors',
       'field'=> 'slug',
       'terms'=>$the_sesauthor,
       ),
	    array (
	   'taxonomy' =>'nonsesauthors',
       'field'=> 'slug',
       'terms'=>$the_nonsesauthor,
       ),
	   array (
	   'taxonomy' =>'yearspublished',
       'field'=> 'slug',
       'terms'=>$the_yearpublished,
       ),
	   'relation' => 'OR'
	);    
}

// var_dump($args); 
 
//$string ='';  
//$my_query = null;
$my_query = new WP_Query($args);
if ($sesdisplay == 'full') {
 $startstring .= '<p>';
 $endstring .= '</p>';
}
else if ($sesdisplay == 'top') {
// $startstring .= '<div id="accordion"><table><tr><td><strong>AuthorsDD</strong></td><td><strong><a href="?the_orderby=yearspublished&the_order=' . $the_order_var .'">Year</strong></td><td><strong>Title</strong></td><td><strong><a href="?the_orderby=sesauthors&the_order=' . $the_order_var .'">Authors</strong><span class="sorting-indicator"></span></td><td><strong><a href="?the_orderby=journals&the_order=ASC">Journal</a></strong></td></tr>';	
 
  $startstring .= '<div id="accordion"><table><tr><td width="25%"><strong>Authors</strong></td><td width="35%"><strong>Title</strong></td><td width="25%"><strong><a href="?the_orderby=journals&the_order=ASC">Journal</a></strong></td><td width="15%"><strong><a href="?the_orderby=yearspublished&the_order=' . $neworder .'">Year</strong></td></tr>';	

  $endstring .= '</div></table>';
   $endstring .=	'</td></tr>';
   ob_start();
 //  next_posts_link('next page >', 0);
 //  $next_post_link = ob_get_contents();
 //  ob_clean();
 //  previous_posts_link('< previous page >', 0);
 //  $previous_posts_link = ob_get_contents();
 //  $endstring .= '<div class="pagenav">';
 //  $endstring .=  '<div align="left"><< previous ' . $previous_posts_link  . '</div>';
 //  $endstring .=  '<div align="center">next >>' . $next_posts_link  . '</div>';
 //  $endstring .=  '</div>';
  
}
if ($sesdisplay == 'accordion'){
$startstring .= '<div class="cahnrs-core-faq"><table><tr><td><strong><a href="?the_orderby=yearspublished&the_order=' . $the_order_var .'">Year</strong></td><td><strong>Title</strong></td><td><strong><a href="?the_orderby=sesauthors&the_order=' . $the_order_var .'">Authors</strong><span class="sorting-indicator"></span></td><td><strong><a href="?the_orderby=journals&the_order=ASC">Journal</a></strong></td></tr></table>';
 $endstring .= '</div>';	
}



if( $my_query->have_posts() ) {
  $string .= $startstring;
  while ($my_query->have_posts()) {
    $my_query->the_post(); 
//	$ja_meta = get_post_meta(get_the_ID(), '_journal_name', TRUE);
	$authors_meta = get_post_meta(get_the_ID(), '_authors', TRUE); 
	$sesauthorsdd_meta = get_post_meta(get_the_ID(), '_sesauthorsdd', TRUE); 
//	$yearpub_meta = get_post_meta(get_the_ID(), '_year_published', TRUE);
	$issuepages_meta = get_post_meta(get_the_ID(), '_issue_pages', TRUE);
	$redirectURL_meta = get_post_meta(get_the_ID(), '_redirect_to', TRUE);
	$short_title_meta = get_post_meta(get_the_ID(), '_short_title', TRUE);
    $ses_author_list =  wp_get_object_terms( get_the_ID(), 'sesauthors' );	
	$nonses_author_list =  wp_get_object_terms( get_the_ID(), 'nonsesauthors' );	
    $ses_journals_list =  wp_get_object_terms( get_the_ID(), 'journals' );	
	$ses_yearspublished_list =  wp_get_object_terms( get_the_ID(), 'yearspublished' );
    $allrelated_sesauthors = get_post_meta(get_the_ID(),'related_sesauthors',true);	
//	var_dump( $ses_author_list);
 //   echo $post_meta;
 
 if ($short_title_meta == "") {
   $short_title_meta = get_the_title();
 } 
 
// Full Display (old) 
 
  switch( $sesdisplay ){
        case 'full': 
//             $string .= '<br>'. $authors_meta . '. ' .$yearpub_meta. '. <a href="'.get_permalink().'" title="' .get_the_title().'">"' . get_the_title(). '"'.'</a> <i>' .$ja_meta. '</i>. '. $issuepages_meta . '.<br> URL: ' . '<a href="' . $redirectURL_meta . '">'. $redirectURL_meta . '</a>'.  '</br>';
	          $string .= '<br>'. $authors_meta . ' and ';
			  
			   if (! empty( $ses_author_list)){			
			  if ( ! is_wp_error( $ses_author_list ) ) {
				    foreach ( $ses_author_list as $ses_author) {
					   $string .= '<b>' . $ses_author->name . '</b>'; 
					  
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			  
			  $string .=  '. ';
			  
			   if (! empty( $ses_yearspublished_list)){
			  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                   foreach ( $ses_yearspublished_list as $ses_yearpublished) {
					   $string .= '' . $ses_yearpublished->name . ''; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty 
			   
			   
			  $string .= '. <a href="'.get_permalink().'" title="' .get_the_title().'">"' . $short_title_meta . '"'.'</a> <i>';
			  
			   if (! empty( $ses_journals_list)){
			  if ( ! is_wp_error( $ses_journals_list ) ) {
                   foreach ( $ses_journals_list as $ses_journal) {
					   $string .= '<a href="' . get_term_link( $ses_journal->slug, $ses_journals_list ) . '">' . $ses_journal->name . '</a></li>'; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			   
			   $string .= '</i>. '. $issuepages_meta . '.<br> URL: ' . '<a href="' . $redirectURL_meta . '">'. $redirectURL_meta . '</a>'.  '</br>';
			 
			 
            break; // End of Full Display

// Top Display - Default and updated

        case 'top': 
 //           $string .= '<tr><td><a href="'.get_permalink().'" title="' .get_the_title().'">"' . get_the_title(). '"'.'</a> (' . $yearpub_meta . ')</td><td> '; 
	        $string .= '<tr><td>';
			
//			$string .= 	$sesauthorsdd_meta . ' </td><td><strong> ';
//			$string .= 	$sesauthorsdd_meta . ' || ';
			
			if (!empty($allrelated_sesauthors)) {
			   if ( ! is_wp_error( $allrelated_sesauthors ) ) {
				
				$allrelated_sesauthors_array = json_decode($allrelated_sesauthors);
				$numItems = count($allrelated_sesauthors_array);
				$i = 0;
				 foreach($allrelated_sesauthors_array as $allrelated_sesauthor){
				  $sesauthorterm =  get_term($allrelated_sesauthor, 'nonsesauthors');
					//$string .= $sesauthorterm->name;
				//	 $string .= '<a href="?the_nonsesauthor=' . $sesauthorterm->slug . '">' . $sesauthorterm->name . '</a>'; 
				  $sesauthorparent = get_term($sesauthorterm->parent, 'nonsesauthors');
    			   if ($sesauthorparent->name == 'SES') {
//					  if (substr($sesauthorterm->name,-4) == '-SES') {
//					    $string .= '<b>' . '<a href="?the_nonsesauthor=' . $sesauthorterm->slug . '">' .  substr($sesauthorterm->name,0,-4) . '</a>' . '</b>';
					    $string .= '<b>' . '<a href="?the_nonsesauthor=' . $sesauthorterm->slug . '">' .  $sesauthorterm->name . '</a>' . '</b>';
				      }
				      else {
					   $string .= $sesauthorterm->name;
				      }
					if (++$i != $numItems) {$string .= ', ';}
				  } // foreach $allrelated_sesauthors_array
				 } // end not wp_error
			    } //end of !empty
				else {
				 $string .= 	$sesauthorsdd_meta . ' ++ ';
				}
                				
				$string .= 	' </td>';
            			
/*			if (! empty( $ses_yearspublished_list)){
			  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                   foreach ( $ses_yearspublished_list as $ses_yearpublished) {
//					   $string .= $ses_yearpublished->name; 
		   			   $string .= '<a href="?the_yearpublished=' . $ses_yearpublished->slug . '">' . $ses_yearpublished->name . '</a>'; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			
			 $string .= '</strong></td>';
*/			
			$string .= '<td><a href="'.get_permalink().'" title="' .get_the_title().'">"' . $short_title_meta . '"'.'</a> ';
    
			if (! empty( $ses_yearspublished_list)){
			  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                   foreach ( $ses_yearspublished_list as $ses_yearpublished) {
					   $string .= '(' . $ses_yearpublished->name . ') '; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			
//			$string .= '(' . $ses_yearpublished->name . ') '; 
          			
/*			$string .= '</td><td> ';
			
    	   if (! empty( $ses_author_list)){			
			  if ( ! is_wp_error( $ses_author_list ) ) {
                   $numItems = count($ses_author_list);
				   $i = 0;
                   foreach ( $ses_author_list as $ses_author) {
//					   $string .= '<b>' . $ses_author->name . '</b>'; 
					   $string .= '<b><a href="?the_sesauthor=' . $ses_author->slug . '">' . $ses_author->name . '</b>'; 
					    if (++$i != $numItems) {$string .= ', ';}
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
*/
			$string .= '</td><td>';
			
			if (! empty( $ses_journals_list)){
			  if ( ! is_wp_error( $ses_journals_list ) ) {
                   foreach ( $ses_journals_list as $ses_journal) {
//					   $string .= '<i>'.  . $ses_journal->name . '</i>'; 
	     	   		   $string .= '<i><a href="?the_journal='.  $ses_journal->slug . '">' . $ses_journal->name . '</i>'; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			$string .= '</td><td><b><a href="?the_yearpublished=' . $ses_yearpublished->slug . '">' . $ses_yearpublished->name . '</a></b>'; 
//			$string .= '</td><td><b>' . $ses_yearpublished->name . '</b>';
			
//            $string .=	'</td></tr></br>'; 
/*              $string .=	'</td></tr>';
			  $string .= '<div class="navigation">';
              $string .= '<div class="alignleft">';  
			  $string .=  next_posts_link('&laquo; Older Entries');
			  $string .=  '</div>';
              $string .= '<div class="alignright">' . previous_posts_link('Newer Entries &raquo;') .'</div></div>'; */
            break;
			
// Accordion Display option			

			case 'accordion':

  $ses_author_string = '';
  $ses_author_string_link = '';
 // $string = '';	

// Generating Output 

//$ses_author_string = '';
 
	$ses_title = $post->post_title;
    	
	if ($short_title_meta == "") {
       $short_title_meta = $post->post_title;
     } 
	 $string .=   '<a href="">';
	 
     if (! empty( $ses_yearspublished_list)){
		  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                  foreach ( $ses_yearspublished_list as $ses_yearpublished) {
				   $string .= '' . $ses_yearpublished->name . ''; 
				   $year_pub = $ses_yearpublished->name;
				   } // end foreach
			  } // end not wp_error
			} // end if not empty 
	 
    $string .=   ' - '. $short_title_meta ;
	

        if (! empty( $ses_author_list)){			
			  if ( ! is_wp_error( $ses_author_list ) ) {
                   $numItems = count($ses_author_list);
				   $i = 0;
                   foreach ( $ses_author_list as $ses_author) {
//					   $string .= '<b>' . $ses_author->name . '</b>'; 
					   $ses_author_string_link .= '<b><a href="?the_sesauthor=' . $ses_author->slug . '">' . $ses_author->name . '</a></b>.'; 
					    if (++$i != $numItems) {$ses_author_string_link .= ', ';}
		
//					   $ses_author_string .= '<b><a href="?the_sesauthor=' . $ses_author->slug . '">' . $ses_author->name . '</a></b>.'; 
	   				   $ses_author_string .= ' - ' . $ses_author->name . ''; 
 //  					    if (++$i != $numItems) {$ses_author_string .= ', ';}
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
        
			$string .=  $ses_author_string;
	
         	$string .=  '</a>';
				
			$string .=  '<div class="cc-content">';
	 
	        $string .= $authors_meta . ' and ';
			
			$string .= $ses_author_string_link;
			
			$string .= ' ' . $year_pub . '. ';	
			
			$string .= ' ' . get_the_title() . ' ';

			   if (! empty( $ses_journals_list)){
			  if ( ! is_wp_error( $ses_journals_list ) ) {
                   foreach ( $ses_journals_list as $ses_journal) {
                       $string .=  '<i>'. $ses_journal->name . '</i>'; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			   
			   $string .= '. '. $issuepages_meta . '. <p> URL: ' . '<a href="' . $redirectURL_meta . '">'. $redirectURL_meta . '</a>'.  '</p>';
			


$string .= '</div>';

			
			break; //End of 'top'

        default:
		
          $string .= '<tr><td><a href="'.$get_permalink().'" title="' .get_the_title().'">"' . get_the_title(). '"'.'</a><b> (' . $yearpub_meta . ')</b></td><td> '. $authors_meta . '</td></tr>';  
            break;
    }

 
     } // end of while have_posts
	 
   $string .= $endstring;
   
    
 }
wp_reset_query();  // Restore global post data stomped by the_post().
return $string;

} //end display_ses_publications 


	 
	 public function cahnrswp_the_permalink( $link ){
		 
		 global $post;
		 
		 if( 'publication' == $post->post_type ) {
			 
			 $meta = get_post_meta( $post->ID , '_redirect_to' , true );
			 
			 if( $meta ){
				 
				 $link = $meta;
				 
			 } // end if $meta
			 
		 } // end if post_type
		 
		 return $link;
		 
	 } // end cahnrswp_the_permalink
	
} // end class 



class CAHNRSWP_SESPUB_Model {
	
	public $post_date;
	
	public $redirect;
	
	
	public function __construct(){
	}
	
	public function set_sespub( $post_id = false ) {
		
		$date = \get_post_meta( $post_id , '_post_date', true );
		
		$this->post_date = ( $date )? date( 'm', $date ).'/'.date( 'd', $date ).'/'.date( 'y', $date ) : $date;
		
		$this->short_title = \get_post_meta( $post_id , '_short_title', true );
		
		$this->redirect = \get_post_meta( $post_id , '_redirect_to', true );
		
		$this->authors = \get_post_meta( $post_id , '_authors', true );

		$this->sesauthorsdd = \get_post_meta( $post_id , '_sesauthorsdd', true );

		
//		$this->year_published = \get_post_meta( $post_id , '_year_published', true );
		
//		$this->journal_name = \get_post_meta( $post_id , '_journal_name', true );
		
		$this->issue_pages = \get_post_meta( $post_id , '_issue_pages', true );
		
		
		
	} // end set sespub
	
	public function save_sespub( $post_id ){
		
		if ( ! isset( $_POST['sespub_nonce'] ) ) return;
		
		if ( ! wp_verify_nonce( $_POST['sespub_nonce'], 'submit_sespub' ) ) return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		
		$fields = array(
			'_post_date'   => 'text',
			'_redirect_to' => 'text',
			'_short_title' => 'text',
			'_authors' => 'text',
//			'_year_published' => 'text',
//			'_journal_name' => 'text',
			'_issue_pages' => 'text',
			'_sesauthorsdd' => 'textarea',
					
		);
		
		$allowed_tags = array( ‘strong’ => array(),‘b’ => array());
		
		foreach( $fields as $f_key => $f_data ){
		
			
			if( isset( $_POST[ $f_key ] ) ){
				if('_sesauthorsdd' == $f_key ) {
				    
				    $instance = $_POST[ $f_key ];
					
				}
				else {
					$instance = sanitize_text_field( $_POST[ $f_key ] );
				}
				
				if( '_post_date' == $f_key ){ 
				
					$instance = strtotime( $instance );
					
				}
				
				update_post_meta( $post_id , $f_key , $instance );
				
			} // end if
			
		} // end foreach
		
	} // end save_sespub
	
} // end class CAHNRSWP_SESPUB_Model

class CAHNRSWP_SESPUB_View {
	
	private $control;
	private $model;
	public $view;
	
	public function __construct( $control , $model ){
		
		$this->control = $control;
		$this->model = $model;
		
	} // end __construct
	
	public function output_editor(){
		  
		include CAHNRSWPRFPDIR . 'inc/editor.php';
		
	}
	
} // end class CAHNRSWP_SESPUB_View

$cahnrswp_SESPUB = CAHNRSWP_SESPUB_Init::get_instance();