<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="nexus-header">
    <div class="nexus-container header-inner">
        
        <!-- BRANDING -->
        <div class="site-branding">
            <a href="<?php echo home_url(); ?>">
                <?php if ( has_custom_logo() ): ?>
                    <?php the_custom_logo(); ?>
                <?php else: ?>
                    <span class="text-logo">LoLLMs_Nexus</span>
                <?php endif; ?>
            </a>
        </div>

        <!-- NAVIGATION -->
        <nav class="main-nav">
            <!-- Renamed from Transmissions to Posts -->
            <a href="/news" class="nav-link">Posts</a>
            
            <a href="https://github.com/ParisNeo/lollms" target="_blank" class="nav-link">GitHub</a>
            
            <!-- USER STATUS -->
            <?php if ( is_user_logged_in() ): 
                $u = wp_get_current_user();
            ?>
                <a href="<?php echo admin_url('profile.php'); ?>" class="user-badge">
                    <img src="<?php echo get_avatar_url($u->ID); ?>" class="user-avatar" alt="">
                    <span style="font-size: 0.9rem; font-weight:600; padding-right:8px;">
                        <?php echo esc_html($u->display_name); ?>
                    </span>
                </a>
            <?php else: ?>
                <a href="<?php echo wp_login_url(); ?>" class="btn btn-outline" style="padding: 6px 16px; font-size: 0.9rem;">
                    Log In
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>