<?php
/** @var int $ID */
/** @var WP_Post $post */
/** @var string $post_thumbnail */
/** @var string $post_header */
/** @var string $wrap_class */

do_action('AHEE__espresso_post_content_template__before_post', $post);
?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('espresso-custom-post-type-details entry-content ' . $wrap_class); ?>>
        <div id="espresso-custom-post-type-header-dv-<?php echo $ID; ?>"
             class="espresso-custom-post-type-header-dv">
            <?php echo $post_thumbnail; ?>
            <?php echo $post_header; ?>
        </div>
    </article>
    <!-- #post -->
<?php
do_action('AHEE__espresso_post_content_template__after_post', $post);
