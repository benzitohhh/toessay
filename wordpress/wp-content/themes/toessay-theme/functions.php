<?php

/*** Theme setup ***/

add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );

function sight_setup() {
    update_option('thumbnail_size_w', 154);
    update_option('thumbnail_size_h', 154);
    add_image_size( 'mini-thumbnail', 50, 50, true );
    add_image_size( 'slide', 640, 290, true );
    register_nav_menu('Navigation', __('Navigation'));
    register_nav_menu('Top menu', __('Top menu'));
}
add_action( 'init', 'sight_setup' );

if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
    update_option( 'posts_per_page', 12 );
    update_option( 'paging_mode', 'default' );
}

/*** Navigation ***/

if ( !is_nav_menu('Navigation') || !is_nav_menu('Top menu') ) {
    $menu_id1 = wp_create_nav_menu('Navigation');
    $menu_id2 = wp_create_nav_menu('Top menu');
    wp_update_nav_menu_item($menu_id1, 1);
    wp_update_nav_menu_item($menu_id2, 1);
}

class extended_walker extends Walker_Nav_Menu{
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];

		//display this element
		if ( is_array( $args[0] ) )
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );

		//Adds the 'parent' class to the current item if it has children
		if( ! empty( $children_elements[$element->$id_field] ) )
			array_push($element->classes,'parent');

		$cb_args = array_merge( array(&$output, $element, $depth), $args);

		call_user_func_array(array(&$this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'end_el'), $cb_args);
	}
}

/*** Slideshow ***/

$prefix = 'sgt_';

$meta_box = array(
    'id' => 'slide',
    'title' => 'Slideshow Options',
    'page' => 'post',
    'context' => 'side',
    'priority' => 'low',
    'fields' => array(
        array(
            'name' => 'Show in slideshow',
            'id' => $prefix . 'slide',
            'type' => 'checkbox'
        )
    )
);
add_action('admin_menu', 'sight_add_box');

