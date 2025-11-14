<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Template_WordPress
 */

?>

<footer id="colophon" class="site-footer">
    <div class="site-info">
        <a href="<?php echo esc_url(__('https://wordpress.org/', 'template-wordpress')); ?>">
            <?php printf(esc_html__('Fièrement propulsé par %s', 'template-wordpress'), 'WordPress'); ?>
        </a>
        <span class="sep">&nbsp;|&nbsp;</span>
        <?php printf(esc_html__('Thème : %1$s par %2$s.', 'template-wordpress'), 'template-wordpress', '<a href="https://corentindiard.fr/">Corentin Diard</a>'); ?>
    </div><!-- .site-info -->
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
