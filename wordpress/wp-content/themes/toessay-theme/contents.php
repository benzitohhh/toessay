<?php get_header(); ?>

<div class="content-title">
contents page yay!!!
    <?php $post = $posts[0]; // Hack. Set $post so that the_date() works.
          printf(__('%s'), single_cat_title('', false)); ?>

    <a href="javascript: void(0);" id="mode"<?php if ($_COOKIE['mode'] == 'grid') echo ' class="flip"'; ?>></a>
</div>

<?php get_template_part('loop'); ?>

<?php get_template_part('pagination'); ?>

<?php get_footer(); ?>