// Add meta box
function sight_add_box() {
    global $meta_box;

    add_meta_box($meta_box['id'], $meta_box['title'], 'sight_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}

// Callback function to show fields in meta box
function sight_show_box() {
    global $meta_box, $post;

    // Use nonce for verification
    echo '<input type="hidden" name="sight_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    echo '<table class="form-table">';

    foreach ($meta_box['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);

        echo '<tr>',
                '<th style="width:50%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                '<td>';
                echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
        echo     '<td>',
            '</tr>';
    }

    echo '</table>';
}

add_action('save_post', 'sight_save_data');

// Save data from meta box
function sight_save_data($post_id) {
    global $meta_box;

    // verify nonce
    if (!wp_verify_nonce($_POST['sight_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    foreach ($meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];

        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}

/*** Options ***/

function options_admin_menu() {
	// here's where we add our theme options page link to the dashboard sidebar
	add_theme_page("Sight Theme Options", "Theme Options", 'edit_themes', basename(__FILE__), 'options_page');
}
add_action('admin_menu', 'options_admin_menu');

function options_page() {
    if ( $_POST['update_options'] == 'true' ) { options_update(); }  //check options update
	?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
		<h2>Sight Theme Options</h2>

        <form method="post" action="">
			<input type="hidden" name="update_options" value="true" />

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="logo_url"><?php _e('Custom logo URL:'); ?></label></th>
                    <td><input type="text" name="logo_url" id="logo_url" size="50" value="<?php echo get_option('logo_url'); ?>"/><br/><span
                            class="description"> <a href="<?php bloginfo("url"); ?>/wp-admin/media-new.php" target="_blank">Upload your logo</a> (max 290px x 128px) using WordPress Media Library and insert its URL here </span><br/><br/><img src="<?php echo (get_option('logo_url')) ? get_option('logo_url') : get_bloginfo('template_url') . '/images/logo.png' ?>"
                     alt=""/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="bg_color"><?php _e('Custom background color:'); ?></label></th>
                    <td><input type="text" name="bg_color" id="bg_color" size="20" value="<?php echo get_option('bg_color'); ?>"/><span
                            class="description"> e.g., <strong>#27292a</strong> or <strong>black</strong></span></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ss_disable"><?php _e('Disable slideshow:'); ?></label></th>
                    <td><input type="checkbox" name="ss_disable" id="ss_disable" <?php echo (get_option('ss_disable'))? 'checked="checked"' : ''; ?>/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ss_timeout"><?php _e('Timeout for slideshow (ms):'); ?></label></th>
                    <td><input type="text" name="ss_timeout" id="ss_timeout" size="20" value="<?php echo get_option('ss_timeout'); ?>"/><span
                            class="description"> e.g., <strong>7000</strong></span></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Pagination:'); ?></label></th>
                    <td>
                        <input type="radio" name="paging_mode" value="default" <?php echo (get_option('paging_mode') == 'default')? 'checked="checked"' : ''; ?>/><span class="description">Default + WP Page-Navi support</span><br/>
                        <input type="radio" name="paging_mode" value="ajax" <?php echo (get_option('paging_mode') == 'ajax')? 'checked="checked"' : ''; ?>/><span class="description">AJAX-fetching posts</span><br/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ga"><?php _e('Google Analytics code:'); ?></label></th>
                    <td><textarea name="ga" id="ga" cols="48" rows="18"><?php echo get_option('ga'); ?></textarea></td>
                </tr>
            </table>

            <p><input type="submit" value="Save Changes" class="button button-primary" /></p>
        </form>
    </div>
<?php
}

// Update options

function options_update() {
	update_option('logo_url', $_POST['logo_url']);
	update_option('bg_color', $_POST['bg_color']);
	update_option('ss_disable', $_POST['ss_disable']);
	update_option('ss_timeout', $_POST['ss_timeout']);
	update_option('paging_mode', $_POST['paging_mode']);
	update_option('ga', stripslashes_deep($_POST['ga']));
}

/*** Widgets ***/

if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name'=>'Site description',
        'before_widget' => '<div class="site-description">',
        'after_widget' => '</div>'
    ));
    register_sidebar(array(
        'name'=>'Sidebar',
        'before_widget' => '<div id="%1$s" class="%2$s widget">',
        'after_widget' => '</div></div>',
        'before_title' => '<h3>',
        'after_title' => '</h3><div class="widget-body clear">'
    ));
}

class GetConnected extends WP_Widget {

    function GetConnected() {
        parent::WP_Widget(false, $name = 'Sight Social Links');
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
            <?php echo $before_widget; ?>
                <?php if ( $title )
                    echo $before_title . $title . $after_title;  else echo '<div class="widget-body clear">'; ?>

                    <!-- RSS -->
                    <div class="getconnected_rss">
                    <a href="<?php echo ( get_option('feedburner_url') )? get_option('feedburner_url') : get_bloginfo('rss2_url'); ?>">RSS Feed</a>
                    <?php echo (get_option('feedburner_url') && function_exists('feedcount'))? feedcount( get_option('feedburner_url') ) : ''; ?>
                    </div>
                    <!-- /RSS -->

                    <!-- Twitter -->
                    <?php if ( get_option('twitter_url') ) : ?>
                    <div class="getconnected_twitter">
                    <a href="<?php echo get_option('twitter_url'); ?>">Twitter</a>
                    <span><?php if ( function_exists('twittercount') ) twittercount( get_option('twitter_url') ); ?> followers</span>
                    </div>
                    <?php endif; ?>
                    <!-- /Twitter -->

                    <!-- Facebook -->
                    <?php if ( get_option('fb_url') ) : ?>
                    <div class="getconnected_fb">
                    <a href="<?php echo get_option('fb_url'); ?>">Facebook</a>
                    <span><?php echo get_option('fb_text'); ?></span>
                    </div>
                    <?php endif; ?>
                    <!-- /Facebook -->

                    <!-- Flickr -->
                    <?php if ( get_option('flickr_url') ) : ?>
                    <div class="getconnected_flickr">
                    <a href="<?php echo get_option('flickr_url'); ?>">Flickr group</a>
                    <span><?php echo get_option('flickr_text'); ?></span>
                    </div>
                    <?php endif; ?>
                    <!-- /Flickr -->

                    <!-- Behance -->
                    <?php if ( get_option('behance_url') ) : ?>
                    <div class="getconnected_behance">
                    <a href="<?php echo get_option('behance_url'); ?>">Behance</a>
                    <span><?php echo get_option('behance_text'); ?></span>
                    </div>
                    <?php endif; ?>
                    <!-- /Behance -->

                    <!-- Delicious -->
                    <?php if ( get_option('delicious_url') ) : ?>
                    <div class="getconnected_delicious">
                    <a href="<?php echo get_option('delicious_url'); ?>">Delicious</a>
                    <span><?php echo get_option('delicious_text'); ?></span>
                    </div>
                    <?php endif; ?>
                    <!-- /Delicious -->

                    <!-- Stumbleupon -->
                    <?php if ( get_option('stumbleupon_url') ) : ?>
                    <div class="getconnected_stumbleupon">
                    <a href="<?php echo get_option('stumbleupon_url'); ?>">Stumbleupon</a>
                    <span><?php echo get_option('stumbleupon_text'); ?></span>
                    </div>
                    <?php endif; ?>
                    <!-- /Stumbleupon -->

                    <!-- Tumblr -->
                    <?php if ( get_option('tumblr_url') ) : ?>
                    <div class="getconnected_tumblr">
                    <a href="<?php echo get_option('tumblr_url'); ?>">Tumblr</a>
                    <span><?php echo get_option('tumblr_text'); ?></span>
                    </div>
                    <?php endif; ?>
                    <!-- /Tumblr -->

                    <!-- Vimeo -->
                    <?php if ( get_option('vimeo_url') ) : ?>
                    <div class="getconnected_vimeo">
                    <a href="<?php echo get_option('vimeo_url'); ?>">Vimeo</a>
                    <span><?php echo get_option('vimeo_text'); ?></span>
                    </div>
                    <?php endif; ?>
                    <!-- /Vimeo -->

                    <!-- Youtube -->
                    <?php if ( get_option('youtube_url') ) : ?>
                    <div class="getconnected_youtube">
                    <a href="<?php echo get_option('youtube_url'); ?>">Youtube</a>
                    <span><?php echo get_option('youtube_text'); ?></span>
                    </div>
                    <?php endif; ?>
                    <!-- /Youtube -->

            <?php echo $after_widget; ?>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        
        update_option('feedburner_url', $_POST['feedburner_url']);
        update_option('twitter_url', $_POST['twitter_url']);
        update_option('fb_url', $_POST['fb_url']);
        update_option('flickr_url', $_POST['flickr_url']);
        update_option('behance_url', $_POST['behance_url']);
        update_option('delicious_url', $_POST['delicious_url']);
        update_option('stumbleupon_url', $_POST['stumbleupon_url']);
        update_option('tumblr_url', $_POST['tumblr_url']);
        update_option('vimeo_url', $_POST['vimeo_url']);
        update_option('youtube_url', $_POST['youtube_url']);
        
        update_option('fb_text', $_POST['fb_text']);
        update_option('flickr_text', $_POST['flickr_text']);
        update_option('behance_text', $_POST['behance_text']);
        update_option('delicious_text', $_POST['delicious_text']);
        update_option('stumbleupon_text', $_POST['stumbleupon_text']);
        update_option('tumblr_text', $_POST['tumblr_text']);
        update_option('vimeo_text', $_POST['vimeo_text']);
        update_option('youtube_text', $_POST['youtube_text']);
        
        return $instance;
    }

    function form($instance) {

        $title = esc_attr($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

            <script type="text/javascript">
                (function($) {
                    $(function() {
                        $('.social_options').hide();
                        $('.social_title').toggle(
                            function(){ $(this).next().slideDown(100) },
                            function(){ $(this).next().slideUp(100) }
                        );
                    })
                })(jQuery)
            </script>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">FeedBurner</a>
                <p class="social_options">
                    <label for="feedburner_url"><?php _e('FeedBurner feed url:'); ?></label>
                    <input type="text" name="feedburner_url" id="feedburner_url" class="widefat"
                           value="<?php echo get_option('feedburner_url'); ?>"/>
                </p>
            </div>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">Twitter</a>
                <p class="social_options">
                    <label for="twitter_url">Profile url:</label>
                    <input type="text" name="twitter_url" id="twitter_url" class="widefat" value="<?php echo get_option('twitter_url'); ?>"/>
                </p>
            </div>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">Facebook</a>
                <p class="social_options">
                    <label for="fb_url">Profile url:</label>
                    <input type="text" name="fb_url" id="fb_url" class="widefat" value="<?php echo get_option('fb_url'); ?>"/>
                    <label for="fb_text">Description:</label>
                    <input type="text" name="fb_text" id="fb_text" class="widefat" value="<?php echo get_option('fb_text'); ?>"/>
                </p>
            </div>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">Flickr</a>
                <p class="social_options">
                    <label for="flickr_url">Profile url:</label>
                    <input type="text" name="flickr_url" id="flickr_url" class="widefat" value="<?php echo get_option('flickr_url'); ?>"/>
                    <label for="flickr_text">Description:</label>
                    <input type="text" name="flickr_text" id="flickr_text" class="widefat" value="<?php echo get_option('flickr_text'); ?>"/>
                </p>
            </div>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">Behance</a>
                <p class="social_options">
                    <label for="behance_url">Profile url:</label>
                    <input type="text" name="behance_url" id="behance_url" class="widefat" value="<?php echo get_option('behance_url'); ?>"/>
                    <label for="behance_text">Description:</label>
                    <input type="text" name="behance_text" id="behance_text" class="widefat" value="<?php echo get_option('behance_text'); ?>"/>
                </p>
            </div>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">Delicious</a>
                <p class="social_options">
                    <label for="delicious_url">Profile url:</label>
                    <input type="text" name="delicious_url" id="delicious_url" class="widefat" value="<?php echo get_option('delicious_url'); ?>"/>
                    <label for="delicious_text">Description:</label>
                    <input type="text" name="delicious_text" id="delicious_text" class="widefat" value="<?php echo get_option('delicious_text'); ?>"/>
                </p>
            </div>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">Stumbleupon</a>
                <p class="social_options">
                    <label for="stumbleupon_url">Profile url:</label>
                    <input type="text" name="stumbleupon_url" id="stumbleupon_url" class="widefat" value="<?php echo get_option('stumbleupon_url'); ?>"/>
                    <label for="stumbleupon_text">Description:</label>
                    <input type="text" name="stumbleupon_text" id="stumbleupon_text" class="widefat" value="<?php echo get_option('stumbleupon_text'); ?>"/>
                </p>
            </div>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">Tumblr</a>
                <p class="social_options">
                    <label for="tumblr_url">Profile url:</label>
                    <input type="text" name="tumblr_url" id="tumblr_url" class="widefat" value="<?php echo get_option('tumblr_url'); ?>"/>
                    <label for="tumblr_text">Description:</label>
                    <input type="text" name="tumblr_text" id="tumblr_text" class="widefat" value="<?php echo get_option('tumblr_text'); ?>"/>
                </p>
            </div>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">Vimeo</a>
                <p class="social_options">
                    <label for="vimeo_url">Profile url:</label>
                    <input type="text" name="vimeo_url" id="vimeo_url" class="widefat" value="<?php echo get_option('vimeo_url'); ?>"/>
                    <label for="vimeo_text">Description:</label>
                    <input type="text" name="vimeo_text" id="vimeo_text" class="widefat" value="<?php echo get_option('vimeo_text'); ?>"/>
                </p>
            </div>

            <div style="margin-bottom: 5px;">
                <a href="javascript: void(0);" class="social_title" style="font-size: 13px; display: block; margin-bottom: 5px;">Youtube</a>
                <p class="social_options">
                    <label for="youtube_url">Profile url:</label>
                    <input type="text" name="youtube_url" id="youtube_url" class="widefat" value="<?php echo get_option('youtube_url'); ?>"/>
                    <label for="youtube_text">Description:</label>
                    <input type="text" name="youtube_text" id="youtube_text" class="widefat" value="<?php echo get_option('youtube_text'); ?>"/>
                </p>
            </div>
        <?php
    }

}
add_action('widgets_init', create_function('', 'return register_widget("GetConnected");'));

class Recentposts_thumbnail extends WP_Widget {

    function Recentposts_thumbnail() {
        parent::WP_Widget(false, $name = 'Sight Recent Posts');
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
            <?php echo $before_widget; ?>
            <?php if ( $title ) echo $before_title . $title . $after_title;  else echo '<div class="widget-body clear">'; ?>

            <?php
                global $post;
                if (get_option('rpthumb_qty')) $rpthumb_qty = get_option('rpthumb_qty'); else $rpthumb_qty = 5;
                $q_args = array(
                    'numberposts' => $rpthumb_qty,
                );
                $rpthumb_posts = get_posts($q_args);
                foreach ( $rpthumb_posts as $post ) :
                    setup_postdata($post);
            ?>

                <a href="<?php the_permalink(); ?>" class="rpthumb clear">
                    <?php if ( has_post_thumbnail() && !get_option('rpthumb_thumb') ) {
                        the_post_thumbnail('mini-thumbnail');
                        $offset = 'style="padding-left: 65px;"';
                    }
                    ?>
                    <span class="rpthumb-title" <?php echo $offset; ?>><?php the_title(); ?></span>
                    <span class="rpthumb-date" <?php echo $offset; unset($offset); ?>><?php the_time(__('M j, Y')) ?></span>
                </a>

            <?php endforeach; ?>

            <?php echo $after_widget; ?>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        update_option('rpthumb_qty', $_POST['rpthumb_qty']);
        update_option('rpthumb_thumb', $_POST['rpthumb_thumb']);
        return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            <p><label for="rpthumb_qty">Number of posts:  </label><input type="text" name="rpthumb_qty" id="rpthumb_qty" size="2" value="<?php echo get_option('rpthumb_qty'); ?>"/></p>
            <p><label for="rpthumb_thumb">Hide thumbnails:  </label><input type="checkbox" name="rpthumb_thumb" id="rpthumb_thumb" <?php echo (get_option('rpthumb_thumb'))? 'checked="checked"' : ''; ?>/></p>
        <?php
    }

}
add_action('widgets_init', create_function('', 'return register_widget("Recentposts_thumbnail");'));

/*** Comments ***/

function commentslist($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li>
        <div id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
            <table>
                <tr>
                    <td>
                        <?php echo get_avatar($comment, 70, get_bloginfo('template_url').'/images/no-avatar.png'); ?>
                    </td>
                    <td>
                        <div class="comment-meta">
                            <?php printf(__('<p class="comment-author"><span>%s</span> says:</p>'), get_comment_author_link()) ?>
                            <?php printf(__('<p class="comment-date">%s</p>'), get_comment_date('M j, Y')) ?>
                            <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
                        </div>
                    </td>
                    <td>
                        <div class="comment-text">
                            <?php if ($comment->comment_approved == '0') : ?>
                                <p><?php _e('Your comment is awaiting moderation.') ?></p>
                                <br/>
                            <?php endif; ?>
                            <?php comment_text() ?>
                        </div>
                    </td>
                </tr>
            </table>
         </div>
<?php
}

/*** Misc ***/

function feedcount($feedurl='http://feeds.feedburner.com/wpshower') {
    $feedid = explode('/', $feedurl);
    $feedid = end($feedid);
    $twodayago = date('Y-m-d', strtotime('-2 days', time()));
    $onedayago = date('Y-m-d', strtotime('-1 days', time()));
    $today = date('Y-m-d');

    $api = "https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=$feedid&dates=$twodayago,$onedayago";

    //Initialize a cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $api);
    $data = curl_exec($ch);
    $base_code = curl_getinfo($ch);
    curl_close($ch);

    if ($base_code['http_code']=='401'){
        $burner_count_circulation = 'This feed does not permit Awareness API access';
        $burner_date = $today;
    } else {

        $xml = new SimpleXMLElement($data); //Parse XML via SimpleXML Class
        $bis = $xml->attributes();  //Bis Contain first attribute, It usually is ok or fail in FeedBurner

        if ($bis=='ok'){
            foreach ($xml->feed as $feed) {
                if ($feed->entry[1]['circulation']=='0'){
                    $burner_count_circulation = $feed->entry[0]['circulation'];
                    $burner_date  =  $feed->entry[0]['date'];
                } else {
                    $burner_count_circulation = $feed->entry[1]['circulation'];
                    $burner_date  =  $feed->entry[1]['date'];
                }
            }
        }

        if ($bis=='fail'){
            switch ($xml->err['code']) {
                case 1:
                    $burner_count_circulation = 'Feed Not Found';
                    break;
                case 5:
                    $burner_count_circulation = 'Missing required parameter (URI)';
                    break;
                case 6:
                    $burner_count_circulation = 'Malformed parameter (DATES)';
                    break;
            }
            $burner_date = $today;
        }

    }
    if ( $bis != 'fail' && $burner_count_circulation != '' ) {
        echo '<span>'.$burner_count_circulation.' readers</span>';
    } else {
        echo '<span>'.$burner_count_circulation.'</span>';
    }
}

function twittercount($twitter_url='http://twitter.com/wpshower') {
    $twitterid = explode('/', $twitter_url);
    $twitterid = end($twitterid);
    $xml = @simplexml_load_file("http://twitter.com/users/show.xml?screen_name=$twitterid");
	echo $xml[0]->followers_count;
}

function seo_title() {
    global $page, $paged;
    $sep = " | "; # delimiter
    $newtitle = get_bloginfo('name'); # default title

    # Single & Page ##################################
    if (is_single() || is_page())
        $newtitle = single_post_title("", false);

    # Category ######################################
    if (is_category())
        $newtitle = single_cat_title("", false);

    # Tag ###########################################
    if (is_tag())
     $newtitle = single_tag_title("", false);

    # Search result ################################
    if (is_search())
     $newtitle = "Search Result " . $s;

    # Taxonomy #######################################
    if (is_tax()) {
        $curr_tax = get_taxonomy(get_query_var('taxonomy'));
        $curr_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy')); # current term data
        # if it is a term
        if (!empty($curr_term)) {
            $newtitle = $curr_tax->label . $sep . $curr_term->name;
        } else {
            $newtitle = $curr_tax->label;
        }
    }

    # Page number
    if ($paged >= 2 || $page >= 2)
            $newtitle .= $sep . sprintf('Page %s', max($paged, $page));

    # Home & Front Page ########################################
    if (is_home() || is_front_page()) {
        $newtitle = get_bloginfo('name') . $sep . get_bloginfo('description');
    } else {
        $newtitle .=  $sep . get_bloginfo('name');
    }
	return $newtitle;
}
add_filter('wp_title', 'seo_title');

function new_excerpt_length($length) {
	return 200;
}
add_filter('excerpt_length', 'new_excerpt_length');


function getTinyUrl($url) {
    $tinyurl = file_get_contents("http://tinyurl.com/api-create.php?url=".$url);
    return $tinyurl;
}

function smart_excerpt($string, $limit) {
    $words = explode(" ",$string);
    if ( count($words) >= $limit) $dots = '...';
    echo implode(" ",array_splice($words,0,$limit)).$dots;
}

function comments_link_attributes(){
    return 'class="comments_popup_link"';
}
add_filter('comments_popup_link_attributes', 'comments_link_attributes');

function next_posts_attributes(){
    return 'class="nextpostslink"';
}
add_filter('next_posts_link_attributes', 'next_posts_attributes');


/* ============================================================= */
/* rank column for admin pages: TODO: move to a plugin           */
/* ============================================================= */

function toessay_get_rank($post_ID) {
    $custom_fields = get_post_custom($post_ID);
    $ranks = $custom_fields['rank'];
    return $ranks ? $ranks[0] : NULL;
}  

function toessay_columns_head($columns) {  
    $columns['rank'] = 'Rank';  
    return $columns;
}  

function toessay_columns_content($column_name, $post_ID) {  
    if ($column_name == 'rank') {  
        $rank = toessay_get_rank($post_ID);  
        if ($rank) {  
            echo $rank;  
        }
    }  
}  

add_filter('manage_posts_columns', 'toessay_columns_head');  
add_action('manage_posts_custom_column', 'toessay_columns_content', 10, 2); 

// Register the column as sortable
function toessay_register_sortable($columns) {
    $columns['rank'] = 'rank';
    return $columns;
}
add_filter( "manage_edit-post_sortable_columns", 'toessay_register_sortable' );

function toessay_rank_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'rank' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'rank',
            'orderby' => 'meta_value_num'
        ) );
    }
    return $vars;
}
add_filter( 'request', 'toessay_rank_column_orderby' );

