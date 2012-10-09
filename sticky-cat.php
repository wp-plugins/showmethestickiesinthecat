<?php
/*
Plugin Name: ShowMeTheStickiesInTheCat
Plugin URI: http://cryptum.net/
Description: This widget shows posts that are stickied under a certain category.
Version: 1.0
Author: Liam (liamzebedee) Edwards-Playne
Author URI: http://cryptum.net/
License: GPL2
*/
class ShowMeTheStickiesInTheCat_Widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'show_me_the_stickies_in_the_cat', // Base ID
			'ShowMeTheStickiesInTheCat', // Name
			array( 'description' => __( 'Displays a list of stickied posts within a category and links to the full archive.', 'text_domain' ), ) // Args
		);
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
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		//= Posts
		// The Query
		$query_args = array(
			'meta_key' => 'sticky',
			'cat' => $instance['category_id'],
			'showposts' => $instance['max_posts']
		);
		$the_query = new WP_Query( $query_args );
		
		// The Loop
		?><ul class="nav nav-list"><?php
		while ( $the_query->have_posts() ) : $the_query->the_post();
			if ( isset( $instance['show_title_only'] ) ):
				?><li><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title() ?></a></li><?php
			else:
				?><li><?php get_template_part( 'content', get_post_format() );
			endif;
		endwhile;
		
		// Get the URL of this category
		$category_link = get_category_link( $instance['category_id'] );
		?>
		<li class="divider"></li>
		<li><a href="<?php echo esc_url( $category_link ); ?>"><?php _e( 'See more...', 'encouragement' ) ?></a></li>
		</ul>
		<?php
		// Reset Post Data
		wp_reset_postdata();
		
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['category_id'] = $new_instance['category_id'];
		$instance['max_posts'] = $new_instance['max_posts'];
		if ( isset($new_instance['show_title_only']) )
			$instance['show_title_only'] = 1;
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		// Title [string]
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'text_domain' );
		}
		
		// Category [dropdown]
		if ( isset( $instance[ 'category_id' ] ) ) {
			$category_id = $instance[ 'category_id' ];
		} else {
			$category_id = 0;
		}
		
		// Max posts [int]
		if ( isset( $instance[ 'max_posts' ] ) ) {
			$max_posts = $instance[ 'max_posts' ];
		} else {
			$max_posts = 5;
		}
		?>
		
		<p>
		
		<?php // Title ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<?php // Category ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'category_id' ); ?>"><?php _e( 'Category:' ); ?></label><br>
			<?php
			$args = array(
				'name' => $this->get_field_name( 'category_id' ),
				'id'   => $this->get_field_id( 'category_id' ),
				'selected' => $category_id,
				'hierarchical' => true,
				'depth' => 5,
				'hide_empty' => false
			);
			wp_dropdown_categories($args); ?>
		</p>
		
		<?php // Max posts ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'max_posts' ); ?>"><?php _e( 'Maximum posts to show:' ); ?></label><br>
			<input type="number" min="1" max="50" value="<?php echo $instance['max_posts'] ?>" id="<?php echo $this->get_field_id( 'max_posts' ); ?>" name="<?php echo $this->get_field_name( 'max_posts' ); ?>">
		</p>
		
		<?php // Show title only ?>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( isset( $instance['show_title_only'] ), 1 ) ?> id="<?php echo $this->get_field_id( 'show_title_only' ); ?>" name="<?php echo $this->get_field_name( 'show_title_only' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_title_only' ); ?>"><?php _e( 'Show only the title?' ); ?></label>
		</p>
		
		</p>
		<?php 
	}

}

function stickycat_init() {
	return register_widget( 'ShowMeTheStickiesInTheCat_Widget' );
}
// Add ShowMeTheStickiesInTheCat widget
add_action( 'widgets_init', 'stickycat_init' );
?>