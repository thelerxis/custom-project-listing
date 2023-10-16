<?php
/*
Plugin Name: Custom Project Listing Plugin
Description: Muestra un listado de proyectos personalizados.
*/

register_activation_hook(__FILE__, 'cpl_activate_plugin');

function cpl_activate_plugin() {
    // Genera las 30 entradas de ejemplo al activar el plugin
    for ($i = 1; $i <= 30; $i++) {
        $project = array(
            'post_title' => 'Proyecto de ejemplo ' . $i,
            'post_content' => 'Descripción del proyecto ' . $i,
            'post_excerpt' => 'Extracto del proyecto ' . $i,
            'post_status' => 'publish',
            'post_type' => 'project',
        );

        wp_insert_post($project);
    }
}


function cpl_enqueue_scripts() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    wp_enqueue_script('custom-project-listing', plugin_dir_url(__FILE__) . 'js/custom-project-listing.js', array('jquery'), '1.0.0', true);

    wp_localize_script('custom-project-listing', 'cpl_ajax_params', array(
        'cpl_nonce' => wp_create_nonce('cpl-ajax-nonce'),
        'cpl_ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'cpl_enqueue_scripts');

function cpl_custom_project_listing_shortcode() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'templates/project-listing.php');
    return ob_get_clean();
}
add_shortcode('custom_project_listing', 'cpl_custom_project_listing_shortcode');

function cpl_get_projects() {
    check_ajax_referer('cpl_nonce', 'security');

    $page = isset($_POST['page']) ? absint($_POST['page']) : 1; // Página solicitada
    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : ''; // Palabra clave de búsqueda

    $args = array(
        'post_type' => 'project',
        'posts_per_page' => 6, // Número de proyectos por página
        'paged' => $page,
    );

    // Agrega la palabra clave de búsqueda a la consulta si se proporciona
    if (!empty($keyword)) {
        $args['s'] = $keyword;
    }

    $projects_query = new WP_Query($args);

    $projects = array();

    if ($projects_query->have_posts()) {
        while ($projects_query->have_posts()) {
            $projects_query->the_post();
            $project_title = get_the_title();
            $project_excerpt = get_the_excerpt();

            $project_data = array(
                'title' => $project_title,
                'excerpt' => $project_excerpt,
            );

            $projects[] = $project_data;
        }
    }

    wp_reset_postdata();

    wp_send_json($projects);
}

add_action('wp_ajax_cpl_get_projects', 'cpl_get_projects');
add_action('wp_ajax_nopriv_cpl_get_projects', 'cpl_get_projects');