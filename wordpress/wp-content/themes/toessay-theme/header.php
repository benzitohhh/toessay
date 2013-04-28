<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php bloginfo('text_direction'); ?>" xml:lang="<?php bloginfo('language'); ?>">
    <head>
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
        <title><?php wp_title ( '|', true,'right' ); ?></title>
        <meta http-equiv="Content-language" content="<?php bloginfo('language'); ?>" />

        <!-- facebook integration -->
        <meta property="fb:admins" content="653640769" />
        <?php if (is_single() || is_author()) { ?>
            <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
            <meta property="og:url" content="<?php the_permalink() ?>"/>
            <meta property="og:title" content="<?php wp_title(); ?>" />
            <meta property="og:description" content="<?php echo strip_tags(get_the_excerpt($post->ID)); ?>" />
            <meta property="og:type" content="article" />
            <meta property="og:image" content="<?php echo toessay_cat_image(); ?>" />
        <?php } else { ?>
            <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
            <meta property="og:url" content="<?php the_permalink() ?>"/>
            <meta property="og:title" content="<?php wp_title(); ?>" />
            <meta property="og:description" content="<?php bloginfo('description'); ?>" />
            <meta property="og:type" content="website" />
            <meta property="og:image" content="<?php echo toessay_cat_image(); ?>" />
        <?php } ?>

	<link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favico.ico" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
        <!--[if IE]><link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_url'); ?>/ie.css" /><![endif]-->
        <?php
			wp_enqueue_script('jquery');
			wp_enqueue_script('cycle', get_template_directory_uri() . '/js/jquery.cycle.all.min.js', 'jquery', false);
			wp_enqueue_script('cookie', get_template_directory_uri() . '/js/jquery.cookie.js', 'jquery', false);
            if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
            wp_enqueue_script('script', get_template_directory_uri() . '/js/script.js', 'jquery', false);
		?>
        <?php wp_head(); ?>
        <?php if ( is_home() && !get_option('ss_disable') ) : ?>
        <script type="text/javascript">
            (function($) {
                $(function() {
                    $('#slideshow').cycle({
                        fx:     'scrollHorz',
                        timeout: <?php echo (get_option('ss_timeout')) ? get_option('ss_timeout') : '7000' ?>,
                        next:   '#rarr',
                        prev:   '#larr'
                    });
                })
            })(jQuery)
        </script>
        <?php endif; ?>
	</head>
	<body <?php echo (get_option('bg_color')) ? 'style="background-color: '.get_option('bg_color').';"' : '' ?>>
        <div class="wrapper">

            <div class="header clear">
                <div class="logo">
                    <a href="<?php bloginfo('home'); ?>">
                        <h1>the outside essay</h1>
                    </a>
                </div>
            </div>

            <div class="nav">
                <ul id="dd" class="dd">
                    <li class="menu-item first">
                        <a href="<?php echo toessay_cat_url(); ?>/about/">About TOE</a>
                    </li>
                    <li class="menu-item">
                        <a href="<?php echo toessay_cat_url(); ?>/contents/">Contents</a>
                    </li>
                    <li class="menu-item">
                        <a href="<?php echo toessay_cat_url(); ?>/contact/">Contact</a>
                    </li>
                    <li class="menu-item last">
                    </li>
                    <li class="menu_item floatright">
                        <div class="search">
                            <form method="get" id="searchform" action="<?php echo get_bloginfo('url'); ?>">
                                <fieldset>
                                    <input name="s" type="text" onfocus="if(this.value=='Search') this.value='';" onblur="if(this.value=='') this.value='Search';" value="Search"></input>
                                    <button type="submit"></button>
                                </fieldset>
                            </form>
                        </div>
                    </li>

                </ul>
            </div>



            <!-- Container -->
            <div id="container" class="clear">
                <?php require( "issuebar.php" ); ?>

                <!-- Content -->
                <div id="content">
