<?php
/**
 * The Main Template File (News Feed)
 */

get_header(); 
?>

<style>
    /* --- HEADER --- */
    .news-header {
        text-align: center;
        padding: 60px 0 40px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 40px;
        background: linear-gradient(to bottom, rgba(15, 23, 42, 0) 0%, rgba(99, 102, 241, 0.05) 100%);
    }

    /* --- SORT BAR --- */
    .sort-bar {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 30px;
    }
    .sort-btn {
        padding: 8px 20px;
        border: 1px solid var(--border);
        border-radius: 20px;
        font-size: 0.85rem;
        color: var(--text-dim);
        background: rgba(15, 23, 42, 0.6);
        transition: all 0.2s;
    }
    .sort-btn:hover, .sort-btn.active {
        border-color: var(--primary);
        background: var(--primary);
        color: white;
    }

    /* --- GRID --- */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        padding-bottom: 80px;
    }

    /* --- CARD --- */
    .news-card {
        background: rgba(30, 41, 59, 0.4);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }
    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.15);
        border-color: var(--primary);
    }
    
    /* Full Clickable Card */
    .card-link::after {
        content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1;
    }

    .card-image {
        height: 180px;
        background: #1e293b;
        overflow: hidden;
    }
    .card-image img { width: 100%; height: 100%; object-fit: cover; }

    .card-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
    .card-meta { font-size: 0.75rem; color: var(--primary); margin-bottom: 10px; text-transform: uppercase; font-weight: 700; }
    .card-title { font-size: 1.3rem; margin: 0 0 15px 0; line-height: 1.3; color: #fff; }
    .card-excerpt { font-size: 0.95rem; color: var(--text-dim); flex-grow: 1; margin-bottom: 20px; }
    
    .read-more { font-size: 0.9rem; color: var(--primary); font-weight: 600; }

    /* --- PAGINATION --- */
    .pagination { text-align: center; margin-bottom: 80px; }
    .pagination .page-numbers {
        padding: 10px 15px; border: 1px solid var(--border); margin: 0 5px; border-radius: 6px; color: var(--text-dim);
    }
    .pagination .current { background: var(--primary); border-color: var(--primary); color: white; }
</style>

<main>
    <div class="news-header">
        <div class="nexus-container">
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Transmission Log</h1>
            
            <!-- DEBUG INFO (Visible only if logged in) -->
            <?php if ( current_user_can('administrator') ) : ?>
                <div style="font-size: 0.8rem; color: #ef4444; margin-top: 10px; border: 1px dashed #ef4444; display: inline-block; padding: 5px 10px;">
                    DEBUG: Found <?php echo $wp_query->found_posts; ?> posts. 
                    Showing <?php echo $wp_query->post_count; ?> on this page.
                </div>
            <?php endif; ?>

            <div class="sort-bar">
                <?php $s = isset($_GET['sort']) ? $_GET['sort'] : 'newest'; ?>
                <a href="?sort=newest" class="sort-btn <?php echo $s === 'newest' ? 'active' : ''; ?>">Newest</a>
                <a href="?sort=oldest" class="sort-btn <?php echo $s === 'oldest' ? 'active' : ''; ?>">Oldest</a>
                <a href="?sort=alpha" class="sort-btn <?php echo $s === 'alpha' ? 'active' : ''; ?>">A-Z</a>
            </div>
        </div>
    </div>

    <div class="nexus-container">
        <?php if ( have_posts() ) : ?>
            <div class="news-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('news-card'); ?>>
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="card-image">
                                <?php the_post_thumbnail('medium_large'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="card-content">
                            <div class="card-meta">
                                <?php echo get_the_date(); ?>
                            </div>

                            <h2 class="card-title">
                                <a href="<?php the_permalink(); ?>" class="card-link">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="card-excerpt">
                                <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                            </div>

                            <div class="read-more">Read Node &rarr;</div>
                        </div>
                    </article>

                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                echo paginate_links([
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                ]);
                ?>
            </div>

        <?php else : ?>
            <div style="text-align:center; padding: 60px; border: 1px dashed var(--border);">
                <h3>No signals detected.</h3>
                <p>Check if your posts are Published.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>