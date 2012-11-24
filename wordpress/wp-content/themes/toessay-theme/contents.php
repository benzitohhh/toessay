<?php get_header(); ?>

<?php setup_ranked_postdata(); ?>

<div class="contents">
    
    <div class="title">
        <p class="page">Contents</p>
        <span class="issue"><?php printf(__('%s'), single_cat_title('', false)); ?></span>
    </div>

    <?php if ($ranked_posts): ?>
    <div id="loop" class="list clear">
        <ul class="postlist">
            <?php foreach ($ranked_posts as $post): ?>  
            <?php setup_postdata($post); ?>
            <li <?php post_class('post clear'); ?> id="post_<?php the_ID(); ?>">
                <span class="post-author"><?php echo toessay_short_name(get_the_author()); ?>:</span>
                <span><?php the_title(); ?></span>
                <a href="<?php the_permalink() ?>"> Read</a>                    
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
