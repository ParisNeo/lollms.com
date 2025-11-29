<?php
/**
 * Template Name: App Tools
 */
if ( !is_user_logged_in() ) { wp_redirect(wp_login_url(get_permalink())); exit; }
get_header(); 
?>

<!-- Scoped Styles for Tools App -->
<style>
    /* --- GROCERY INPUT REVAMP --- */
    .grocery-input-card {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .g-row-main {
        display: flex; gap: 12px; margin-bottom: 12px;
    }
    
    .g-input-lg {
        flex: 1;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        padding: 16px;
        border-radius: 12px;
        color: white;
        font-size: 1.1rem;
        outline: none;
        transition: 0.2s;
    }
    .g-input-lg:focus { border-color: var(--primary); background: rgba(255,255,255,0.1); }
    
    .g-btn-scan-lg {
        width: 58px;
        background: rgba(255,255,255,0.1);
        border: 1px solid var(--border);
        border-radius: 12px;
        font-size: 1.6rem;
        cursor: pointer;
        color: white;
        display: flex; align-items: center; justify-content: center;
        transition: 0.2s;
    }
    .g-btn-scan-lg:active { transform: scale(0.95); background: rgba(255,255,255,0.2); }

    .g-row-details {
        display: flex; gap: 12px;
    }
    
    .g-details-group {
        display: flex; gap: 10px; flex: 1;
    }
    
    .g-input-sm {
        width: 100%;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        padding: 16px;
        border-radius: 12px;
        color: white;
        font-size: 1rem;
        text-align: center;
        outline: none;
        transition: 0.2s;
    }
    .g-input-sm:focus { border-color: var(--primary); }

    .g-btn-add-lg {
        flex: 1;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        padding: 16px;
        white-space: nowrap;
        transition: 0.2s;
    }
    .g-btn-add-lg:active { transform: scale(0.97); }

    /* Mobile Layout Overrides */
    @media (max-width: 600px) {
        .g-row-details { flex-direction: column; }
        .g-details-group { width: 100%; }
        .g-btn-add-lg { width: 100%; padding: 18px; font-size: 1.2rem; } /* Big touch target */
    }

    /* --- GLOBAL STYLES --- */
    /* Header Add Button */
    .btn-header-add {
        width: 36px; height: 36px;
        border-radius: 8px;
        border: none;
        color: white;
        font-size: 1.5rem;
        font-weight: 400;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: transform 0.2s, box-shadow 0.2s;
        padding-bottom: 2px;
    }
    .btn-header-add:active { transform: scale(0.9); }

    /* Modal Form Styling */
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.85); backdrop-filter: blur(5px);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-card {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 20px;
        width: 100%;
        max-width: 450px;
        padding: 30px;
        position: relative;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        max-height: 90vh;
        overflow-y: auto;
    }
    @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    .modal-label { display: block; color: var(--text-dim); font-size: 0.85rem; margin-bottom: 8px; text-transform: uppercase; font-weight: 700; }
    .modal-input {
        width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--border);
        padding: 15px; border-radius: 12px; color: white; font-size: 1rem; margin-bottom: 20px;
        outline: none; transition: 0.2s;
    }
    .modal-input:focus { border-color: var(--primary); background: rgba(0,0,0,0.5); }
    
    .photo-upload-box {
        border: 2px dashed var(--border);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        cursor: pointer;
        transition: 0.2s;
        background-size: cover;
        background-position: center;
        height: 150px;
        display: flex; flex-direction: column; justify-content: center;
    }
    .photo-upload-box:hover { border-color: var(--primary); background-color: rgba(99, 102, 241, 0.05); }

    .modal-actions { display: flex; gap: 10px; margin-top: 10px; }
    .btn-full { flex: 1; padding: 15px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; font-size: 1rem; }
    .btn-save { background: var(--success); color: white; }
    .btn-delete { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }
    .btn-cancel { background: transparent; border: 1px solid var(--border); color: var(--text-dim); }

    /* Full Screen Barcode Overlay */
    #fs-barcode-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: #ffffff;
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .fs-barcode-container {
        transform: rotate(90deg);
        width: 80vh; 
        height: 90vw;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    #fs-barcode-text {
        color: #000; font-family: monospace; font-size: 1.5rem; font-weight: bold; margin-top: 20px; letter-spacing: 2px;
    }
    .fs-hint {
        position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%);
        color: #aaa; font-size: 0.9rem; pointer-events: none;
    }
    
    .coupon-card.exhausted {
        filter: grayscale(1);
        opacity: 0.6;
        border-left-color: #555 !important;
    }
    .coupon-card.exhausted .coupon-val { color: #888; text-decoration: line-through; }
</style>

<main class="nexus-container">
    <div class="app-container">
        
        <!-- HEADER (App Navigation) -->
        <div id="app-nav" class="app-header" style="display:none;">
            <button class="btn-home" onclick="goHome()">⌂</button>
            <h2 id="app-title" style="margin:0; font-size:1.2rem;">App</h2>
            <!-- Header Add Button -->
            <button id="btn-header-add" class="btn-header-add">+</button>
        </div>

        <!-- 1. LAUNCHER (Home) -->
        <div id="launcher-view">
            <h1 style="text-align:center; font-size:2rem; margin-top:20px;">Nexus OS</h1>
            <p style="text-align:center; color:var(--text-dim);">Select an application</p>
            
            <div class="app-launcher-grid">
                <div class="app-icon" onclick="openApp('grocery')">
                    <div class="icon-box icon-grocery">🛒</div>
                    <span class="icon-label">Groceries</span>
                </div>
                <div class="app-icon" onclick="openApp('wallet')">
                    <div class="icon-box icon-wallet">💳</div>
                    <span class="icon-label">Wallet</span>
                </div>
                <div class="app-icon" onclick="openApp('coupons')">
                    <div class="icon-box icon-coupons">🎟️</div>
                    <span class="icon-label">Coupons</span>
                </div>
            </div>
        </div>

        <!-- 2. GROCERY APP -->
        <div id="app-grocery" style="display:none;">
            
            <!-- NEW REVAMPED INPUT CARD -->
            <div class="grocery-input-card">
                <div class="g-row-main">
                    <input type="text" id="g-name" class="g-input-lg" placeholder="What do you need?" onkeypress="handleGroceryEnter(event)">
                    <button class="g-btn-scan-lg" onclick="startScanner('grocery')">📷</button>
                </div>
                <div class="g-row-details">
                    <div class="g-details-group">
                        <input type="text" id="g-qty" class="g-input-sm" placeholder="Qty" onkeypress="handleGroceryEnter(event)">
                        <input type="number" id="g-price" class="g-input-sm" placeholder="Price" step="0.01" onkeypress="handleGroceryEnter(event)">
                    </div>
                    <button class="g-btn-add-lg" onclick="addGrocery()">Add Item</button>
                </div>
            </div>
            
            <ul id="grocery-list" class="item-list" style="padding-bottom: 80px;"></ul>
            
            <div style="margin-top:20px; color:var(--text-dim); font-size:0.9rem;">HISTORY</div>
            <ul id="grocery-history" class="item-list" style="opacity:0.6; padding-bottom: 80px;"></ul>
            <div class="total-bar">
                <div style="font-size:0.8rem; color:var(--text-dim); text-transform:uppercase;">Total</div>
                <div id="grand-total" class="total-amount">$0.00</div>
            </div>
        </div>

        <!-- 3. WALLET APP -->
        <div id="app-wallet" style="display:none; padding-bottom: 20px;">
            <div id="wallet-grid" class="wallet-grid"></div>
        </div>

        <!-- 4. COUPONS APP -->
        <div id="app-coupons" style="display:none; padding-bottom: 20px;">
            <div id="coupons-list"></div>
        </div>

    </div>
</main>

<!-- EDITOR MODAL (CRUD) -->
<div id="editor-modal" class="modal-overlay">
    <div class="modal-card">
        <h2 id="editor-title" style="margin-top:0; margin-bottom:20px;">Add Item</h2>
        
        <input type="hidden" id="edit-id">
        <input type="hidden" id="edit-type">

        <!-- Image Upload (Visible for Wallet) -->
        <div id="field-image-container" style="display:none;">
            <label class="modal-label">Card Image</label>
            <label class="photo-upload-box" id="edit-photo-box">
                <span id="edit-photo-label" style="color:var(--text-dim);">Tap to snap photo</span>
                <input type="file" id="edit-photo" accept="image/*" capture="environment" style="display:none;" onchange="handleEditorImage()">
            </label>
        </div>

        <!-- Name -->
        <label class="modal-label">Name</label>
        <input type="text" id="edit-name" class="modal-input" placeholder="Item Name (e.g. IKEA Family)">

        <!-- Barcode -->
        <label class="modal-label">Barcode</label>
        <div style="display:flex; gap:10px; margin-bottom:20px;">
            <input type="text" id="edit-code" class="modal-input" placeholder="Scanned Code" style="margin-bottom:0;">
            <button class="btn-scan" onclick="startScanner('editor')">📷</button>
        </div>

        <!-- Show Barcode Button (Only visible if code exists) -->
        <button id="btn-show-code" class="btn-full" onclick="showFullscreenBarcode()" 
                style="background: #fff; color: #000; margin-bottom: 20px; display: none; align-items:center; justify-content:center; gap:10px;">
            📱 Show Code for Scanning
        </button>

        <!-- Value (Visible for Coupons) -->
        <div id="field-value-container" style="display:none;">
            <label class="modal-label">Value ($)</label>
            <input type="number" id="edit-price" class="modal-input" placeholder="0.00" step="0.01">
        </div>

        <!-- Actions -->
        <div class="modal-actions">
            <button class="btn-full btn-delete" id="btn-editor-delete" onclick="deleteFromEditor()" style="display:none;">Delete</button>
            <button class="btn-full btn-cancel" onclick="closeEditor()">Cancel</button>
            <button class="btn-full btn-save" onclick="saveItem()">Save</button>
        </div>
    </div>
</div>

<!-- FULL SCREEN BARCODE OVERLAY -->
<div id="fs-barcode-overlay" onclick="closeFullscreenBarcode()">
    <div class="fs-barcode-container">
        <svg id="fs-barcode-svg"></svg>
        <div id="fs-barcode-text"></div>
    </div>
    <div class="fs-hint">Tap anywhere to close & deduct</div>
</div>

<!-- SCANNER MODAL -->
<div id="scanner-modal" style="display:none;">
    <div style="position:absolute; top:0; width:100%; padding:20px; background:rgba(0,0,0,0.5); color:white; text-align:center; z-index:2;">Scan Barcode</div>
    <div id="reader"></div>
    <button class="close-scan" onclick="stopScanner()">Cancel</button>
</div>

<script>
const apiRoot = "<?php echo esc_url_raw(rest_url('lollms/v1/')); ?>";
const nonce = "<?php echo wp_create_nonce('wp_rest'); ?>";

// State
let currentApp = 'launcher';
let editorImageBase64 = null;
let html5QrcodeScanner = null;

// --- NAVIGATION ---
function goHome() {
    document.getElementById('launcher-view').style.display = 'block';
    document.getElementById('app-nav').style.display = 'none';
    ['app-grocery', 'app-wallet', 'app-coupons'].forEach(id => document.getElementById(id).style.display = 'none');
    currentApp = 'launcher';
}

function openApp(appName) {
    currentApp = appName;
    document.getElementById('launcher-view').style.display = 'none';
    document.getElementById('app-nav').style.display = 'flex';
    
    // Hide all
    ['app-grocery', 'app-wallet', 'app-coupons'].forEach(id => document.getElementById(id).style.display = 'none');
    
    // Show selected
    document.getElementById('app-' + appName).style.display = 'block';
    
    // Configure Titles
    const titles = { 'grocery': '🛒 Groceries', 'wallet': '💳 Wallet', 'coupons': '🎟️ Coupons' };
    document.getElementById('app-title').innerText = titles[appName];

    // Configure Header Add Button
    const addBtn = document.getElementById('btn-header-add');
    if (appName === 'grocery') {
        addBtn.style.visibility = 'hidden';
    } else {
        addBtn.style.visibility = 'visible';
        
        if (appName === 'wallet') {
            addBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            addBtn.onclick = () => openEditor('wallet');
        } else if (appName === 'coupons') {
            addBtn.style.background = 'linear-gradient(135deg, #f43f5e, #be123c)';
            addBtn.onclick = () => openEditor('coupon');
        }
    }

    // Load Data
    if(appName === 'grocery') loadGrocery();
    if(appName === 'wallet') loadWallet();
    if(appName === 'coupons') loadCoupons();
}

// --- EDITOR LOGIC (CRUD) ---
function openEditor(type, item = null) {
    // Reset Fields
    document.getElementById('edit-id').value = item ? item.id : '';
    document.getElementById('edit-type').value = type;
    document.getElementById('edit-name').value = item ? item.item_name : '';
    document.getElementById('edit-code').value = item ? item.barcode : '';
    document.getElementById('edit-price').value = item ? item.price : '';
    document.getElementById('edit-photo').value = ''; 
    editorImageBase64 = item ? item.image_data : null;

    // UI State: Edit vs Add
    document.getElementById('editor-title').innerText = item ? 'Edit Item' : 'New ' + type.charAt(0).toUpperCase() + type.slice(1);
    document.getElementById('btn-editor-delete').style.display = item ? 'block' : 'none';

    // Field Visibility
    const isWallet = type === 'wallet';
    const isCoupon = type === 'coupon';
    
    document.getElementById('field-image-container').style.display = isWallet ? 'block' : 'none';
    document.getElementById('field-value-container').style.display = isCoupon ? 'block' : 'none';

    // Photo Preview Logic
    const photoBox = document.getElementById('edit-photo-box');
    const photoLabel = document.getElementById('edit-photo-label');
    if (editorImageBase64) {
        photoBox.style.backgroundImage = `url(${editorImageBase64})`;
        photoLabel.style.display = 'none';
    } else {
        photoBox.style.backgroundImage = 'none';
        photoLabel.style.display = 'block';
    }

    // Toggle "Show Barcode" button
    if (item && item.barcode) {
        document.getElementById('btn-show-code').style.display = 'flex';
    } else {
        document.getElementById('btn-show-code').style.display = 'none';
    }

    // Show Modal
    document.getElementById('editor-modal').style.display = 'flex';
}

function closeEditor() {
    document.getElementById('editor-modal').style.display = 'none';
}

function handleEditorImage() {
    const file = document.getElementById('edit-photo').files[0];
    if(file) {
        const reader = new FileReader();
        reader.onloadend = function() {
            editorImageBase64 = reader.result;
            document.getElementById('edit-photo-box').style.backgroundImage = `url(${editorImageBase64})`;
            document.getElementById('edit-photo-label').style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}

// --- FULL SCREEN BARCODE LOGIC ---
function showFullscreenBarcode() {
    const code = document.getElementById('edit-code').value;
    if(!code) return;

    document.getElementById('fs-barcode-overlay').style.display = 'flex';
    document.getElementById('fs-barcode-text').innerText = code;
    
    // Render
    try {
        JsBarcode("#fs-barcode-svg", code, { 
            format: "CODE128", 
            lineColor: "#000", 
            width: 3, 
            height: 100, 
            displayValue: false,
            background: "#ffffff"
        });
    } catch(e) {
        document.getElementById('fs-barcode-text').innerText = code + " (Render Error)";
    }
}

async function closeFullscreenBarcode() {
    document.getElementById('fs-barcode-overlay').style.display = 'none';
    
    // --- PAY & DEDUCT LOGIC ---
    const type = document.getElementById('edit-type').value;
    const id = document.getElementById('edit-id').value;
    
    // Only prompt for Coupons that have a valid ID (saved items)
    if (type === 'coupon' && id) {
        // Small delay to ensure UI updates
        setTimeout(async () => {
            const spent = prompt("How much did you spend using this coupon? (Leave empty if just viewing)");
            if (spent !== null && spent !== '' && parseFloat(spent) > 0) {
                // Perform Deduction
                await fetch(`${apiRoot}deduct`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                    body: JSON.stringify({ id: id, spent: spent })
                });
                
                // Close the editor as we are done
                closeEditor();
                loadCoupons();
            }
        }, 100);
    }
}

async function saveItem() {
    const id = document.getElementById('edit-id').value;
    const type = document.getElementById('edit-type').value;
    const name = document.getElementById('edit-name').value;
    
    if(!name) return alert("Name is required");

    const data = {
        name: name,
        item_name: name,
        type: type,
        barcode: document.getElementById('edit-code').value,
        price: document.getElementById('edit-price').value,
        image_data: editorImageBase64
    };

    if (id) {
        data.id = id;
        await fetch(`${apiRoot}update`, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
            body: JSON.stringify(data)
        });
    } else {
        await apiAdd(data);
    }

    closeEditor();
    if (type === 'wallet') loadWallet();
    if (type === 'coupon') loadCoupons();
}

async function deleteFromEditor() {
    const id = document.getElementById('edit-id').value;
    const type = document.getElementById('edit-type').value;
    if(!id || !confirm('Permanently delete this item?')) return;
    
    await fetch(`${apiRoot}delete`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify({ id: id }) });
    
    closeEditor();
    if (type === 'wallet') loadWallet();
    if (type === 'coupon') loadCoupons();
}

