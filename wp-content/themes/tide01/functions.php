<?php
if (!defined('ABSPATH')) exit;
function tide01_setup(){add_theme_support('title-tag');add_theme_support('woocommerce');add_theme_support('html5',['search-form','gallery','caption','style','script']);register_nav_menus(['primary'=>'Primary']);}
add_action('after_setup_theme','tide01_setup');
function tide01_assets(){wp_enqueue_style('tide01-main',get_template_directory_uri().'/assets/main.css',[],filemtime(get_template_directory().'/assets/main.css'));wp_enqueue_script('tide01-main',get_template_directory_uri().'/assets/main.js',[],filemtime(get_template_directory().'/assets/main.js'),true);}
add_action('wp_enqueue_scripts','tide01_assets');
function tide01_mark(){return '<svg viewBox="0 0 64 40" aria-hidden="true"><path d="M2 29c12 2 17-20 31-18 9 1 12 10 19 11 4 1 7-1 10-4-2 11-12 20-28 20C18 38 8 35 2 29Z" fill="currentColor"/><path d="M22 21c6-8 14-7 19 0-7-3-13 0-19 0Z" fill="#F26722"/></svg>';}
function tide01_collection_config($key=null){$all=[
'RASTA ORIGINAL'=>['slug'=>'rasta-original','n'=>'01','mood'=>'Salidas de playa','desc'=>'Raíces, actitud y libertad. La línea más orgánica de SURFCRAVE: arena, sal, after-surf y el surfer con rastas que dio origen al universo visual.','class'=>'rasta','scene'=>'scene-rasta.svg','character'=>'characters/rasta-original.webp','quote'=>'RAÍCES · ACTITUD · SIEMPRE'],
'SUNSET CLUB'=>['slug'=>'sunset-club','n'=>'02','mood'=>'Todo el día','desc'=>'Sol, amigos y libertad. Una línea joven y luminosa que acompaña del mar a la ciudad con su surfer rubio como personaje original.','class'=>'sunset','scene'=>'scene-sunset.svg','character'=>'characters/sunset-club.webp','quote'=>'SOL · AMIGOS · LIBERTAD'],
'BLACK WATER'=>['slug'=>'black-water','n'=>'03','mood'=>'Salidas nocturnas','desc'=>'Profundidad, misterio y exploración. El lado nocturno de SURFCRAVE: negro, aqua eléctrico, luna y su surfer original after-dark.','class'=>'black','scene'=>'scene-black.svg','character'=>'characters/black-water.webp','quote'=>'NIGHT · SALT · STREET']];return $key?($all[$key]??null):$all;}
function tide01_collection_name_from_slug($slug){foreach(tide01_collection_config() as $name=>$c)if($c['slug']===$slug)return $name;return null;}
function tide01_asset_url_if_exists($relative){$path=get_template_directory().'/assets/'.$relative;return file_exists($path)?esc_url(get_template_directory_uri().'/assets/'.$relative):null;}
function tide01_character_url($c){return empty($c['character'])?null:tide01_asset_url_if_exists($c['character']);}
function tide01_collection_card($name,$large=false){$c=tide01_collection_config($name);if(!$c)return;$url=add_query_arg('collection',$c['slug'],wc_get_page_permalink('shop'));$base=get_template_directory_uri().'/assets/';$character=tide01_character_url($c);$scene=esc_url($base.$c['scene']);$character_html=$character?'<img class="collection-character" src="'.$character.'" alt="'.esc_attr($name).'" loading="lazy">':'';echo '<a class="collection-card-v2 '.$c['class'].($large?' large':'').'" href="'.esc_url($url).'"><div class="collection-art"><img class="collection-scene" src="'.$scene.'" alt="" loading="lazy"><div class="collection-glow"></div>'.$character_html.'<div class="collection-lines"></div></div><div class="collection-copy"><small>'.$c['n'].' / '.esc_html($c['mood']).'</small><h3>'.esc_html($name).'</h3><p>'.esc_html($c['desc']).'</p><strong>'.esc_html($c['quote']).' · VER COLECCIÓN →</strong></div></a>';}
function tide01_product_visual($product=null){if(!$product)$product=wc_get_product(get_the_ID());if(!$product)return;$name=get_post_meta($product->get_id(),'_tide_collection',true)?:'SUNSET CLUB';$c=tide01_collection_config($name)?:tide01_collection_config('SUNSET CLUB');$image_id=$product->get_image_id();if($image_id){$size=(function_exists('is_product')&&is_product())?'woocommerce_single':'woocommerce_thumbnail';echo '<div class="tide-product-art tide-product-photo collection-'.$c['class'].'">'.wp_get_attachment_image($image_id,$size,false,['class'=>'tide-product-image','loading'=>'lazy','alt'=>$product->get_name()]).'</div>';return;}$sub=get_post_meta($product->get_id(),'_tide_substyle',true);$character=tide01_character_url($c);$character_html=$character?'<img class="tide-character" src="'.$character.'" alt="">':'';echo '<div class="tide-product-art collection-'.$c['class'].'" aria-hidden="true"><div class="tide-atmosphere"></div>'.$character_html.'<div class="tide-board"><span>'.esc_html($sub?:$name).'</span></div><div class="tide-stamp">'.esc_html($c['n']).'<br>'.esc_html($c['mood']).'</div></div>';}
function tide01_collection_products($name,$limit=4){$q=new WP_Query(['post_type'=>'product','posts_per_page'=>$limit,'post_status'=>'publish','meta_key'=>'_tide_collection','meta_value'=>$name]);if(!$q->have_posts())return;echo '<div class="product-grid collection-products">';while($q->have_posts()){$q->the_post();$p=wc_get_product(get_the_ID());echo '<article class="product-card"><a href="'.esc_url(get_permalink()).'">';tide01_product_visual($p);echo '<div class="product-meta"><div><span>'.esc_html(get_post_meta(get_the_ID(),'_tide_mood',true)).'</span><h3>'.esc_html(get_the_title()).'</h3></div><strong>'.$p->get_price_html().'</strong></div></a></article>';}wp_reset_postdata();echo '</div>';}
add_action('init',function(){if(class_exists('WooCommerce')){remove_action('woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_thumbnail',10);add_action('woocommerce_before_shop_loop_item_title','tide01_product_visual',10);remove_action('woocommerce_before_single_product_summary','woocommerce_show_product_images',20);add_action('woocommerce_before_single_product_summary',function(){tide01_product_visual(wc_get_product(get_the_ID()));},20);}});
add_filter('woocommerce_product_query_meta_query',function($mq){if(!empty($_GET['collection'])){$name=tide01_collection_name_from_slug(sanitize_key($_GET['collection']));if($name)$mq[]=['key'=>'_tide_collection','value'=>$name];}return $mq;});
add_filter('woocommerce_add_to_cart_text',fn()=>'Quiero este');add_filter('woocommerce_product_single_add_to_cart_text',fn()=>'Quiero este producto');
add_action('woocommerce_single_product_summary',function(){global $product;if(!$product)return;$name=get_post_meta($product->get_id(),'_tide_collection',true);$mood=get_post_meta($product->get_id(),'_tide_mood',true);if($name)echo '<div class="product-collection-note"><strong>SURFCRAVE · '.esc_html($name).'</strong><span>'.esc_html($mood).'</span></div>';},6);


