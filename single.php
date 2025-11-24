<?php
/**
 * Template for Single Posts
 */
get_header(); 
?>

<main class="nexus-container" style="max-width: 800px; padding: 60px 20px;">
    <?php while ( have_posts() ) : the_post(); ?>
        
        <article>
            <header style="text-align: center; margin-bottom: 40px; border-bottom: 1px solid var(--border); padding-bottom: 30px;">
                <div style="display: inline-block; padding: 4px 12px; background: rgba(99, 102, 241, 0.1); color: var(--primary); border-radius: 20px; font-size: 0.85rem; margin-bottom: 15px; font-family: monospace;">
                    CMD: READ_NODE // <?php echo get_the_date('Y.m.d'); ?>
                </div>
                <h1 style="font-size: 2.5rem; line-height: 1.2; margin: 0;"><?php the_title(); ?></h1>
            </header>

            <!-- Article Content -->
            <div class="entry-content" style="font-size: 1.1rem; color: #cbd5e1;">
                <?php the_content(); ?>
            </div>
        </article>

        <!-- Navigation -->
        <div style="display: flex; justify-content: space-between; margin-top: 60px; padding-top: 30px; border-top: 1px solid var(--border);">
            <div><?php previous_post_link('%link', '← Previous Node'); ?></div>
            <div><?php next_post_link('%link', 'Next Node →'); ?></div>
        </div>

    <?php endwhile; ?>
</main>

<style>
    /* Make images inside articles responsive */
    .entry-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; }
    .entry-content h2 { margin-top: 40px; color: white; }
    .entry-content a { color: var(--primary); text-decoration: underline; }
</style>

<?php get_footer(); ?>