// --- GROCERY LOGIC ---
async function loadGrocery() {
    const list = document.getElementById('grocery-list');
    list.innerHTML = 'Loading...';
    
    const [resItems, resHist] = await Promise.all([
        fetch(`${apiRoot}items/grocery`, { headers: { 'X-WP-Nonce': nonce } }),
        fetch(`${apiRoot}suggestions`, { headers: { 'X-WP-Nonce': nonce } })
    ]);
    
    const items = await resItems.json();
    const history = await resHist.json();

    list.innerHTML = '';
    items.forEach(item => {
        list.innerHTML += `
            <li class="todo-item" id="item-${item.id}">
                <div class="todo-checkbox" onclick="toggleItem(${item.id})"></div>
                <div class="item-details">
                    <div style="display:flex; justify-content:space-between;">
                        <span class="item-name">${item.item_name}</span>
                    </div>
                    <div class="item-inputs">
                        <input class="inline-input qty" value="${item.quantity}" onchange="updateItem(${item.id}, 'quantity', this.value)">
                        <input class="inline-input price" type="number" step="0.01" value="${item.price}" onchange="updateItem(${item.id}, 'price', this.value)">
                    </div>
                </div>
            </li>`;
    });
    
    calculateTotal();
    renderHistory(history);
}

function renderHistory(items) {
    const list = document.getElementById('grocery-history');
    list.innerHTML = '';
    items.forEach(item => {
        if(item.list_type !== 'grocery') return;
        const safeName = item.item_name.replace(/'/g, "\\'");
        list.innerHTML += `
            <li class="history-item">
                <span>${item.item_name}</span>
                <button class="btn-icon" style="color:var(--success);" onclick="restoreItem('${safeName}', '${item.quantity}', '${item.price}')">+</button>
            </li>`;
    });
}

function handleGroceryEnter(e) {
    if(e.key === 'Enter') addGrocery();
}

async function addGrocery() {
    const name = document.getElementById('g-name').value;
    if(!name) return;
    await apiAdd({
        name: name, type: 'grocery', 
        qty: document.getElementById('g-qty').value,
        price: document.getElementById('g-price').value
    });
    document.getElementById('g-name').value = '';
    document.getElementById('g-qty').value = '';
    document.getElementById('g-price').value = '';
    // Re-focus name for rapid entry
    document.getElementById('g-name').focus();
    loadGrocery();
}

// --- WALLET LOGIC ---
async function loadWallet() {
    const grid = document.getElementById('wallet-grid');
    grid.innerHTML = '<div style="color:var(--text-dim); padding:20px;">Loading wallet...</div>';
    const res = await fetch(`${apiRoot}items/wallet`, { headers: { 'X-WP-Nonce': nonce } });
    const items = await res.json();
    
    grid.innerHTML = '';
    items.forEach(item => {
        const card = document.createElement('div');
        card.className = 'wallet-card';
        if(item.image_data) {
            card.innerHTML = `<img src="${item.image_data}">`;
        }
        card.innerHTML += `
            <div class="wallet-content">
                <div class="wallet-title">${item.item_name}</div>
                <div class="wallet-code">${item.barcode || '****'}</div>
            </div>
        `;
        card.onclick = () => openEditor('wallet', item);
        grid.appendChild(card);
    });
}

// --- COUPON LOGIC ---
async function loadCoupons() {
    const list = document.getElementById('coupons-list');
    list.innerHTML = '<div style="color:var(--text-dim); padding:20px;">Loading coupons...</div>';
    const res = await fetch(`${apiRoot}items/coupon`, { headers: { 'X-WP-Nonce': nonce } });
    const items = await res.json();
    
    list.innerHTML = '';
    items.forEach(item => {
        const isExhausted = parseFloat(item.price) <= 0.01;
        const exhaustedClass = isExhausted ? ' exhausted' : '';
        const exhaustedLabel = isExhausted ? ' (Empty)' : '';
        
        const div = document.createElement('div');
        div.className = 'coupon-card' + exhaustedClass;
        div.innerHTML = `
            <div class="coupon-top">
                <div>
                    <div style="font-weight:bold; font-size:1.1rem;">${item.item_name}${exhaustedLabel}</div>
                    <div style="color:#666; font-size:0.8rem;">${item.barcode ? 'Code: ' + item.barcode : 'Tap to edit'}</div>
                </div>
                <div class="coupon-val">$${parseFloat(item.price).toFixed(2)}</div>
            </div>
            <div class="coupon-rip"></div>
        `;
        div.onclick = () => openEditor('coupon', item);
        list.appendChild(div);
    });
}

// --- API HELPERS ---
async function apiAdd(data) {
    return fetch(`${apiRoot}add`, {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify(data)
    });
}

async function toggleItem(id) {
    await fetch(`${apiRoot}toggle`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify({ id: id }) });
    loadGrocery();
}