/* ============================================================= */
/* setup category subpage urls
/*   "/<issue-X>/YYY" -> /index.php?category_name=<issue-X>&YYY=1*/
/*                         and loads template YYY.php            */
/* ============================================================= */
$CAT_SUBPAGES = array('contents', 'about', 'contact');
function toessay_queryvars_filter( $qvars ) {
    global $CAT_SUBPAGES;
    foreach ($CAT_SUBPAGES as $p) {
        $qvars[] = $p;
    }
    return $qvars;
}
add_filter('query_vars', 'toessay_queryvars_filter' );

function toessay_contents_rewrite_rule( $rules ) {
    global $CAT_SUBPAGES;
    $newrules = array();
    foreach ($CAT_SUBPAGES as $p) {
        $newrules['([^/]*)/' . $p . '/?$'] = 'index.php?category_name=$matches[1]&' . $p . '=1';        
    }
    return $newrules + $rules;
}
add_filter( 'rewrite_rules_array','toessay_contents_rewrite_rule' );

function toessay_flush_rules(){
    $rules = get_option( 'rewrite_rules' );
    global $CAT_SUBPAGES;
    foreach ($CAT_SUBPAGES as $p) {
        if ( ! isset( $rules['([^/]*)/' . $p . '/?$'] )   ) {
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
            break;
        }
    }
}
add_action( 'init','toessay_flush_rules' );