/* SURFCRAVE catalog media migration — 2026-09-22 */
add_action('init', function () {
    if (!class_exists('WooCommerce') || get_option('surfcrave_catalog_media_migration_20260922_v1')) return;

    $map = [
        'SC-SC-001' => [84, 38, 'SUNSET CLUB', 'Todo el día'],
        'SC-SC-002' => [83, 34, 'SUNSET CLUB', 'Todo el día'],
        'SC-SC-003' => [81, 62, 'SUNSET CLUB', 'Todo el día'],
        'SC-SC-004' => [82, 44, 'SUNSET CLUB', 'Todo el día'],
        'SC-SC-005' => [80, 48, 'SUNSET CLUB', 'Todo el día'],
        'SC-SC-006' => [79, 28, 'SUNSET CLUB', 'Todo el día'],
        'SC-SC-007' => [78, 55, 'SUNSET CLUB', 'Todo el día'],
        'SC-SC-008' => [77, 22, 'SUNSET CLUB', 'Todo el día'],
        'SC-SC-009' => [76, 19, 'SUNSET CLUB', 'Todo el día'],
        'SC-SC-010' => [75, 12, 'SUNSET CLUB', 'Todo el día'],
        'SC-BW-001' => [69, 39, 'BLACK WATER', 'Salidas nocturnas'],
        'SC-BW-002' => [68, 58, 'BLACK WATER', 'Salidas nocturnas'],
        'SC-BW-003' => [70, 74, 'BLACK WATER', 'Salidas nocturnas'],
        'SC-BW-004' => [63, 56, 'BLACK WATER', 'Salidas nocturnas'],
        'SC-BW-005' => [67, 89, 'BLACK WATER', 'Salidas nocturnas'],
        'SC-BW-006' => [66, 39, 'BLACK WATER', 'Salidas nocturnas'],
        'SC-BW-007' => [62, 29, 'BLACK WATER', 'Salidas nocturnas'],
        'SC-BW-008' => [61, 36, 'BLACK WATER', 'Salidas nocturnas'],
        'SC-BW-009' => [65, 44, 'BLACK WATER', 'Salidas nocturnas'],
        'SC-BW-010' => [64, 24, 'BLACK WATER', 'Salidas nocturnas'],
    ];

    $report = ['updated' => [], 'missing_sku' => [], 'missing_media' => []];
    foreach ($map as $sku => [$attachment_id, $price, $collection, $mood]) {
        $product_id = wc_get_product_id_by_sku($sku);
        if (!$product_id) { $report['missing_sku'][] = $sku; continue; }
        if (get_post_type($attachment_id) !== 'attachment') { $report['missing_media'][] = $sku; continue; }
        $product = wc_get_product($product_id);
        if (!$product) { $report['missing_sku'][] = $sku; continue; }
        $product->set_image_id($attachment_id);
        $product->set_regular_price((string) $price);
        $product->set_price((string) $price);
        $product->save();
        update_post_meta($product_id, '_tide_collection', $collection);
        update_post_meta($product_id, '_tide_mood', $mood);
        $report['updated'][] = $sku;
    }
    update_option('woocommerce_currency', 'EUR');
    $report['currency'] = get_option('woocommerce_currency');
    $report['ran_at'] = current_time('mysql');
    update_option('surfcrave_catalog_media_migration_20260922_v1', $report, false);
}, 40);


