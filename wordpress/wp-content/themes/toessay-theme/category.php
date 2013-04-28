<?php get_header(); ?>

<?php setup_ranked_postdata(); ?>

<?php if ($ranked_posts): ?>

    <div id="loop" class="list clear homepage">

        <div class="hero">
            <?php $post = $ranked_posts[0]; ?>
            <?php setup_postdata($post); ?>
            <div <?php post_class('post clear'); ?> id="post_<?php the_ID(); ?>">
                <h2><?php the_title(); ?></h2>
                <div class="post-meta">
                    <span class="post-author"><?php the_author(); ?></span>
                </div>
                <div class="post-content"><?php if (function_exists('smart_excerpt')) smart_excerpt(get_the_excerpt(), 55); ?> <a href="<?php the_permalink() ?>">More</a></div>
            </div>
        </div>

        <?php if ($ranked_posts[1]): ?>
            <div class="subhero">
                <div class="row">
                    <?php for ($i = 1; $i < 3 && $i < $N; $i++): ?>
                        <?php $post = $ranked_posts[$i]; ?>
                        <?php setup_postdata($post); ?>
                        <div <?php post_class('post clear col' . ($i-1)%2 ); ?> id="post_<?php the_ID(); ?>">
                            <h2><?php the_title(); ?></h2>
                            <div class="post-meta">
                                <span class="post-author"><?php the_author(); ?></span>
                            </div>
                            <div class="post-content"><?php if (function_exists('smart_excerpt')) smart_excerpt(get_the_excerpt(), 55); ?> <a href="<?php the_permalink() ?>">More</a></div>
                        </div>
                    <?php endfor;?>
                </div>
            </div>
        <?php endif;?>

        <?php if ($ranked_posts[3]): ?>
            <div class="morelinks">
                <div class="row">
                    <div class="col col0">
                        <?php for ($i = 3; $i < $N; $i++): ?>
                            <?php $post = $ranked_posts[$i]; ?>
                            <?php setup_postdata($post); ?>
                            <div <?php post_class('post clear'); ?> id="post_<?php the_ID(); ?>">
                                <span class="post-author"><?php echo toessay_short_name(get_the_author()); ?>: </span>
                                <a href="<?php the_permalink() ?>"><?php echo to_essay_shorten_title(toessay_short_name(get_the_author()), get_the_title()); ?> </a>
                            </div>
                        <?php endfor;?>
                    </div>
                </div>
            </div>
        <?php endif;?>

    </div>

<?php endif; ?>

<?php get_footer(); ?>
