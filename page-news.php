<?php
/**
 * Template Name: Custom News Feed
 * Description: Forces the display of blog posts on the News page.
 */

get_header(); 

// --- CUSTOM QUERY ---
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
if ( get_query_var('page') ) $paged = get_query_var('page');

$args = [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'ignore_sticky_posts' => 1
];

// Handle Sorting
if ( isset( $_GET['sort'] ) ) {
    switch ( $_GET['sort'] ) {
        case 'oldest': $args['order'] = 'ASC'; $args['orderby'] = 'date'; break;
        case 'alpha':  $args['orderby'] = 'title'; $args['order'] = 'ASC'; break;
        default:       $args['order'] = 'DESC'; $args['orderby'] = 'date';
    }
}

$news_query = new WP_Query( $args );
?>

<main class="nexus-container" style="padding-bottom: 80px;">
    
    <!-- HEADER -->
    <div style="text-align:center; padding: 60px 0 40px; margin-bottom:40px; border-bottom:1px solid var(--border);">
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Posts</h1>
        
        <!-- SORT BUTTONS -->
        <div style="display:flex; justify-content:center; gap:10px; margin-top:30px;">
            <?php $s = $_GET['sort'] ?? 'newest'; ?>
            <a href="?sort=newest" class="btn btn-outline <?php echo $s === 'newest' ? 'btn-primary' : ''; ?>" style="padding:6px 15px; font-size:0.85rem;">Newest</a>
            <a href="?sort=oldest" class="btn btn-outline <?php echo $s === 'oldest' ? 'btn-primary' : ''; ?>" style="padding:6px 15px; font-size:0.85rem;">Oldest</a>
            <a href="?sort=alpha"  class="btn btn-outline <?php echo $s === 'alpha' ? 'btn-primary' : ''; ?>" style="padding:6px 15px; font-size:0.85rem;">A-Z</a>
        </div>
    </div>

    <!-- GRID -->
    <?php if ( $news_query->have_posts() ) : ?>
        <div class="nexus-grid">
            <?php while ( $news_query->have_posts() ) : $news_query->the_post(); ?>
                
                <a href="<?php the_permalink(); ?>" class="news-card">
                    <div class="card-image">
                        <?php 
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail('medium_large');
                        } elseif ( $img_url = lollms_get_first_image() ) {
                            echo '<img src="' . esc_url($img_url) . '" alt="' . get_the_title() . '">';
                        } else {
                            echo '<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.03); color:rgba(255,255,255,0.1); font-size:3rem;">◈</div>';
                        }
                        ?>
                    </div>

                    <div class="card-content">
                        <div class="card-meta"><?php echo get_the_date(); ?></div>
                        <h2 class="card-title"><?php the_title(); ?></h2>
                        <div class="card-excerpt">
                            <?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
                        </div>
                        <div class="read-more">Read Node &rarr;</div>
                    </div>
                </a>

            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <!-- PAGINATION -->
        <div style="text-align: center; margin-top: 60px;">
            <?php
            echo paginate_links([
                'total' => $news_query->max_num_pages,
                'current' => $paged,
                'prev_text' => '←',
                'next_text' => '→',
                'type' => 'list',
                'format' => '?paged=%#%'
            ]);
            ?>
        </div>

    <?php else : ?>
        <div style="text-align:center; padding: 60px; color: var(--text-dim);">
            <h3>No posts found.</h3>
        </div>
    <?php endif; ?>

</main>

<style>
    /* Pagination Styles Fix */
    ul.page-numbers { display:flex; justify-content:center; gap:10px; list-style:none; padding:0; }
    .page-numbers { padding: 8px 14px; border: 1px solid var(--border); border-radius: 6px; color: var(--text-dim); }
    .page-numbers.current { background: var(--primary); border-color: var(--primary); color: white; }
    .page-numbers:hover:not(.current) { border-color: var(--text-main); color: var(--text-main); }
</style>

<?php get_footer(); ?>