/* SURFCRAVE Rasta media migration — 2026-09-22 */
add_action('init', function () {
    if (!class_exists('WooCommerce') || get_option('surfcrave_rasta_media_migration_20260922_v1')) return;

    $map = [
        'SC-RO-001' => [93, 36],
        'SC-RO-002' => [94, 32],
        'SC-RO-003' => [92, 68],
        'SC-RO-004' => [91, 49],
        'SC-RO-005' => [0, 42],
        'SC-RO-006' => [90, 29],
        'SC-RO-007' => [89, 27],
        'SC-RO-008' => [85, 34],
        'SC-RO-009' => [87, 18],
        'SC-RO-010' => [86, 14],
    ];

    $report = ['updated' => [], 'missing_sku' => [], 'missing_media' => []];
    foreach ($map as $sku => [$attachment_id, $price]) {
        $product_id = wc_get_product_id_by_sku($sku);
        if (!$product_id) { $report['missing_sku'][] = $sku; continue; }
        $product = wc_get_product($product_id);
        if (!$product) { $report['missing_sku'][] = $sku; continue; }
        if ($attachment_id) {
            if (get_post_type($attachment_id) === 'attachment') $product->set_image_id($attachment_id);
            else $report['missing_media'][] = $sku;
        }
        $product->set_regular_price((string) $price);
        $product->set_price((string) $price);
        $product->save();
        update_post_meta($product_id, '_tide_collection', 'RASTA ORIGINAL');
        update_post_meta($product_id, '_tide_mood', 'Salidas de playa');
        $report['updated'][] = $sku;
    }
    $report['ran_at'] = current_time('mysql');
    update_option('surfcrave_rasta_media_migration_20260922_v1', $report, false);
}, 41);