function toessay_filter_category_template($template){
    $object = get_queried_object();
    $templates = array();
    global $CAT_SUBPAGES;
    foreach ($CAT_SUBPAGES as $p) {
        if(get_query_var($p)) {
            $templates[] = $p . '.php';
        }
    }
    
    // add more templates here if required
    return ( locate_template($templates) != false ) ? locate_template($templates) : $template;
}
add_filter('category_template', 'toessay_filter_category_template');

/* ============================================================= */
/* short name      i.e. "Barack Obama" -> "B. Obama"             */
/* ============================================================= */
function toessay_short_name($fullName) {
    $arr = explode(' ', $fullName);
    $arr[0] = $arr[0]{0} . ".";
    return implode(' ', $arr);
}

function toessay_is_too_long($author, $title) {
    $len = strlen($author) + strlen($title);
    return $len >= 24;
}

function to_essay_shorten_title($author, $title) {
    $len = strlen($author) + strlen($title);
    $aLen = 30 - strlen($author);
    if ($aLen < strLen($title)) {
        return substr($title, 0, $aLen) . "..";
    }
    return $title;
}

/* ============================================================= */
/* category meta helpers: TODO: move to a plugin                 */
/* ============================================================= */

function toessay_cats() {
    global $toessay_cats, $toessay_cats_meta;
    if (!$toessay_cats) {
        // lazy load
        $toessay_cats = get_categories( array('orderby'=>'id', 'order'=>'desc') );
        $toessay_cats_meta = array();
        foreach ($toessay_cats as $cat) { 
            $meta = get_all_terms_meta($cat->term_id);
            $toessay_cats_meta[$cat->term_id] = $meta;
        }
    }
    return $toessay_cats;
}

