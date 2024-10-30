<?php  
    global $post;
    $featured_img_url = get_the_post_thumbnail_url($post->ID,'full');
?>
<div class="b-post">
    <a href="<?php the_permalink(); ?>">
        <img src="<?= $featured_img_url; ?>" alt="">
    </a>
    <div class="blog-cont">
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    </div>
</div>
    