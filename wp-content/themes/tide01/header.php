<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="SURFCRAVE — Sea. Street. Freedom. Rasta Original, Sunset Club y Black Water."><?php wp_head(); ?><link rel="icon" type="image/svg+xml" href="<?php echo esc_url(get_template_directory_uri().'/assets/social-avatar.svg'); ?>"><meta property="og:title" content="SURFCRAVE — Sea. Street. Freedom."><meta property="og:description" content="More waves. Less rules."><meta property="og:image" content="<?php echo esc_url(get_template_directory_uri().'/assets/social-cover.svg'); ?>"></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<div class="prelaunch">SURFCRAVE · PRE-LAUNCH / TEST DROP · DEUTSCHLAND FIRST</div>
<header class="site-header">
<a class="brand surfcrave-brand" href="<?php echo esc_url(home_url('/')); ?>">
<?php $brand_logo=tide01_asset_url_if_exists('brand/surfcrave-wordmark.webp'); ?>
<?php if($brand_logo): ?><img class="brand-logo-master" src="<?php echo $brand_logo; ?>" alt="SURFCRAVE — Sea. Street. Freedom."><?php else: ?><span class="brand-fallback"><b>SURFCRAVE</b><small>SEA. STREET. FREEDOM.</small></span><?php endif; ?>
</a>
<nav aria-label="Principal"><a href="<?php echo esc_url(home_url('/shop/')); ?>">Shop</a><a href="<?php echo esc_url(home_url('/collections/')); ?>">Collections</a><a href="<?php echo esc_url(home_url('/story/')); ?>">Story</a><a href="<?php echo esc_url(home_url('/feedback/')); ?>">Feedback</a></nav>
<div class="header-actions"><button id="themeToggle" class="icon-btn" aria-label="Cambiar tema">◐</button><a class="bag" href="<?php echo esc_url(wc_get_cart_url()); ?>">Bag <span><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></span></a></div>
</header><main>