function toessay_cats_meta() {
    global $toessay_cats, $toessay_cats_meta;
    if (!$toessay_cats_meta) {
        toessay_cats();
    }
    return $toessay_cats_meta;
}

function toessay_get_most_recent_published_category() {
    $cats = toessay_cats();
    $cats_meta = toessay_cats_meta();
    foreach ($cats as $cat) {
        $isPublished = $cats_meta[$cat->term_id]['published'][0];
        if ($isPublished) {
            return $cat;
        }
    }
    return NULL;
}

/*
  Returns index of the specified cat_id in ordered list of cats
 */
function toessay_get_idx($id) {
    $cats = toessay_cats();
    foreach ($cats as $idx => $cat) {
        if ($cat->term_id == $id) {
            return $idx;
        }
    }
    return NULL;
}

/* ============================================================= */
/* setup category meta                                           */
/* ============================================================= */
function toessay_setup_category_meta() {
    $cat_id = get_queried_object()->term_id;
    if ($cat_id) {
        $cat = get_category($cat_id);
    } elseif ($cats = get_the_category()) {
        // get from page
        $cat = $cats[0];
    } else {
        //use  most recent
        $cat = toessay_get_most_recent_published_category();
    }

    $cats = toessay_cats();
    $cats_meta = toessay_cats_meta();
    $idx = toessay_get_idx($cat->term_id);
    $next = $cats[$idx - 1];
    $prev = $cats[$idx + 1];
    if (!$cats_meta[$next->term_id]['published'][0]) {
        $next = NULL;
    }
    if (!$cats_meta[$prev->term_id]['published'][0]) {
        $prev = NULL;
    }

    $arr = array();
    $arr['cat'] = $cat;
    $arr['cat_meta'] = $cats_meta[$cat->term_id];
    $arr['prev'] = $prev;
    $arr['next'] = $next;
    return $arr;
}

