<?php
/** @var WP_Post $post */
/** @var int $ID */
/** @var string $link_target */
/** @var string $thumbnail_size */
defined('EVENT_ESPRESSO_VERSION') || exit;
do_action('AHEE_post_thumbnail_template_before_featured_img', $post);
if (has_post_thumbnail($ID)) {
    $img_ID = get_post_thumbnail_id($ID);
    if ($img_ID) {
        $featured_img = wp_get_attachment_image_src($img_ID, $thumbnail_size);
        if ($featured_img) {
            ?>
            <div id="ee-post-thumbnail-img-dv-<?php echo $ID; ?>" class="ee-post-thumbnail-img-dv">
                <a class="ee-post-thumbnail-img-lnk"
                   href="<?php the_permalink(); ?>"<?php echo $link_target; ?>
                >
                    <img class="ee-post-thumbnail-img"
                         src="<?php echo $featured_img[0]; ?>"
                         width="<?php echo $featured_img[1]; ?>"
                         height="<?php echo $featured_img[2]; ?>"
                         alt="<?php echo esc_attr(get_post(get_post($img_ID))->post_excerpt); ?>"
                    />
                </a>
            </div>
            <?php
        }
    }
}
do_action('AHEE_post_thumbnail_template_after_featured_img', $post);

