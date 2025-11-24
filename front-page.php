<?php
/**
 * Template Name: Welcome Page
 */
get_header(); 
?>

<main>
    <!-- HERO -->
    <section class="hero-section">
        <div class="nexus-container">
            <h1 class="hero-title">
                One Tool to<br>
                <span style="color: var(--primary);">Orchestrate</span> Them All.
            </h1>
            <p class="hero-subtitle">
                The ultimate open-source interface for Large Language Multimodal Systems.
                <br>Private. Uncensored. Universal.
            </p>
            
            <?php echo do_shortcode('[lollms_download_btn]'); ?>
        </div>
    </section>

    <!-- USER DASHBOARD -->
    <section class="nexus-container" style="margin-bottom: 80px;">
        <div style="background: var(--bg-panel); border: 1px solid var(--border); border-radius: 16px; padding: 40px; text-align: center;">
            
            <?php if ( is_user_logged_in() ): $u = wp_get_current_user(); ?>
                <h2 style="font-size: 2rem; margin-top:0;">Welcome, Operator <?php echo esc_html($u->user_login); ?></h2>
                <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                    <button class="btn btn-primary">📅 Reminders</button>
                    <button class="btn btn-outline">🪙 Wallet</button>
                </div>
            <?php else: ?>
                <h2 style="font-size: 2rem; margin-top:0;">Join the Network</h2>
                <p style="color: var(--text-dim);">Connect your node to access cloud tools.</p>
                <div style="margin-top: 20px;">
                    <a href="<?php echo wp_login_url(); ?>" class="btn btn-primary">Connect</a>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- LATEST POSTS -->
    <section class="nexus-container" style="padding-bottom: 100px;">
        <div style="display:flex; justify-content:space-between; align-items:end; margin-bottom:30px; border-bottom:1px solid var(--border); padding-bottom:15px;">
            <h2 style="margin:0; font-size:1.8rem;">Latest Posts</h2>
            <a href="/news" style="color:var(--primary); font-weight:600;">View All →</a>
        </div>

        <div class="nexus-grid">
            <?php
            $q = new WP_Query(['posts_per_page' => 3, 'ignore_sticky_posts' => 1]);
            if ($q->have_posts()) : while ($q->have_posts()) : $q->the_post();
            ?>
                <a href="<?php the_permalink(); ?>" class="news-card">
                    <div class="card-image">
                        <?php 
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('medium_large');
                        } elseif ($img = lollms_get_first_image()) {
                            echo '<img src="' . esc_url($img) . '">';
                        } else {
                            echo '<div style="width:100%; height:100%; background:var(--bg-deep);"></div>';
                        }
                        ?>
                    </div>
                    <div class="card-content">
                        <div class="card-meta"><?php echo get_the_date(); ?></div>
                        <h3 class="card-title"><?php the_title(); ?></h3>
                        <div class="card-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                        </div>
                        <div class="read-more">Read Node →</div>
                    </div>
                </a>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>