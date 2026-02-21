<!-- APP: LAUNCHER -->
<div id="launcher-view">
    <div class="launcher-header">
        <h1>LoLLMs OS</h1>
        <p>Select system module</p>
    </div>
    <div class="app-launcher-grid">
        <?php
        // Define the apps, their labels, and default fallback icons/classes
        $launcher_apps = [
            'chat'      => ['label' => 'Chat',      'icon' => '💬', 'class' => 'icon-chat'],
            'grocery'   => ['label' => 'Groceries', 'icon' => '🛒', 'class' => 'icon-grocery'],
            'cards'     => ['label' => 'Cards',     'icon' => '💳', 'class' => 'icon-wallet'],
            'coupons'   => ['label' => 'Coupons',   'icon' => '🎟️', 'class' => 'icon-coupons'],
            'calendar'  => ['label' => 'Calendar',  'icon' => '📅', 'class' => 'icon-cal'],
            'qr'        => ['label' => 'QR Tools',  'icon' => '📷', 'class' => 'icon-qr'],
            'crypto'    => ['label' => 'Wallet',    'icon' => '🪙', 'class' => 'icon-crypto'],
            'converter' => ['label' => 'Converter', 'icon' => '⇄',  'class' => 'icon-conv'],
        ];

        foreach ($launcher_apps as $app_id => $app_data) {
            // Check for icon.png in the app folder
            $icon_path = get_theme_file_path("apps/{$app_id}/icon.png");
            $icon_uri  = get_theme_file_uri("apps/{$app_id}/icon.png");
            
            echo '<div class="app-icon" onclick="openApp(\'' . $app_id . '\')">';
            
            if (file_exists($icon_path)) {
                // Render Image Icon (overrides default styling)
                echo '<div class="icon-box" style="padding:0; border:none; background:transparent; overflow:hidden;">';
                echo '<img src="' . esc_url($icon_uri) . '" style="width:100%; height:100%; object-fit:cover;">';
                echo '</div>';
            } else {
                // Render Default CSS/Unicode Icon
                echo '<div class="icon-box ' . $app_data['class'] . '">' . $app_data['icon'] . '</div>';
            }
            
            echo '<span class="icon-label">' . $app_data['label'] . '</span>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<script>
// --- KERNEL NAVIGATION ---

// Listen for browser/phone Back Button
window.addEventListener('popstate', function(e) {
    if (e.state && e.state.app) {
        // If history has an app state, restore that app view
        openApp(e.state.app, true);
    } else {
        // Otherwise, return to launcher
        goHome(true);
    }
});

function goHome(fromHistory) {
    // If user clicked the UI Home button (not back button), and we have a history state pushed,
    // we should go back in history to keep the stack clean.
    if (!fromHistory && history.state && history.state.app) {
        history.back();
        return;
    }

    document.getElementById('launcher-view').style.display = 'block'; 
    document.getElementById('app-nav').style.display = 'none';
    
    // Hide all apps using the shared class
    document.querySelectorAll('.app-module').forEach(el => {
        el.style.display = 'none';
    });
}

function openApp(app, fromHistory) {
    // Push new state to history if this wasn't triggered by the back button
    if (!fromHistory) {
        history.pushState({app: app}, null, "");
    }

    document.getElementById('launcher-view').style.display = 'none'; 
    document.getElementById('app-nav').style.display = 'flex';
    
    // Hide all modules
    document.querySelectorAll('.app-module').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show selected and enforce top alignment
    const appContainer = document.getElementById('app-'+app);
    if(appContainer) {
        appContainer.style.display = 'flex';
        appContainer.style.flexDirection = 'column';
        appContainer.style.justifyContent = 'flex-start';
    }
    
    // Setup Header
    const titles = { 'chat': 'Lollms Chat', 'grocery': 'Groceries', 'cards': 'Cards', 'coupons': 'Coupons', 'calendar': 'Calendar', 'qr': 'QR Tools', 'crypto': 'Wallet', 'converter': 'Converter' };
    document.getElementById('app-title').innerText = titles[app] || 'App';
    
    // Setup Header Action Button
    const addBtn = document.getElementById('btn-header-add');
    if(['chat', 'grocery','qr','crypto','converter'].includes(app)) {
        addBtn.style.visibility = 'hidden'; 
    } else { 
        addBtn.style.visibility = 'visible'; 
        addBtn.onclick = () => openEditor(app==='calendar'?'calendar':(app==='coupons'?'coupon':'wallet')); 
    }
    
    // Initialize App
    if(app==='chat' && typeof initChat === 'function') initChat();
    if(app==='grocery' && typeof loadGrocery === 'function') loadGrocery(); 
    if(app==='cards' && typeof loadCards === 'function') loadCards(); 
    if(app==='coupons' && typeof loadCoupons === 'function') loadCoupons(); 
    if(app==='calendar' && typeof loadCalendar === 'function') loadCalendar(); 
    if(app==='qr' && typeof loadQR === 'function') loadQR(); 
    if(app==='crypto' && typeof loadCrypto === 'function') loadCrypto(); 
    if(app==='converter' && typeof initConverter === 'function') initConverter();
}
</script>