/* SURFCRAVE Germanized controlled install — 2026-09-22 */
add_action('init', function () {
    if (!current_user_can('manage_options') || get_option('surfcrave_germanized_install_20260922_v1')) return;

    $snapshot_keys = [
        'woocommerce_currency','woocommerce_default_country','woocommerce_allowed_countries',
        'woocommerce_all_except_countries','woocommerce_specific_allowed_countries','woocommerce_ship_to_countries',
        'woocommerce_specific_ship_to_countries','woocommerce_calc_taxes','woocommerce_prices_include_tax',
        'woocommerce_tax_based_on','woocommerce_shop_page_id','woocommerce_cart_page_id',
        'woocommerce_checkout_page_id','woocommerce_myaccount_page_id'
    ];
    $snapshot = [];
    foreach ($snapshot_keys as $key) $snapshot[$key] = get_option($key, null);
    update_option('surfcrave_pre_germanized_snapshot_20260922', $snapshot, false);

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    $report = ['started_at'=>current_time('mysql'),'installed'=>false,'activated'=>false,'plugin'=>''];
    $plugins = get_plugins();
    $plugin_file = '';
    foreach (array_keys($plugins) as $file) {
        if (strpos($file, 'woocommerce-germanized/') === 0) { $plugin_file = $file; break; }
    }

    if (!$plugin_file) {
        $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
        $result = $upgrader->install('https://downloads.wordpress.org/plugin/woocommerce-germanized.latest-stable.zip');
        $report['install_result'] = is_wp_error($result) ? $result->get_error_message() : (bool)$result;
        wp_clean_plugins_cache(true);
        $plugins = get_plugins();
        foreach (array_keys($plugins) as $file) {
            if (strpos($file, 'woocommerce-germanized/') === 0) { $plugin_file = $file; break; }
        }
    }

    if ($plugin_file) {
        $report['installed'] = true;
        $report['plugin'] = $plugin_file;
        if (!is_plugin_active($plugin_file)) {
            $activation = activate_plugin($plugin_file);
            $report['activation_result'] = is_wp_error($activation) ? $activation->get_error_message() : 'ok';
        }
        $report['activated'] = is_plugin_active($plugin_file);
    }
    $report['finished_at'] = current_time('mysql');
    update_option('surfcrave_germanized_install_20260922_v1', $report, false);
}, 5);


/* SURFCRAVE German storefront locale — 2026-09-22 */
add_action('init', function () {
    if (get_option('surfcrave_de_locale_20260922_v2')) return;
    update_option('WPLANG', 'de_DE');
    update_user_meta(1, 'locale', 'es_ES');
    update_option('surfcrave_de_locale_20260922_v2', ['after'=>get_option('WPLANG',''),'ran_at'=>current_time('mysql')], false);
}, 6);

/* SURFCRAVE legacy commerce redirects — 2026-09-22 */
add_action('template_redirect', function () {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    $map = ['cart'=>'warenkorb','checkout'=>'kasse','my-account'=>'mein-konto'];
    if (isset($map[$path])) {
        wp_safe_redirect(home_url('/'.$map[$path].'/'), 301);
        exit;
    }
});


