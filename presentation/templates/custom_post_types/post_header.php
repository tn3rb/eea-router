<?php
/** @var int $ID */
/** @var string $wrap_class */
?>
<header class="ee-post-header<?php echo $wrap_class; ?>">
    <h1 id="ee-post-details-h1" class="entry-title">
        <a class="" href="<?php the_permalink($ID); ?>"><?php the_title(); ?></a>
    </h1>
    <?php if (has_excerpt($ID)) {
        the_excerpt();
    } ?>
</header>
