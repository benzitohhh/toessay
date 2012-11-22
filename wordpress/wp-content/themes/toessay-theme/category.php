


<?php get_header(); ?>


<?php
global $query_string, $wpdb, $post;

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

?>

<div class="content-title">
    <?php $post = $posts[0]; // Hack. Set $post so that the_date() works.
          printf(__('%s'), single_cat_title('', false)); ?>
    <a href="javascript: void(0);" id="mode"<?php if ($_COOKIE['mode'] == 'grid') echo ' class="flip"'; ?>></a>
</div>

<?php if ($ranked_posts): ?>

    <div id="loop" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'grid'; else echo 'list'; ?> clear">
    
        <div class="hero">
            <?php $post = $ranked_posts[0]; ?>
            <?php setup_postdata($post); ?>
            <div <?php post_class('post clear'); ?> id="post_<?php the_ID(); ?>">
                <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
                <div class="post-meta">
                    <span class="post-author"><a
                        href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" title="Posts by <?php the_author(); ?>"><?php the_author(); ?></a></span>
                </div>
                <div class="post-content"><?php if (function_exists('smart_excerpt')) smart_excerpt(get_the_excerpt(), 55); ?> <a href="<?php the_permalink() ?>">More</a></div>
            </div>
        </div>

        <?php if ($ranked_posts[1]): ?>
            <div class="subhero">
                <?php for ($i = 1; $i < 3 && $i < $N; $i++): ?>
                    <?php $post = $ranked_posts[$i]; ?>
                    <?php setup_postdata($post); ?>
                    <div <?php post_class('post clear'); ?> id="post_<?php the_ID(); ?>">
                        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
                        <div class="post-meta">
                            <span class="post-author"><a
                                href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" title="Posts by <?php the_author(); ?>"><?php the_author(); ?></a></span>
                        </div>
                        <div class="post-content"><?php if (function_exists('smart_excerpt')) smart_excerpt(get_the_excerpt(), 55); ?> <a href="<?php the_permalink() ?>">More</a></div>
                    </div>
                <?php endfor;?>
            </div>
        <?php endif;?>

        <?php if ($ranked_posts[3]): ?>
            <div class="morelinks">
                <div class="col1">
                    <?php for ($i = 3; $i < 7 && $i < $N; $i++): ?>
                        <?php $post = $ranked_posts[$i]; ?>
                        <?php setup_postdata($post); ?>
                        <div <?php post_class('post clear'); ?> id="post_<?php the_ID(); ?>">
                                <span class="post-author"><a
                                    href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" title="Posts by <?php the_author(); ?>"><?php the_author(); ?></a></span>
                                <a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
                                <a href="<?php the_permalink() ?>"> More</a>
                        </div>
                    <?php endfor;?>
                </div>
                <div class="col2">
                    
                </div>
            </div>
        <?php endif;?>
        
    </div>

<?php endif; ?>

<?php get_footer(); ?>