/* ============================================================= */
/* get category meta                                             */
/* ============================================================= */
function toessay_cat() {
    global $toessay_cat;
    if (!$toessay_cat) {
        // lazy load
        $toessay_cat = toessay_setup_category_meta();
    }
    return $toessay_cat;
}

function toessay_cat_id() {
    $cat = toessay_cat();
    return $cat['cat']->term_id;
}

function toessay_cat_name() {
    $cat = toessay_cat();
    return $cat['cat']->cat_name;
}

function toessay_get_url($cat) {
    return get_bloginfo('url') . "/" . $cat->slug;
}

function toessay_cat_url() {
    $cat = toessay_cat();
    return toessay_get_url($cat['cat']);
}

function toessay_cat_url_prev() {
    $cat = toessay_cat();
    $prev = $cat['prev'];
    return $prev ? toessay_get_url($prev) : NULL;
}

function toessay_cat_url_next() {
    $cat = toessay_cat();
    $next = $cat['next'];
    return $next ? toessay_get_url($next) : NULL;
}

function toessay_cat_date() {
    $cat = toessay_cat();
    return $cat['cat_meta']['date'][0];
}

function toessay_cat_image() {
    $cat = toessay_cat();
    $image = $cat['cat_meta']['image'][0];
    if (!$image) {
        return NULL;
    }
    $idx = strrpos($image, '.');
    $ext = substr($image, $idx+1);
    return substr($image, 0 , $idx) . "-154x154." . $ext;
}

