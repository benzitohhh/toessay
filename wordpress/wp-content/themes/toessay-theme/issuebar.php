                <!-- issuebar -->
                <div class="issuebar">
                    <div class="holder">
                        <img src="http://placehold.it/154x154">
                        <p><?php echo toessay_cat_date(); ?>, <?php echo toessay_cat_name(); ?></p>
                        <div class="issue-nav">
                            <?php if(toessay_cat_url_prev()): ?>
                                <a class="link prev" href="<?php echo toessay_cat_url_prev(); ?>">&lt; <span>Prev</span></a>
                            <?php endif; ?>
                            <?php if(toessay_cat_url_next()): ?>
                                <a class="link next" href="<?php echo toessay_cat_url_next(); ?>"><span>Next</span> &gt;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
