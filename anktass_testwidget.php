<?php
/*
Plugin Name: Anktass TestWidget
Description: Test Widget for exam
Version: 1.0
Author: Peter Larsson
Author URI: http://www.anktass.se
*/

// Registers a Custom Post Type called TestPosts
function test_cpt_init()
{
    $args = array(
      'public' => true,
      'label'  => 'TestPosts',
      'singular_name' => 'TestPost'
    );
    register_post_type('TestPosts', $args);
}
// Hook it to the init hook.
add_action('init', 'test_cpt_init');

// Create a class for my widget that extends the wordpress class WP_Widget
class wp_my_plugin extends WP_Widget
{
    // The constructor
    function wp_my_plugin()
    {
        parent::WP_Widget(false, $name = __('TestPosts Display Widget', 'wp_widget_plugin'));
    }

    // The widget admin form
    function form($instance)
    {
        if($instance) {
            $showNumPosts = esc_attr($instance['showNumPosts']);
        } else {
            $showNumPosts = '5';
        }

        print "<p>";
        print _e('Number of posts to show', 'wp_widget_plugin') . " ";
        print "<input id='" . $this->get_field_id('showNumPosts') . "' name='" . $this->get_field_name('showNumPosts') . "' type='number' value='" . $showNumPosts . "' />";
        print "</p>";
    }

    // Widget admin update handling
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['showNumPosts'] = strip_tags($new_instance['showNumPosts']);
        return $instance;
    }

    // Widget front-end
    function widget($args, $instance) {
        $showNumPosts = $instance['showNumPosts'];

        echo "<div class='widget-text wp_widget_plugin_box'>";

        $wp_query_args = array('post_type' => 'TestPosts', 'posts_per_page' => $showNumPosts);
        $loop = new WP_Query($wp_query_args);
 
        while ($loop->have_posts()) : $loop->the_post();
            $title = get_the_title();  // Get the title of the post
            $excerpt = get_the_excerpt();  // Get the excerpt from the post content

            preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', get_the_content(), $first_image); // Extract the first image of the post content.   
            $first_image_url = $first_image['src'];  // Set the first_image_url (just cleaner looking). Empty if no images exists.
            include('views/display_widget.php');

        endwhile;

        echo '</div>';
     }
}

// Register the widget
add_action('widgets_init', create_function('', 'return register_widget("wp_my_plugin");'));

?>
