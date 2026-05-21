<?php
/**
 * Template Name: Welcome Page
 */
get_header(); 
?>

<style>
    .hero-section {
        position: relative;
        padding: 120px 0 100px;
        text-align: center;
        overflow: hidden;
    }
    .hero-bg-glow {
        position: absolute;
        top: -50%;
        left: 50%;
        transform: translateX(-50%);
        width: 800px;
        height: 800px;
        background: radial-gradient(circle, rgba(220, 38, 38, 0.15) 0%, rgba(10, 2, 2, 0) 70%);
        z-index: -1;
        pointer-events: none;
    }
    .hero-title {
        font-size: clamp(3rem, 8vw, 5rem);
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: 25px;
        letter-spacing: -1px;
    }
    .hero-subtitle {
        font-size: 1.25rem;
        color: var(--text-dim);
        max-width: 650px;
        margin: 0 auto 40px;
        line-height: 1.8;
    }
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin: 60px 0;
    }
    .feature-card {
        background: rgba(38, 15, 15, 0.4);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        transition: transform 0.3s, border-color 0.3s;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary);
        background: rgba(38, 15, 15, 0.8);
    }
    .feature-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        display: inline-block;
    }
    .app-showcase {
        background: var(--bg-panel);
        border-radius: 24px;
        border: 1px solid var(--border);
        padding: 80px 40px;
        margin: 100px 0;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden;
    }
    .app-showcase::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--accent));
    }
</style>

<main>
    <!-- HERO -->
    <section class="hero-section">
        <div class="hero-bg-glow"></div>
        <div class="nexus-container">
            <h1 class="hero-title">
                LoLLMs
            </h1>
            <p class="hero-subtitle">
                The Lord of Large Language and Multimodal Systems. Your personal, private, and uncensored gateway to the future of AI. Run it locally, connect globally. One tool to rule them all.
            </p>
            
            <?php echo do_shortcode('[lollms_download_btn]'); ?>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="nexus-container">
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3 style="font-size:1.5rem; margin-bottom:15px;">100% Private</h3>
                <p style="color:var(--text-dim);">Your data never leaves your machine. Full local execution of top-tier AI models without corporate telemetry.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3 style="font-size:1.5rem; margin-bottom:15px;">Hardware Agnostic</h3>
                <p style="color:var(--text-dim);">Seamlessly runs on NVIDIA, AMD, Apple Silicon, or just CPU. LoLLMs adapts efficiently to your rig.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3 style="font-size:1.5rem; margin-bottom:15px;">OS Ecosystem</h3>
                <p style="color:var(--text-dim);">Not just a chat box. Access integrated apps, wallets, calendars, and tools directly through the UI.</p>
            </div>
        </div>
    </section>

    <!-- USER DASHBOARD / OS CTA -->
    <section class="nexus-container">
        <div class="app-showcase">
            <?php if ( is_user_logged_in() ): $u = wp_get_current_user(); ?>
                <h2 style="font-size: 2.8rem; margin-top:0;">System Online, Operator <?php echo esc_html($u->user_login); ?></h2>
                <p style="color: var(--text-dim); font-size: 1.2rem; max-width: 600px; margin: 20px auto 40px;">Your personal instance is ready. Access the LoLLMs OS dashboard to manage tools, crypto, calendars, and your AI node.</p>
                <a href="/tools" class="btn btn-primary" style="font-size: 1.2rem; padding: 18px 50px; border-radius: 50px; font-weight: bold;">
                    🚀 Launch LoLLMs OS
                </a>
            <?php else: ?>
                <h2 style="font-size: 2.8rem; margin-top:0;">Connect to the OS</h2>
                <p style="color: var(--text-dim); font-size: 1.2rem; max-width: 600px; margin: 20px auto 40px;">Log in to access your synchronized cloud tools, apps, and securely manage your local node connection.</p>
                <a href="<?php echo wp_login_url(); ?>" class="btn btn-primary" style="font-size: 1.2rem; padding: 18px 50px; border-radius: 50px; font-weight: bold;">
                    Authenticate Identity
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- LATEST POSTS -->
    <section class="nexus-container" style="padding-bottom: 100px;">
        <div style="display:flex; justify-content:space-between; align-items:end; margin-bottom:40px; border-bottom:1px solid var(--border); padding-bottom:15px;">
            <h2 style="margin:0; font-size:2rem;">Transmission Log</h2>
            <a href="/news" style="color:var(--primary); font-weight:600; font-size: 1.1rem;">View All Transmissions →</a>
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
                            // Beautiful fallback placeholder
                            echo '<div style="width:100%; height:100%; background: linear-gradient(135deg, #260f0f, #160505); display:flex; align-items:center; justify-content:center; color:rgba(220,38,38,0.05); font-size:5rem;">◈</div>';
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
            <?php endwhile; wp_reset_postdata(); else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-dim); border: 1px dashed var(--border); border-radius: 16px;">
                    No transmissions detected in the log yet.
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>