/* SURFCRAVE Polylang stable normalization — 2026-09-22 */
add_action('init', function () {
    if (!current_user_can('manage_options') || get_option('surfcrave_polylang_stable_20260922_v1')) return;
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    $report=['started_at'=>current_time('mysql')];
    $plugin='polylang/polylang.php';
    $plugins=get_plugins();
    $version=isset($plugins[$plugin]['Version']) ? $plugins[$plugin]['Version'] : '';
    $report['before']=$version;
    if ($version && $version !== '3.8.9') {
        if (is_plugin_active($plugin)) deactivate_plugins($plugin, true);
        $deleted=delete_plugins([$plugin]);
        $report['deleted']=is_wp_error($deleted)?$deleted->get_error_message():$deleted;
        $upgrader=new Plugin_Upgrader(new Automatic_Upgrader_Skin());
        $installed=$upgrader->install('https://downloads.wordpress.org/plugin/polylang.3.8.9.zip');
        $report['installed']=is_wp_error($installed)?$installed->get_error_message():(bool)$installed;
        wp_clean_plugins_cache(true);
    }
    $plugins=get_plugins();
    $report['after']=isset($plugins[$plugin]['Version']) ? $plugins[$plugin]['Version'] : '';
    $report['active']=is_plugin_active($plugin);
    $report['finished_at']=current_time('mysql');
    update_option('surfcrave_polylang_stable_20260922_v1',$report,false);
}, 7);


/* SURFCRAVE async German language pack — 2026-09-22 */
add_action('surfcrave_install_de_language_pack', function () {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/translation-install.php';
    $report=['started_at'=>current_time('mysql')];
    $result=function_exists('wp_download_language_pack') ? wp_download_language_pack('de_DE') : new WP_Error('missing_function','wp_download_language_pack unavailable');
    $report['download']=is_wp_error($result)?$result->get_error_message():$result;
    if (!is_wp_error($result) && $result) update_option('WPLANG','de_DE');
    $report['locale_option']=get_option('WPLANG','');
    $report['available']=get_available_languages();
    $report['finished_at']=current_time('mysql');
    update_option('surfcrave_de_language_pack_result_20260922_v3',$report,false);
}, 10);
add_action('init', function () {
    if (get_option('surfcrave_de_language_pack_scheduled_20260922_v3')) return;
    if (function_exists('as_enqueue_async_action')) {
        as_enqueue_async_action('surfcrave_install_de_language_pack', [], 'surfcrave');
        update_option('surfcrave_de_language_pack_scheduled_20260922_v3','action-scheduler',false);
    } else {
        wp_schedule_single_event(time()+5,'surfcrave_install_de_language_pack');
        update_option('surfcrave_de_language_pack_scheduled_20260922_v3','wp-cron',false);
    }
}, 99);


