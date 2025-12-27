<?php
/**
 * Template Name: App Calendar (Redirect)
 * Description: Legacy page, redirects to Nexus OS Calendar.
 */
if ( is_user_logged_in() ) {
    // Redirect logged-in users to the new Calendar App
    wp_redirect( home_url('/tools') );
    exit;
} else {
    wp_redirect( wp_login_url( home_url('/tools') ) );
    exit;
}
?>
