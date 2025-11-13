<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Template_WordPress
 */

get_header();
?>

    <main id="primary" class="site-main">

        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Oups ! Cette page est introuvable.', 'template-wordpress'); ?></h1>
            </header><!-- .page-header -->

            <div class="page-content">
                <p><?php esc_html_e('Il semble qu\'aucun résultat n\'ait été trouvé à cet endroit. Essayez peut-être l\'un des liens ci-dessous ou effectuez une recherche.', 'template-wordpress'); ?></p>

                <?php get_search_form(); ?>

            </div><!-- .page-content -->
        </section><!-- .error-404 -->

    </main><!-- #main -->

<?php
get_footer();