/* SURFCRAVE shop diagnostic — 2026-09-23 */
add_action('init', function () {
    if (!class_exists('WooCommerce') || get_option('surfcrave_shop_diag_20260923_v1')) return;

    $report = [];
    $counts = wp_count_posts('product');
    $report['post_counts'] = [
        'publish' => isset($counts->publish) ? (int)$counts->publish : 0,
        'draft'   => isset($counts->draft) ? (int)$counts->draft : 0,
        'private' => isset($counts->private) ? (int)$counts->private : 0,
    ];
    $report['shop_page_id'] = (int)get_option('woocommerce_shop_page_id');
    $report['shop_permalink'] = wc_get_page_permalink('shop');
    $report['hide_out_of_stock'] = get_option('woocommerce_hide_out_of_stock_items');

    $all = wc_get_products(['status'=>'publish','limit'=>-1,'return'=>'objects']);
    $report['wc_publish_total'] = count($all);
    $report['catalog_visibility'] = ['visible'=>0,'catalog'=>0,'search'=>0,'hidden'=>0,'other'=>0];
    $report['stock'] = ['instock'=>0,'outofstock'=>0,'onbackorder'=>0,'other'=>0];
    $report['surfcrave'] = [];

    foreach ($all as $p) {
        $vis = $p->get_catalog_visibility();
        if (isset($report['catalog_visibility'][$vis])) $report['catalog_visibility'][$vis]++; else $report['catalog_visibility']['other']++;
        $stock = $p->get_stock_status();
        if (isset($report['stock'][$stock])) $report['stock'][$stock]++; else $report['stock']['other']++;
        if (strpos((string)$p->get_sku(), 'SC-') === 0) {
            $report['surfcrave'][] = [
                'id'=>$p->get_id(),'sku'=>$p->get_sku(),'status'=>$p->get_status(),
                'visibility'=>$vis,'stock'=>$stock,'price'=>$p->get_price(),
                'collection'=>get_post_meta($p->get_id(),'_tide_collection',true)
            ];
        }
    }

    $q = new WP_Query(['post_type'=>'product','post_status'=>'publish','posts_per_page'=>100,'fields'=>'ids']);
    $report['raw_wp_query_count'] = (int)$q->found_posts;
    $report['raw_wp_query_ids'] = array_map('intval',$q->posts);

    $visibility = wc_get_product_visibility_term_ids();
    $report['visibility_term_ids'] = $visibility;
    $report['ran_at'] = current_time('mysql');

    $body = '<pre>'.esc_html(wp_json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)).'</pre>';
    $existing = get_page_by_path('surfcrave-shop-diagnostic', OBJECT, 'page');
    $postarr = ['post_type'=>'page','post_status'=>'draft','post_title'=>'SURFCRAVE SHOP DIAGNOSTIC','post_name'=>'surfcrave-shop-diagnostic','post_content'=>$body];
    if ($existing) { $postarr['ID']=$existing->ID; wp_update_post($postarr); } else { wp_insert_post($postarr); }
    update_option('surfcrave_shop_diag_20260923_v1', $report, false);
}, 90);


/* SURFCRAVE shop rendered self-check — 2026-09-23 */
add_action('init', function () {
    if (isset($_GET['surfcrave_probe']) || get_option('surfcrave_shop_selfcheck_20260923_v1')) return;
    update_option('surfcrave_shop_selfcheck_20260923_v1', ['state'=>'running'], false);

    $url = add_query_arg('surfcrave_probe', '1', wc_get_page_permalink('shop'));
    $res = wp_remote_get($url, ['timeout'=>20, 'redirection'=>3, 'sslverify'=>true]);
    $report = ['url'=>$url, 'ran_at'=>current_time('mysql')];
    if (is_wp_error($res)) {
        $report['error']=$res->get_error_message();
    } else {
        $body = wp_remote_retrieve_body($res);
        $report['http_code'] = (int)wp_remote_retrieve_response_code($res);
        $report['body_length'] = strlen($body);
        $report['surfcrave_loop_marker'] = substr_count($body, 'surfcrave-product-loop');
        $report['product_li_count'] = preg_match_all('/<li[^>]+class=["\'][^"\']*product[^"\']*["\']/i', $body, $m);
        $report['tide_product_art_count'] = substr_count($body, 'tide-product-art');
        $report['no_products_message'] = (strpos($body, 'Keine Produkte in dieser Kollektion gefunden') !== false);
        $report['sample_skus_present'] = [
            'SC-RO-001' => strpos($body, 'SC-RO-001') !== false,
            'SC-SC-001' => strpos($body, 'SC-SC-001') !== false,
            'SC-BW-001' => strpos($body, 'SC-BW-001') !== false,
        ];
    }
    update_option('surfcrave_shop_selfcheck_20260923_v1', $report, false);
    $existing = get_page_by_path('surfcrave-shop-self-check', OBJECT, 'page');
    $postarr = [
        'post_type'=>'page','post_status'=>'draft','post_title'=>'SURFCRAVE SHOP SELF CHECK',
        'post_name'=>'surfcrave-shop-self-check',
        'post_content'=>'<pre>'.esc_html(wp_json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)).'</pre>'
    ];
    if ($existing) { $postarr['ID']=$existing->ID; wp_update_post($postarr); } else { wp_insert_post($postarr); }
}, 98);
