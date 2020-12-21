<?php
class CAHNRSWP_Widget_SESPUB extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'publications', // Base ID
			__( 'Publications', 'text_domain' ), // Name
			array( 'description' => __( 'Display lists of current PUblications', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		
		$the_query = $this->get_query();
		
		if ( $the_query->have_posts() ) {
			
			echo $args['before_widget']; 
			
			while ( $the_query->have_posts() ) {
				
				$the_query->the_post();
				
				$date = get_post_meta( $the_query->post->ID , '_post_date', true );
				
				if( $date ) {
					
					$date = date( 'D, d M Y' , $date );
					
				}
				
				include '../../ses-publications/widget/inc/ses-pub.php';
			}
			
			echo $args['after_widget'];
			
		} else {
			
			// no posts found
			
		}

		wp_reset_postdata();
		
	}

	public function form( $instance ) {
		// outputs the options form on admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
	
	private function get_query(){
		
		$args['post_type'] = 'publication';
		
		$args['posts_per_page'] = '-1';
		
		$args['meta_key'] = '_post_date';
		
		$args['order'] = 'ASC';
		
        $args['orderby']   = 'meta_value';
		
		$meta_query['key'] = '_post_date';
		
		$meta_query['value'] = time(); // Set to current time
		
		$meta_query['compare'] = '>=';
		
		$args['meta_query'] = array( $meta_query );
		
		$query = new WP_Query( $args );
		
		return $query;
	}
}

register_widget( 'CAHNRSWP_Widget_SESPUBS' );