                <!-- issuebar -->
                <div class="issuebar">

                    <div class="img-and-title">
                        <!-- <a href="<?php echo toessay_cat_url(); ?>/"><img src="http://placehold.it/176x176"></a> -->
                        <a href="<?php echo toessay_cat_url(); ?>/"><img src="<?php echo toessay_cat_image(); ?>"/></a>
                        <p><?php echo toessay_cat_date(); ?>, <?php echo toessay_cat_name(); ?></p>
                        <div class="issue-nav">
                            <?php if(toessay_cat_url_prev()): ?>
                                <a class="link prev" href="<?php echo toessay_cat_url_prev(); ?>">&lt;<span></span></a>
                            <?php endif; ?>
                            <?php if(toessay_cat_url_next()): ?>
                                <a class="link next" href="<?php echo toessay_cat_url_next(); ?>">&gt;<span></span></a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="twitter-link">
                        <a href="http://twitter.com/theoutsideessay"><img src="<?php bloginfo('template_directory'); ?>/images/twitter-bird-blue-laura-obi.png" alt="" /></a>
                    </div>

                </div>
