<?php 

function init_template(){

    add_theme_support('post-thumbnails');
    add_theme_support( 'title-tag');

    register_nav_menus(
        array(
            'top_menu' => 'Menú Principal'
        )
    );

}


function assets(){
    wp_register_style('bootstrap','https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css', '', '4.4.1','all');
    wp_register_style('montserrat', 'https://fonts.googleapis.com/css?family=Montserrat&display=swap','','1.0', 'all');
    wp_enqueue_style('estilos', get_stylesheet_uri(), array('bootstrap','montserrat'),'1.0', 'all');
   
    wp_register_script('popper','https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js','','1.16.0', true);
    wp_enqueue_script('boostraps', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array('jquery','popper'),'4.4.1', true);
    wp_enqueue_script('custom', get_template_directory_uri().'/assets/js/custom.js', '', '1.0', true);
    wp_localize_script('custom', 'pg', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'apiurl' => home_url('wp-json/pg/v1/')
    ));
}

add_action('wp_enqueue_scripts','assets');


function sidebar(){
    register_sidebar(
        array(
            'name' => 'Pie de página',
            'id'   => 'footer',
            'description' => 'Zona de Widgets para pie de página',
            'before_title' => '<p>',
            'after_title'  => '</p>',
            'before_widget' => '<div id="%1$s" class="%2$s">',
            'after_widget'  => '</div>',
        )
        );
}

add_action('widgets_init', 'sidebar');

function productos_type(){
    $labels = array(
        'name' => 'Productos',
        'singular_name' => 'Producto',
        'manu_name' => 'Productos',
    );

    $args = array(
        'label'  => 'Productos', 
        'description' => 'Productos de Platzi',
        'labels'       => $labels,
        'supports'   => array('title','editor','thumbnail', 'revisions'),
        'public'    => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-cart',
        'can_export' => true,
        'publicly_queryable' => true,
        'rewrite'       => true,
        'show_in_rest' => true

    );    
    register_post_type('producto', $args);
}

add_action('init', 'productos_type');

add_theme_support('post-thumbnails');

add_action('init', 'pgRegisterTax');
function pgRegisterTax() {
    $args = array (
        'hierarchical' => true,
        'labels' => array(
            'name' => 'Categorias de productos',
            'singular_name' => 'Categoria de productos'
        ),
        'show_in_nav_menu' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'categoria-productos')
        
    );
    register_taxonomy('categoria-productos',array('producto'), $args);

}

function pgFiltroProductos() {
    $args = array(
        'post_type' => 'producto',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'title',
    );

    if( $_POST['categoria'] ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'categoria-productos',
                'field' => 'slug',
                'terms' => $_POST['categoria']
            )
        );
        $productos = new WP_Query($args);
        if ($productos->have_posts()) {
            $return = array();
            while ($productos->have_posts()) {
                $productos->the_post();
                $return[] = array(
                    'imagen' => get_the_post_thumbnail(get_the_ID(), 'large'),
                    'link' => get_the_permalink(),
                    'titulo' => get_the_title()
                );
            }
            wp_send_json($return);
        }
    }
}
add_action("wp_ajax_pgFiltroProductos", "pgFiltroProductos");
add_action("wp_ajax_nopriv_pgFiltroProductos", "pgFiltroProductos");


function novedadesAPI() {
    register_rest_route(
        'pg/v1',
        '/novedades/(?P<cantidad>\d+)',
        array(
            'methods' => 'GET',
            'callback' => 'pedidoNovedades'
        )
    );
}
add_action('rest_api_init', 'novedadesAPI');

function pedidoNovedades($data) {

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $data['cantidad'],
        'order' => 'ASC',
        'orderby' => 'title',
    );
    $novedades = new WP_Query($args);

    if ($novedades->have_posts()) {
        $return = array();
        while ($novedades->have_posts()) {
            $novedades->the_post();
            $return[] = array(
                'imagen' => get_the_post_thumbnail(get_the_ID(), 'large'),
                'link' => get_the_permalink(),
                'titulo' => get_the_title()
            );
        }
        return $return;
    }

}

function pgRegisterBlock() {
    $assets = include_once get_template_directory().'/blocks/build/index.asset.php';

    wp_register_script(
        'pg-block',
        get_template_directory_uri().'/blocks/build/index.js',
        $assets['dependencies'],
        $assets['version']
    );

    register_block_type(
        'pg/basic',
        array(
            'editor_script' => 'pg-block',
            'attributes'      => array(
                'content' => array(
                    'type'    => 'string',
                    'default' => 'Hello world'
                )
            ),
            'render_callback' => 'pgRenderDinamycBlok'
        )

    );
}
add_action('init', 'pgRegisterBlock');

function pgRenderDinamycBlok($attributes, $content) {
    return "<h2>" . $attributes['content'] . "</h2>";
}