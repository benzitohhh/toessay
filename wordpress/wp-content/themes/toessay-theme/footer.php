            </div>
            <!-- /Content -->

            <?php get_sidebar(); ?>

            </div>
            <!-- /Container -->

            <div class="footer">
                <p class="copyright">&copy; <?php echo date("Y"); ?> <a href="<?php bloginfo('home'); ?>"><?php bloginfo('name'); ?></a>. All Rights Reserved.<br /></p>
            </div>
        </div>
        <!-- Page generated: <?php timer_stop(1); ?> s, <?php echo get_num_queries(); ?> queries -->
        <?php wp_footer(); ?>

        <?php echo (get_option('ga')) ? get_option('ga') : '' ?>

	</body>
</html>
