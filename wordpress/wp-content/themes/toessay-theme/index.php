<?php
$cat = toessay_get_most_recent_published_category();
if ($cat) {
    // redirect
    $url = get_bloginfo('url') . "/" . $cat->slug . "/";
    header('Location: ' . $url) ;
}

get_header();
?>

<?php 
/* query_posts(array( */
/*     'post__not_in' => $exl_posts, */
/*     'paged' => $paged, */
/* )); */
?>

<?php //get_template_part('loop'); ?>

<?php //wp_reset_query(); ?>

<?php //get_template_part('pagination'); ?>

<?php get_footer(); ?>
