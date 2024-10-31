<?php
/*
Plugin Name: Posts Carousel Widget
Plugin URI: http://www.devcodeit.com/posts-carousel-widget
Description: Displays posts inside a carousel which will be added to widgets.
Version: 1.1
Author: devcodeit
Author URI: http://devcodeit.com/
License: GPL2
*/

/*  Copyright 2012  devcodeit  (email : admin@devcodeit.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* 
function postcarousel_init_func() { }
add_action('init', 'postcarousel_init_func'); 
*/

function pcw_plugin_menu() {
	add_options_page('Posts Carousel Options', 'Posts Carousel', 'manage_options', 'posts-carousel', 'postcarous_coptions_func', '', 3);
	add_action( 'admin_init' , 'postcarous_register_my_settings' );
}
add_action('admin_menu', 'pcw_plugin_menu');

function postcarous_register_my_settings(){
	register_setting( 'postcarouopts', 'postcarouopts' );
}

function postcarous_coptions_func(){
	include('postcarous_options.php');	
}



function postcarousel_scripts_func() {
	
	if( !is_admin() ){ 
	
		wp_register_script(
			'jcarousellite',
			plugins_url('/js/jcarousellite_1.0.1c4.js', __FILE__),
			array('jquery')
		);
		
		wp_enqueue_script( 'jcarousellite' );
		
	}
}
add_action('wp_print_scripts', 'postcarousel_scripts_func');

function postcarousel_head_func() {
		
		$options = get_option('postcarouopts');
		$hoverpause = ( $options['hover_pause'] == 1 )? 'true' : 'false';
		$visible = !empty( $options['visible'] )? $options['visible'] : 1;
		
		$auto = !empty( $options['scroll_milli'] )? $options['scroll_milli'] : 800;
		$speed = !empty( $options['speed'] )? $options['speed'] : 1000;
	?>
		
		<script type="text/javascript">
			jQuery(function() {
				jQuery("#postscarouselwidget").jCarouselLite({
					vertical: true,
					hoverPause: <?php echo $hoverpause; ?>,
					visible: <?php echo $visible; ?>,
					auto: <?php echo $auto; ?>,
					speed: <?php echo $speed; ?>
				});
			});
		</script>
		
	<?php
}
add_action('wp_head', 'postcarousel_head_func');


class PostsCarouselWidget extends WP_Widget {

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'postscarouselwidget', // Base ID
			'Posts Carousel Widget', // Name
			array( 'description' => __( 'A Carousel Widget for posts', 'text_domain' ), ) // Args
		);
	}

 	public function form( $instance ) {
		// outputs the options form on admin
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		
		$category = isset( $instance[ 'category' ] )? $instance[ 'category' ] : 0;
		$posts_per_page = ( isset( $instance[ 'posts_per_page' ] ) && !empty( $instance[ 'posts_per_page' ] ) ) ? $instance[ 'posts_per_page' ] : 10;
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>">
			<?php
			
				$args = array(
							'type'                     => 'post',
							'child_of'                 => 0,
							'parent'                   => '',
							'orderby'                  => 'name',
							'order'                    => 'ASC',
							'hide_empty'               => 0,
							'hierarchical'             => 1,
							'exclude'                  => '',
							'include'                  => '',
							'number'                   => '',
							'taxonomy'                 => 'category',
							'pad_counts'               => false );
							
				$args=array(
							  'orderby' => 'name',
							  'hide_empty' => 0,
							  'order' => 'ASC'
							);
				
				$categs = get_categories( $args );
		
				foreach ( $categs as $categ ) {
					$selected = ( esc_attr( $category ) == $categ->term_id )? 'selected="selected"': '';
					
					$option = '<option value="' . $categ->term_id . '" '. $selected .'>';
					$option .= $categ->name;
					$option .= '</option>';
					echo $option;
				}
			?>
			</select>
		
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e( 'Posts per page:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text" value="<?php echo esc_attr( $posts_per_page ); ?>" />
		</p>
		
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['post_per_page'] = strip_tags( $new_instance['post_per_page'] );

		return $instance;
		
	}
	
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		query_posts('cat='.$instance['category'].'&posts_per_page='.$instance['title']);
		$posts = '';
		// The Loop
		while ( have_posts() ) : the_post();
			$posts .= '<li><div class="carousel-item"><a href="'. get_permalink() .'">'.apply_filters( 'the_content', get_the_content() ).'</a></div></li>';
		endwhile;
		
		echo '<div id="postscarouselwidget"><ul>' . $posts . '</ul></div>';
		
		// Reset Query
		wp_reset_query();

		
		echo $after_widget;
	}
	
	

}

// register Foo_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "postscarouselwidget" );' ) );

?>