function toessay_cat_published() {
    $cat = toessay_cat();
    return $cat['cat_meta']['published'][0];
}

/* ============================================================= */
/* ranked posts                                                  */
/* ============================================================= */
function setup_ranked_postdata() {
    global $ranked_posts, $N, $query_string, $wpdb, $post;

    // get ALL posts
    $posts = query_posts($query_string.'&showposts=-1');

    // get ranked posts
    $cat_id = get_queried_object()->term_id;
    $querystr = "
    SELECT * FROM $wpdb->posts
    LEFT JOIN $wpdb->postmeta ON($wpdb->posts.ID = $wpdb->postmeta.post_id)
    LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
    LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
    LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)
    WHERE $wpdb->terms.term_id = $cat_id
    AND $wpdb->term_taxonomy.taxonomy = 'category'
    AND $wpdb->posts.post_status = 'publish'
    AND $wpdb->posts.post_type = 'post'
    AND $wpdb->postmeta.meta_key = 'rank'
    ORDER BY $wpdb->postmeta.meta_value+0 ASC
    ";
    $ranked_posts = $wpdb->get_results($querystr, OBJECT);

    // push unranked posts into ranked
    foreach ($ranked_posts as $post) {
        $ids[] = $post->ID;
    }
    foreach ($posts as $post) {
        if (!in_array($post->ID, $ids)) {
            $ranked_posts[] = $post;
        }
    }
    $N = count($ranked_posts);
}

?>
