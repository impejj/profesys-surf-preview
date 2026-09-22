<?php get_header(); ?><section class="section"><h1><?php bloginfo('name'); ?></h1><?php if(have_posts()) while(have_posts()){the_post(); the_content();} ?></section><?php get_footer(); ?>
