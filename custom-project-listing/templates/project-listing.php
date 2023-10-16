<div id="cpl-search-container">
    <input type="text" id="cpl-search" placeholder="Buscar proyectos...">
</div>
<div id="cpl-project-list">
    <?php
    $args = array(
        'post_type' => 'project',
        'posts_per_page' => 6,
        'paged' => get_query_var('page') ? get_query_var('page') : 1,
    );

    if (isset($_POST['keyword'])) {
        $keyword = sanitize_text_field($_POST['keyword']);
        $args['s'] = $keyword;
    }

    $projects = new WP_Query($args);

    if ($projects->have_posts()) :
        while ($projects->have_posts()) : $projects->the_post();
            $project_title = get_the_title();
            $project_excerpt = get_the_excerpt();
            ?>
            <div class="project-item">
                <h3><?php echo $project_title; ?></h3>
                <p><?php echo $project_excerpt; ?></p>
            </div>
        <?php
        endwhile;

        the_posts_pagination(array(
            'prev_text' => 'Anterior',
            'next_text' => 'Siguiente',
        ));

        wp_reset_postdata();
    else :
        echo 'No se encontraron proyectos.';
    endif;
    ?>
</div>
<div id="cpl-pagination">
    <a href="#" id="cpl-pagination-prev">Anterior</a>
    <a href="#" id="cpl-pagination-next">Siguiente</a>
</div>
