<?php get_header(); ?>

<main class='container my-3'>
    <?php if(have_posts()){
            while(have_posts()){
                the_post();
                $taxonomy = get_the_terms(get_the_ID(), 'categoria-productos');
            ?>
                <h1 class='my-5'><?php the_title() ?></h1>
                <div class="row my-5">
                    <div class="col-md-6 col-12">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                    <div class="col-md-6 col-12">
                        <?php echo do_shortcode('[contact-form-7 id="81fa91a" title="Formulario de contacto 1"]') ?>
                    </div> 
                    <div class="col-12">
                        <?php the_content(); ?>
                    </div> 
                </div>
            <?php
                $id = get_the_ID();
                $args = array(
                    'post_type' => 'producto',
                    'posts_per_page' => 6,
                    'order' => 'ASC',
                    'orderby' => 'title',
                    'post__not_in'  => [$id],
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'categoria-productos',
                            'field' => 'slug',
                            'terms' => $taxonomy[0]->slug
                        )
                    )
                );
                $productos = new WP_Query($args);
                if ($productos->have_posts()) { ?>
                    <div class="row text-center justify-content-center productos-relacionados">
                        <div class="col-12">
                            <h3>Productos Relacionados </h3> 
                        </div>
                        <?php
                            while ($productos->have_posts()) {
                                $productos->the_post(); ?>

                                <div class="col-2 my-3 text-center">
                                    <?php
                                        the_post_thumbnail('thumbnail');
                                    ?>
                                    <h4>
                                        <a href="<?php the_permalink() ?>">
                                            <?php the_title() ?>
                                        </a>
                                    </h4>
                                </div>
                        
                        <?php
                            }
                        ?>
                    </div>
            <?php
                }
            }
    } ?>

</main>
<?php get_footer(); ?>