<?php

/**
 *  https://codex.wordpress.org/Plugin_API/Action_Reference/publish_post
 */
add_action( 'publish_post', 'custom_create_sitemap' );          // add an XML version of sitemap when a post is created/updated
add_action( 'publish_page', 'custom_create_sitemap' );          // add an XML version of sitemap when a page is created/updated
add_action( 'publish_product', 'custom_create_sitemap' );       // add an XML version of sitemap when a product is created/updated
add_action( 'publish_portfolio', 'custom_create_sitemap' );     // add an XML version of sitemap when a portfolio is created/updated
add_action( 'publish_news', 'custom_create_sitemap' );          // add an XML version of sitemap when a news article is created/updated

function custom_create_sitemap() {
    $postsForSitemap = get_posts(array(
        'numberposts'   => -1,
        'orderby'       => 'modified',
        'post_type'     => array('post','page','product','portfolio','news'), // change as required
        'order'         => 'DESC',

        // exclude individual post(s) use IDs and comma seperate each
        'exclude'       => '25,45,55'
    ));

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/09/sitemap.xsd">';

    foreach($postsForSitemap as $post) {
        setup_postdata($post);

        $postdate = explode(" ", $post->post_modified);

        $sitemap .= '<url>'.
            '<loc>'. get_permalink($post->ID) .'</loc>'.
            '<lastmod>'. $postdate[0] .'</lastmod>'.
            '<changefreq>monthly</changefreq>'.
            '<priority>0.5</priority>'.
        '</url>';
    }

    $sitemap .= '</urlset>';

    $fp = fopen(ABSPATH . "sitemap.xml", 'w');
    fwrite($fp, $sitemap);
    fclose($fp);
}