async function restoreItem(name, qty, price) {
    await apiAdd({ name: name, type: 'grocery', qty: qty, price: price });
    loadGrocery();
}

async function updateItem(id, field, value) {
    await fetch(`${apiRoot}update`, {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify({ id: id, field: field, value: value })
    });
    calculateTotal();
}

function calculateTotal() {
    let t = 0;
    document.querySelectorAll('.inline-input.price').forEach(i => t += (parseFloat(i.value)||0));
    document.getElementById('grand-total').innerText = '$' + t.toFixed(2);
}

// --- SCANNER ---
let scanTargetApp = '';

function startScanner(app) {
    scanTargetApp = app;
    document.getElementById('scanner-modal').style.display = 'flex';
    
    if(html5QrcodeScanner) {
        try { html5QrcodeScanner.clear(); } catch(e){}
    }

    html5QrcodeScanner = new Html5Qrcode("reader");
    
    const w = window.innerWidth;
    const boxWidth = Math.min(320, w * 0.85);
    const boxHeight = 120;
    
    const config = { 
        fps: 15, 
        qrbox: { width: boxWidth, height: boxHeight },
        aspectRatio: 1.777778,
        experimentalFeatures: { useBarCodeDetectorIfSupported: true },
        formatsToSupport: [
            Html5QrcodeSupportedFormats.QR_CODE,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.CODABAR,
            Html5QrcodeSupportedFormats.ITF,
            Html5QrcodeSupportedFormats.DATA_MATRIX
        ]
    };

    html5QrcodeScanner.start(
        { facingMode: "environment" }, 
        config, 
        onScanSuccess
    ).catch(err => {
        console.error("Scanner Error:", err);
        alert("Camera access denied or failed. Please check permissions.");
        stopScanner();
    });
}

function stopScanner() {
    if(html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            document.getElementById('scanner-modal').style.display = 'none';
            document.getElementById('reader').innerHTML = "";
            html5QrcodeScanner.clear();
        }).catch(err => {
            document.getElementById('scanner-modal').style.display = 'none';
            document.getElementById('reader').innerHTML = "";
        });
    } else {
        document.getElementById('scanner-modal').style.display = 'none';
    }
}

function onScanSuccess(decodedText) {
    stopScanner();
    // If scanning from the Editor Modal
    if(scanTargetApp === 'editor') {
        document.getElementById('edit-code').value = decodedText;
    }
    else if(scanTargetApp === 'grocery') {
        document.getElementById('g-name').value = "Scanned: " + decodedText; 
    }
}
</script>

<?php get_footer(); ?>
