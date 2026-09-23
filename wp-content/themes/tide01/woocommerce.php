<?php
get_header();

$slug   = !empty($_GET['collection']) ? sanitize_key($_GET['collection']) : '';
$active = tide01_collection_name_from_slug($slug);
$cfg    = $active ? tide01_collection_config($active) : null;
?>

<section class="shop-hero-v2 <?php echo $cfg ? 'shop-'.$cfg['class'] : 'shop-all'; ?>">
  <div class="shop-hero-copy">
    <span>SURFCRAVE / SHOP / DROP 001</span>
    <h1><?php echo $active ? esc_html($active) : 'FIND YOUR LINE.'; ?></h1>
    <p>
      <?php
      echo $cfg
        ? esc_html($cfg['desc'])
        : 'Tres universos, una misma obsesión por el mar. Elegí tu línea y después encontrá las piezas que mejor encajan con tu forma de vivir SURFCRAVE.';
      ?>
    </p>
    <div class="shop-hero-meta">
      <b><?php echo $cfg ? esc_html($cfg['mood']) : 'GERMANY FIRST'; ?></b>
      <span>TEST DROP 001</span>
      <span>MORE WAVES. LESS RULES.</span>
    </div>
  </div>

  <div class="shop-hero-visual">
    <?php if ($cfg && tide01_character_url($cfg)): ?>
      <img src="<?php echo tide01_character_url($cfg); ?>" alt="<?php echo esc_attr($active); ?>">
      <div class="shop-hero-number"><?php echo esc_html($cfg['n']); ?></div>
    <?php else: ?>
      <div class="shop-hero-brand">
        <img src="<?php echo esc_url(get_template_directory_uri().'/assets/brand/surfcrave-wordmark.webp'); ?>" alt="SURFCRAVE">
        <strong>SEA.<br>STREET.<br>FREEDOM.</strong>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="shop-collection-picker" aria-label="Colecciones SURFCRAVE">
  <?php foreach (tide01_collection_config() as $name => $c):
    $url = add_query_arg('collection', $c['slug'], wc_get_page_permalink('shop'));
    $character = tide01_character_url($c);
    $is_active = ($active === $name);
  ?>
    <a class="shop-collection-tile <?php echo esc_attr($c['class']); ?> <?php echo $is_active ? 'active' : ''; ?>"
       href="<?php echo esc_url($url); ?>">
      <div class="shop-collection-art">
        <img class="shop-collection-scene" src="<?php echo esc_url(get_template_directory_uri().'/assets/'.$c['scene']); ?>" alt="">
        <?php if ($character): ?>
          <img class="shop-collection-character" src="<?php echo $character; ?>" alt="<?php echo esc_attr($name); ?>">
        <?php endif; ?>
      </div>
      <div class="shop-collection-info">
        <small><?php echo esc_html($c['n'].' / '.$c['mood']); ?></small>
        <h2><?php echo esc_html($name); ?></h2>
        <p><?php echo esc_html($c['quote']); ?></p>
        <strong><?php echo $is_active ? 'COLECCIÓN ACTIVA' : 'ENTRAR →'; ?></strong>
      </div>
    </a>
  <?php endforeach; ?>
</section>

<nav class="collection-filter collection-filter-v2" aria-label="Filtrar por colección">
  <a class="<?php echo !$active ? 'active' : ''; ?>" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
    <span>00</span> TODO
  </a>
  <?php foreach (tide01_collection_config() as $name => $c): ?>
    <a class="<?php echo $active === $name ? 'active' : ''; ?> <?php echo esc_attr($c['class']); ?>"
       href="<?php echo esc_url(add_query_arg('collection', $c['slug'], wc_get_page_permalink('shop'))); ?>">
      <span><?php echo esc_html($c['n']); ?></span>
      <?php echo esc_html($name); ?>
    </a>
  <?php endforeach; ?>
</nav>

<section class="shop-catalog-head">
  <div>
    <span><?php echo $active ? 'CURATED DROP' : 'FULL DROP'; ?></span>
    <h2><?php echo $active ? esc_html($active) : 'SHOP THE DROP'; ?></h2>
  </div>
  <p>
    <?php echo $active
      ? 'Productos de esta línea. El catálogo sigue en fase de validación antes de comprar stock.'
      : 'Explorá todo el Test Drop. Producto, stock y precio final se validan antes de producción.'; ?>
  </p>
</section>

<section class="shop-wrap shop-wrap-v2">
  <?php
  if (is_shop() || is_product_taxonomy()) {
      $args = [
          'post_type'      => 'product',
          'post_status'    => 'publish',
          'posts_per_page' => -1,
          'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
          'order'          => 'ASC',
      ];

      if ($active) {
          $args['meta_query'] = [[
              'key'   => '_tide_collection',
              'value' => $active,
          ]];
      }

      $surfcrave_products = new WP_Query($args);

      echo '<div class="woocommerce surfcrave-product-loop">';
      if ($surfcrave_products->have_posts()) {
          woocommerce_product_loop_start();
          while ($surfcrave_products->have_posts()) {
              $surfcrave_products->the_post();
              wc_get_template_part('content', 'product');
          }
          woocommerce_product_loop_end();
      } else {
          echo '<div class="woocommerce-info">Keine Produkte in dieser Kollektion gefunden.</div>';
      }
      echo '</div>';

      wp_reset_postdata();
  } else {
      woocommerce_content();
  }
  ?>
</section>

<section class="shop-brand-close">
  <span>SURFCRAVE / TEST DROP</span>
  <h2>BUY LESS.<br>WANT IT MORE.</h2>
  <p>Validamos primero. Producimos después. Cada pieza tiene que ganarse su lugar.</p>
  <a class="btn light" href="<?php echo esc_url(home_url('/feedback/')); ?>">DAR MI OPINIÓN →</a>
</section>

<?php get_footer(); ?>
