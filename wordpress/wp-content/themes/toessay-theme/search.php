<?php get_header(); ?>

<div class="search-page">
    <?php if ( have_posts() ) : ?>
        <div class="title">
            <p class="page">Search results:</p>
            <span class="issue">"<?php the_search_query(); ?>"</span>
        </div>
        <div id="loop" class="list clear">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="hero">
                    <div <?php post_class('post clear'); ?> id="post_<?php the_ID(); ?>">
                        <h2><?php the_title(); ?></h2>
                        <div class="post-meta">
                            <span class="post-author"><?php the_author(); ?></span>
                        </div>
                        <div class="post-content"><?php if (function_exists('smart_excerpt')) smart_excerpt(get_the_excerpt(), 55); ?> <a href="<?php the_permalink() ?>">More</a></div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div class="content-title">
            Your search <strong><?php the_search_query(); ?></strong> did not match any documents
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
