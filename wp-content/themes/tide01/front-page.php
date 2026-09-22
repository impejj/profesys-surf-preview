<?php
if (!defined('ABSPATH')) exit;

get_header();
?>
<main id="primary" class="site-main surfcrave-front-page">
<?php
while (have_posts()) :
    the_post();
    the_content();
endwhile;
?>
</main>
<?php
get_footer();
