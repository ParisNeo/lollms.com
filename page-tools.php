<?php
/**
 * Template Name: App Tools
 */
if ( !is_user_logged_in() ) { wp_redirect(wp_login_url(get_permalink())); exit; }
get_header(); 
?>

<style>
    /* --- SHARED STYLES --- */
    .g-row-main { display: flex; gap: 12px; margin-bottom: 12px; }
    .g-input-lg { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid var(--border); padding: 16px; border-radius: 12px; color: white; font-size: 1.1rem; outline: none; }
    .g-input-lg:focus { border-color: var(--primary); background: rgba(255,255,255,0.1); }
    .g-btn-scan-lg { width: 58px; background: rgba(255,255,255,0.1); border: 1px solid var(--border); border-radius: 12px; font-size: 1.6rem; cursor: pointer; color: white; display: flex; align-items: center; justify-content: center; }
    
    /* Modal */
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(5px); z-index: 2000; display: none; align-items: center; justify-content: center; padding: 20px; }
    .modal-card { background: var(--bg-panel); border: 1px solid var(--border); border-radius: 20px; width: 100%; max-width: 450px; padding: 30px; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.5); max-height: 90vh; overflow-y: auto; }
    .modal-label { display: block; color: var(--text-dim); font-size: 0.85rem; margin-bottom: 8px; text-transform: uppercase; font-weight: 700; margin-top: 15px; }
    .modal-input { width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--border); padding: 15px; border-radius: 12px; color: white; font-size: 1rem; outline: none; }
    .modal-input:focus { border-color: var(--primary); background: rgba(0,0,0,0.5); }
    .modal-actions { display: flex; gap: 10px; margin-top: 25px; }
    .btn-full { flex: 1; padding: 15px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; font-size: 1rem; }
    .btn-save { background: var(--success); color: white; }
    .btn-delete { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }
    .btn-cancel { background: transparent; border: 1px solid var(--border); color: var(--text-dim); }
    .btn-primary { background: var(--primary); color:white; }

    /* Barcode/Scanner */
    #fs-barcode-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: #ffffff; z-index: 10000; display: none; align-items: center; justify-content: center; }
    .fs-barcode-container { transform: rotate(90deg); width: 80vh; height: 90vw; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    #fs-barcode-text { color: #000; font-family: monospace; font-size: 1.5rem; font-weight: bold; margin-top: 20px; }
    #scanner-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; z-index: 9999; flex-direction: column; }

    /* --- SPECIFIC APP STYLES --- */
    #calendar-container { background: rgba(30, 41, 59, 0.5); border-radius: 12px; padding: 10px; min-height: 500px; }
    .btn-scan-lg-qr { width: 200px; height: 200px; border-radius: 50%; border: 4px solid var(--primary); background: rgba(99,102,241,0.1); color: white; cursor: pointer; display: inline-flex; flex-direction: column; align-items: center; justify-content: center; transition: 0.3s; }
    .btn-scan-lg-qr:hover { transform: scale(1.05); }
    .qr-card { background: white; color: black; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; cursor: pointer; text-align: center; }
    .qr-card img { width: 100px; height: 100px; margin: 15px auto; }
    .crypto-card { background: linear-gradient(135deg, #0f172a, #1e293b); border: 1px solid var(--border); border-radius: 16px; padding: 20px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; transition: 0.2s; cursor: pointer; }
    .crypto-card:hover { border-color: var(--primary); transform: translateY(-3px); }
    .crypto-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-right: 15px; }
    .crypto-icon.btc { background: #f7931a; color: white; }
    .crypto-icon.eth { background: #627eea; color: white; }
    .crypto-icon.lol { background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; }
    .crypto-info { flex-grow: 1; overflow: hidden; }
    .crypto-name { font-weight: bold; font-size: 1.1rem; }
    .crypto-address { font-family: monospace; color: var(--text-dim); font-size: 0.85rem; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 200px; }
    .crypto-balance { font-size: 1.1rem; font-weight: 700; color: var(--success); text-align: right; min-width: 80px; }
    .tx-list { margin-top: 15px; border-top: 1px solid var(--border); padding-top: 10px; max-height: 200px; overflow-y: auto; }
    .tx-item { font-size: 0.8rem; padding: 8px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; }
    .tx-item.in { color: var(--success); }
    .tx-item.out { color: var(--danger); }
    .tx-hash { font-family: monospace; opacity: 0.7; }
    #modal-map { height: 200px; width: 100%; border-radius: 12px; margin-top: 10px; border: 1px solid var(--border); display: none; }

    /* Converter Styles */
    .conv-grid { display: grid; grid-template-columns: 1fr auto 1fr; gap: 10px; align-items: center; margin-top: 20px; }
    .conv-input-group { background: rgba(255,255,255,0.05); border-radius: 12px; padding: 15px; border: 1px solid var(--border); }
    .conv-input { width: 100%; background: transparent; border: none; color: white; font-size: 1.5rem; outline: none; margin-bottom: 5px; }
    .conv-select { width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--border); color: var(--text-dim); border-radius: 6px; padding: 5px; font-size: 0.9rem; outline: none; }
    .conv-equal { font-size: 2rem; color: var(--text-dim); text-align: center; }
    
    /* Currency Ticker */
    .currency-ticker-wrap {
        background: rgba(0,0,0,0.3); border-radius: 12px; padding: 10px; margin-bottom: 20px;
        border: 1px solid var(--border); overflow-x: auto; white-space: nowrap;
    }
    .ticker-item {
        display: inline-block; padding: 5px 15px; border-right: 1px solid var(--border);
        font-size: 0.9rem; color: var(--text-main);
    }
    .ticker-item:last-child { border-right: none; }
    .ticker-val { color: var(--success); font-weight: bold; margin-left: 5px; }
    .ticker-label { color: var(--text-dim); font-size: 0.8rem; }
    
    /* Import/Export Buttons */
    .btn-tool-sm { background: rgba(255,255,255,0.1); border: 1px solid var(--border); padding: 5px 15px; border-radius: 8px; color: var(--text-dim); cursor: pointer; font-size: 0.85rem; transition: 0.2s; }
    .btn-tool-sm:hover { border-color: var(--primary); color: white; background: rgba(99,102,241,0.1); }
</style>

<main class="nexus-container">
    <div class="app-container">
        <!-- HEADER -->
        <div id="app-nav" class="app-header" style="display:none; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:15px;">
            <button class="btn-home" onclick="goHome()" style="background:rgba(255,255,255,0.1); border:1px solid var(--border); border-radius:8px; width:36px; height:36px; display:flex; align-items:center; justify-content:center; color:white; font-size:1.2rem; cursor:pointer;">⌂</button>
            <h2 id="app-title" style="margin:0; font-size:1.2rem;">App</h2>
            <button id="btn-header-add" class="btn-header-add" style="width:36px; height:36px; border-radius:8px; border:none; color:white; font-size:1.5rem; cursor:pointer;">+</button>
        </div>

        <!-- 1. LAUNCHER -->
        <div id="launcher-view">
            <h1 style="text-align:center; font-size:2rem; margin-top:20px;">LoLLMs OS</h1>
            <p style="text-align:center; color:var(--text-dim);">Select an application</p>
            <div class="app-launcher-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(100px, 1fr)); gap:20px; padding:40px 10px;">
                <div class="app-icon" onclick="openApp('grocery')"><div class="icon-box icon-grocery">🛒</div><span class="icon-label">Groceries</span></div>
                <div class="app-icon" onclick="openApp('cards')"><div class="icon-box icon-wallet">💳</div><span class="icon-label">Cards</span></div>
                <div class="app-icon" onclick="openApp('coupons')"><div class="icon-box icon-coupons">🎟️</div><span class="icon-label">Coupons</span></div>
                <div class="app-icon" onclick="openApp('calendar')"><div class="icon-box" style="background:linear-gradient(135deg, #8b5cf6, #7c3aed); color:white;">📅</div><span class="icon-label">Calendar</span></div>
                <div class="app-icon" onclick="openApp('qr')"><div class="icon-box" style="background:linear-gradient(135deg, #f59e0b, #d97706); color:white;">📷</div><span class="icon-label">QR Tools</span></div>
                <div class="app-icon" onclick="openApp('crypto')"><div class="icon-box" style="background:linear-gradient(135deg, #06b6d4, #3b82f6); color:white;">🪙</div><span class="icon-label">Wallet</span></div>
                <div class="app-icon" onclick="openApp('converter')"><div class="icon-box" style="background:linear-gradient(135deg, #ec4899, #be185d); color:white;">⇄</div><span class="icon-label">Converter</span></div>
            </div>
        </div>

        <!-- APPS -->
        <div id="app-grocery" style="display:none;">
            <div class="grocery-input-card">
                <div class="g-row-main"><input type="text" id="g-name" class="g-input-lg" placeholder="What do you need?" onkeypress="handleGroceryEnter(event)"><button class="g-btn-scan-lg" onclick="startScanner('grocery')">📷</button></div>
                <div style="display:flex; gap:12px;"><div style="display:flex; gap:10px; flex:1;"><input type="text" id="g-qty" class="modal-input" placeholder="Qty" style="text-align:center;"><input type="number" id="g-price" class="modal-input" placeholder="Price" step="0.01" style="text-align:center;"></div><button class="btn-save" style="flex:1; border-radius:12px; border:none; font-weight:bold; cursor:pointer;" onclick="addGrocery()">ADD</button></div>
            </div>
            <ul id="grocery-list" class="item-list" style="padding-bottom:80px;"></ul>
            <div style="margin-top:20px; color:var(--text-dim); font-size:0.9rem;">HISTORY</div>
            <ul id="grocery-history" class="item-list" style="opacity:0.6; padding-bottom:80px;"></ul>
            <div class="total-bar"><div style="font-size:0.8rem; color:var(--text-dim); text-transform:uppercase;">Total</div><div id="grand-total" class="total-amount">$0.00</div></div>
        </div>

        <div id="app-cards" style="display:none; padding-bottom: 20px;">
            <div style="display:flex; gap:10px; margin-bottom:15px; justify-content:flex-end;">
                <button class="btn-tool-sm" onclick="importCards()">⬆ Import</button>
                <button class="btn-tool-sm" onclick="exportCards()">⬇ Export</button>
                <input type="file" id="import-file" style="display:none" accept=".json" onchange="handleImportFile(this)">
            </div>
            <div id="cards-grid" class="wallet-grid"></div>
        </div>
        
        <div id="app-coupons" style="display:none;"><div id="coupons-list"></div></div>
        <div id="app-calendar" style="display:none; height:100%;"><div id="calendar-container"></div></div>

        <div id="app-qr" style="display:none;">
            <div class="app-tabs" style="display:flex; background:rgba(0,0,0,0.2); padding:4px; border-radius:12px; margin-bottom:20px;">
                <div class="app-tab active" onclick="switchQRTab('scan')" id="tab-qr-scan">Scan</div>
                <div class="app-tab" onclick="switchQRTab('gen')" id="tab-qr-gen">Generate</div>
                <div class="app-tab" onclick="switchQRTab('lib')" id="tab-qr-lib">Library</div>
            </div>
            <div id="view-qr-scan" class="qr-view"><div style="text-align:center; padding:40px 0;"><button class="btn-scan-lg-qr" onclick="startScanner('qr')"><div style="font-size:3rem;">📷</div><div>Scan QR</div></button><div id="qr-scan-result" style="display:none; margin-top:30px; background:rgba(255,255,255,0.05); padding:20px; border-radius:12px;"><p style="color:var(--text-dim); margin-bottom:5px;">Detected:</p><div id="qr-content-text" style="font-family:monospace; word-break:break-all; margin-bottom:15px; font-size:1.1rem; color:var(--primary);"></div><div style="display:flex; gap:10px;"><button class="btn-full btn-primary" id="btn-qr-visit" style="display:none;" onclick="visitQRLink()">Open</button><button class="btn-full btn-save" onclick="saveScannedQR()">Save</button></div></div></div></div>
            <div id="view-qr-gen" class="qr-view" style="display:none;"><textarea id="qr-input-text" class="modal-input" rows="3" placeholder="Content..."></textarea><button class="btn-full btn-primary" onclick="generateQR()" style="margin-top:10px;">Generate</button><div id="qr-gen-output" style="text-align:center; padding:20px; background:white; border-radius:12px; display:none; margin-top:20px;"><div id="qrcode-canvas" style="display:flex; justify-content:center;"></div></div><button id="btn-save-gen" class="btn-full btn-save" style="display:none; margin-top:10px;" onclick="saveGeneratedQR()">Save</button></div>
            <div id="view-qr-lib" class="qr-view" style="display:none;"><div id="qr-library-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(120px, 1fr)); gap:15px;"></div></div>
        </div>

        <div id="app-crypto" style="display:none; padding-bottom:20px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:20px; background:rgba(255,255,255,0.05); padding:15px; border-radius:12px;">
                <div><div style="font-size:0.8rem; color:var(--text-dim);">BTC Price</div><div id="price-btc" style="font-weight:bold;">Loading...</div></div>
                <div><div style="font-size:0.8rem; color:var(--text-dim);">ETH Price</div><div id="price-eth" style="font-weight:bold;">Loading...</div></div>
            </div>
            <div id="crypto-list"></div>
            <div style="margin-top:40px; border-top:1px solid var(--border); padding-top:20px; text-align:center;">
                <h3 style="margin-top:0;">Add Wallet</h3>
                <div style="margin-bottom:15px;">
                    <button class="btn-primary" style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px; background:#f6851b; border:none; border-radius:12px; padding:12px;" onclick="connectMetaMask()">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/3/36/MetaMask_Fox.svg" width="24"> Connect MetaMask
                    </button>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button class="btn-outline" style="flex:1;" onclick="openEditor('crypto', null, 'BTC')">➕ BTC</button>
                    <button class="btn-outline" style="flex:1;" onclick="openEditor('crypto', null, 'ETH')">➕ ETH</button>
                    <button class="btn-outline" style="flex:1;" onclick="openEditor('crypto', null, 'LOL')">➕ LOL</button>
                </div>
            </div>
        </div>

        <div id="app-converter" style="display:none; padding-bottom:20px;">
            <!-- Realtime Currency Ticker -->
            <div class="currency-ticker-wrap" id="live-currency-ticker">
                <div class="ticker-item" style="font-style:italic; color:var(--text-dim);">Fetching live rates (TND, EUR, GBP, JPY)...</div>
            </div>

            <div class="app-tabs" style="display:flex; background:rgba(0,0,0,0.2); padding:4px; border-radius:12px; margin-bottom:20px;">
                <div class="app-tab active" onclick="switchConvTab('currency')" id="tab-conv-currency">Currency</div>
                <div class="app-tab" onclick="switchConvTab('length')" id="tab-conv-length">Length</div>
                <div class="app-tab" onclick="switchConvTab('weight')" id="tab-conv-weight">Weight</div>
                <div class="app-tab" onclick="switchConvTab('temp')" id="tab-conv-temp">Temp</div>
            </div>
            
            <div class="conv-grid">
                <div class="conv-input-group">
                    <input type="number" id="conv-in-1" class="conv-input" value="1" oninput="convert('1')">
                    <select id="conv-sel-1" class="conv-select" onchange="convert('1')"></select>
                </div>
                <div class="conv-equal">=</div>
                <div class="conv-input-group">
                    <input type="number" id="conv-in-2" class="conv-input" value="1" oninput="convert('2')">
                    <select id="conv-sel-2" class="conv-select" onchange="convert('1')"></select>
                </div>
            </div>
            <div style="text-align:center; font-size:0.8rem; color:var(--text-dim); margin-top:20px;">Rates relative to USD (Live)</div>
        </div>
    </div>
</main>

<!-- EDITOR MODAL -->
<div id="editor-modal" class="modal-overlay">
    <div class="modal-card">
        <h2 id="editor-title" style="margin-top:0; margin-bottom:20px;">Add Item</h2>
        <input type="hidden" id="edit-id"><input type="hidden" id="edit-type"><input type="hidden" id="edit-subtype">

        <!-- Fields -->
        <div id="field-image-container" style="display:none;">
            <label class="modal-label">Image</label>
            <label class="photo-upload-box" id="edit-photo-box"><span id="edit-photo-label" style="color:var(--text-dim);">Tap to snap</span><input type="file" id="edit-photo" accept="image/*" capture="environment" style="display:none;" onchange="handleEditorImage()"></label>
        </div>

        <label class="modal-label" id="label-name">Name</label>
        <input type="text" id="edit-name" class="modal-input" placeholder="Title">

        <div id="field-barcode-container">
            <label class="modal-label" id="label-barcode">Barcode / Address</label>
            <div style="display:flex; gap:10px;">
                <input type="text" id="edit-code" class="modal-input" placeholder="Value" onchange="detectWalletType()">
                <button class="btn-scan" onclick="startScanner('editor')">📷</button>
            </div>
            <div style="display:flex; gap:10px; margin-top:10px;">
                <button id="btn-show-code" class="btn-full" onclick="showFullscreenBarcode()" style="background:#fff; color:#000; display:none;">Show Barcode</button>
                <button id="btn-show-qr-addr" class="btn-full" onclick="showAddrQR()" style="background:#fff; color:#000; display:none;">Receive (QR)</button>
            </div>
            <button id="btn-gen-wallet" class="btn-full btn-primary" onclick="generateWalletAddress()" style="margin-top:10px; display:none;">🎲 Generate New Keys</button>
        </div>

        <!-- Crypto Private Key & History -->
        <div id="field-privkey-container" style="display:none;">
            <label class="modal-label" style="color:var(--danger);">Private Key (Saved Locally)</label>
            <div style="background:rgba(239, 68, 68, 0.1); padding:10px; border-radius:8px; word-break:break-all; font-family:monospace; color:var(--danger); font-size:0.8rem; display:none;" id="privkey-display"></div>
            <button class="btn-full" onclick="document.getElementById('privkey-display').style.display='block'" style="background:transparent; border:1px solid var(--danger); color:var(--danger); margin-top:5px; font-size:0.8rem;">⚠️ Reveal Key</button>
            
            <button class="btn-full" style="background:linear-gradient(135deg, #10b981, #059669); color:white; margin-top:20px; font-weight:bold; padding:15px;" onclick="openSendModal()">💸 SEND / PAY</button>
        </div>
        
        <div id="field-history-container" style="display:none;">
            <label class="modal-label">Recent Transactions</label>
            <div id="tx-history-list" class="tx-list"><div style="color:var(--text-dim); text-align:center;">Loading history...</div></div>
        </div>

        <!-- Calendar -->
        <div id="field-calendar-container" style="display:none;">
            <div style="display:flex; gap:15px;"><div style="flex:1;"><label class="modal-label">Start</label><input type="datetime-local" id="edit-start" class="modal-input"></div><div style="flex:1;"><label class="modal-label">End</label><input type="datetime-local" id="edit-end" class="modal-input"></div></div>
            <label class="modal-label">Location</label><input type="text" id="edit-location" class="modal-input"><div id="modal-map"></div>
            <label class="modal-label">Description</label><textarea id="edit-description" class="modal-input" rows="3"></textarea>
        </div>
        
        <div id="field-value-container" style="display:none;"><label class="modal-label">Value ($)</label><input type="number" id="edit-price" class="modal-input" step="0.01"></div>

        <div class="modal-actions">
            <button class="btn-full btn-delete" id="btn-editor-delete" onclick="deleteFromEditor()" style="display:none;">Delete</button>
            <button class="btn-full btn-cancel" onclick="closeEditor()">Cancel</button>
            <button class="btn-full btn-save" onclick="saveItem()">Save</button>
        </div>
    </div>
</div>

<!-- SEND MODAL -->
<div id="send-modal" class="modal-overlay">
    <div class="modal-card">
        <h2 style="margin-top:0;">Send Payment</h2>
        <p style="color:var(--text-dim); margin-top:0;" id="send-subtitle">Sending...</p>
        
        <label class="modal-label">Recipient Address</label>
        <div style="display:flex; gap:10px;">
            <input type="text" id="send-addr" class="modal-input" placeholder="0x... or 1..." style="margin-bottom:0;">
            <button class="btn-scan" onclick="startScanner('send')">📷</button>
        </div>
        
        <label class="modal-label">Amount</label>
        <input type="number" id="send-amount" class="modal-input" placeholder="0.00" step="0.000001">
        
        <div class="modal-actions">
            <button class="btn-full btn-cancel" onclick="document.getElementById('send-modal').style.display='none'">Cancel</button>
            <button class="btn-full btn-primary" onclick="confirmSend()">Confirm Send</button>
        </div>
    </div>
</div>

<div id="fs-barcode-overlay" onclick="closeFullscreenBarcode()"><div class="fs-barcode-container"><svg id="fs-barcode-svg"></svg><div id="fs-barcode-text"></div></div></div>
<div id="qr-addr-overlay" onclick="document.getElementById('qr-addr-overlay').style.display='none'" style="position:fixed; top:0; left:0; right:0; bottom:0; background:white; z-index:10001; display:none; align-items:center; justify-content:center; flex-direction:column;"><div id="addr-qr-canvas"></div><div style="margin-top:20px; color:black; font-weight:bold;">Wallet Address</div></div>
<div id="scanner-modal"><div style="position:absolute; top:0; width:100%; padding:20px; background:rgba(0,0,0,0.5); color:white; text-align:center; z-index:2;">Scan</div><div id="reader" style="width:100%; height:100%;"></div><button class="close-scan" onclick="stopScanner()" style="position:absolute; bottom:40px; left:50%; transform:translateX(-50%); background:white; padding:12px 30px; border-radius:30px; border:none; font-weight:bold; z-index:3;">Cancel</button></div>

<script>
const apiRoot = "<?php echo esc_url_raw(rest_url('lollms/v1/')); ?>";
const nonce = "<?php echo wp_create_nonce('wp_rest'); ?>";

// State
let currentApp = 'launcher';
let editorImageBase64 = null;
let html5QrcodeScanner = null;
let calendarInstance = null;
let mapInstance = null;
let qrScannedContent = null;
// Rates Store
let fetchedRates = { BTC: 0, ETH: 0 }; 
let fiatRates = { USD: 1, EUR: 0.92, GBP: 0.79, TND: 3.10, JPY: 148, CAD: 1.35, AUD: 1.52, CHF: 0.88, CNY: 7.19 }; // Defaults

function goHome() {
    document.getElementById('launcher-view').style.display = 'block'; document.getElementById('app-nav').style.display = 'none';
    ['app-grocery', 'app-cards', 'app-coupons', 'app-calendar', 'app-qr', 'app-crypto', 'app-converter'].forEach(id => document.getElementById(id).style.display = 'none');
}

function openApp(app) {
    document.getElementById('launcher-view').style.display = 'none'; document.getElementById('app-nav').style.display = 'flex';
    ['app-grocery', 'app-cards', 'app-coupons', 'app-calendar', 'app-qr', 'app-crypto', 'app-converter'].forEach(id => document.getElementById(id).style.display = 'none');
    document.getElementById('app-'+app).style.display = 'block';
    const titles = { 'grocery': 'Groceries', 'cards': 'Cards', 'coupons': 'Coupons', 'calendar': 'Calendar', 'qr': 'QR Tools', 'crypto': 'Wallet', 'converter': 'Converter' };
    document.getElementById('app-title').innerText = titles[app];
    const addBtn = document.getElementById('btn-header-add');
    if(['grocery','qr','crypto','converter'].includes(app)) addBtn.style.visibility = 'hidden'; else { addBtn.style.visibility = 'visible'; addBtn.onclick=()=>openEditor(app==='calendar'?'calendar':(app==='coupons'?'coupon':'wallet')); }
    if(app==='grocery') loadGrocery(); if(app==='cards') loadCards(); if(app==='coupons') loadCoupons(); if(app==='calendar') loadCalendar(); if(app==='qr') loadQR(); if(app==='crypto') loadCrypto(); if(app==='converter') initConverter();
}

// --- CONVERTER ---
const units = {
    currency: ['USD', 'EUR', 'TND', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF', 'CNY', 'BTC', 'ETH'],
    length: ['m', 'km', 'ft', 'mi', 'cm', 'in'],
    weight: ['kg', 'g', 'lb', 'oz'],
    temp: ['C', 'F', 'K']
};
let currentConvCat = 'currency';

async function initConverter() {
    await fetchFiatRates();
    switchConvTab('currency');
}

async function fetchFiatRates() {
    try {
        const res = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
        const data = await res.json();
        if(data && data.rates) {
            fiatRates = data.rates; 
            
            // Render Ticker
            const ticker = document.getElementById('live-currency-ticker');
            let html = '';
            const show = ['TND', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD'];
            show.forEach(c => {
                if(fiatRates[c]) html += `<div class="ticker-item"><span class="ticker-label">USD/${c}</span><span class="ticker-val">${fiatRates[c].toFixed(3)}</span></div>`;
            });
            ticker.innerHTML = html;
        }
    } catch(e) { console.log('Fiat rates failed', e); }
}

function switchConvTab(cat) {
    currentConvCat = cat;
    document.querySelectorAll('#app-converter .app-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-conv-'+cat).classList.add('active');
    
    const sel1 = document.getElementById('conv-sel-1');
    const sel2 = document.getElementById('conv-sel-2');
    sel1.innerHTML = ''; sel2.innerHTML = '';
    
    units[cat].forEach(u => {
        sel1.add(new Option(u, u));
        sel2.add(new Option(u, u));
    });
    
    if(cat==='currency') { sel1.value='USD'; sel2.value='TND'; }
    if(cat==='length') { sel1.value='m'; sel2.value='ft'; }
    if(cat==='weight') { sel1.value='kg'; sel2.value='lb'; }
    if(cat==='temp') { sel1.value='C'; sel2.value='F'; }
    
    convert('1');
}

function convert(source) {
    const val1 = parseFloat(document.getElementById('conv-in-1').value);
    const val2 = parseFloat(document.getElementById('conv-in-2').value);
    const u1 = document.getElementById('conv-sel-1').value;
    const u2 = document.getElementById('conv-sel-2').value;
    
    if(isNaN(val1) && source==='1') return;
    if(isNaN(val2) && source==='2') return;

    let res = 0;
    
    if (currentConvCat === 'currency') {
        const getRate = (code) => {
            if(code === 'USD') return 1;
            if(fiatRates[code]) return fiatRates[code]; 
            if(code === 'BTC') return fetchedRates.BTC ? (1 / fetchedRates.BTC) : 0; 
            if(code === 'ETH') return fetchedRates.ETH ? (1 / fetchedRates.ETH) : 0;
            return 1;
        };

        const rate1 = getRate(u1); 
        const rate2 = getRate(u2); 
        
        if(source==='1') res = (val1 / rate1) * rate2;
        else res = (val2 / rate2) * rate1;
    }
    else if (currentConvCat === 'length') {
        const toM = { m:1, km:1000, cm:0.01, ft:0.3048, in:0.0254, mi:1609.34 };
        if(source==='1') res = val1 * toM[u1] / toM[u2];
        else res = val2 * toM[u2] / toM[u1];
    }
    else if (currentConvCat === 'weight') {
        const toKg = { kg:1, g:0.001, lb:0.453592, oz:0.0283495 };
        if(source==='1') res = val1 * toKg[u1] / toKg[u2];
        else res = val2 * toKg[u2] / toKg[u1];
    }
    else if (currentConvCat === 'temp') {
        const kVal = (u, v) => (u=='C'? v+273.15 : (u=='F'? (v-32)*5/9+273.15 : v));
        const fromK = (u, k) => (u=='C'? k-273.15 : (u=='F'? (k-273.15)*9/5+32 : k));
        if(source==='1') res = fromK(u2, kVal(u1, val1));
        else res = fromK(u1, kVal(u2, val2));
    }

    if(source==='1') document.getElementById('conv-in-2').value = (Math.round(res*100000)/100000);
    else document.getElementById('conv-in-1').value = (Math.round(res*100000)/100000);
}

// --- IMPORT / EXPORT CARDS ---
async function exportCards() {
    const res = await fetch(`${apiRoot}items/wallet`, { headers: { 'X-WP-Nonce': nonce } });
    const items = await res.json();
    const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(items));
    const downloadAnchorNode = document.createElement('a');
    downloadAnchorNode.setAttribute("href", dataStr);
    downloadAnchorNode.setAttribute("download", "lollms_cards_" + new Date().toISOString().slice(0,10) + ".json");
    document.body.appendChild(downloadAnchorNode);
    downloadAnchorNode.click();
    downloadAnchorNode.remove();
}

function importCards() { document.getElementById('import-file').click(); }

async function handleImportFile(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = async function(e) {
        try {
            const items = JSON.parse(e.target.result);
            if (!Array.isArray(items)) throw new Error("Invalid format");
            if(!confirm(`Import ${items.length} cards? Duplicates might be created.`)) return;
            let count = 0;
            for (const item of items) {
                await apiAdd({
                    name: item.item_name, type: 'wallet', barcode: item.barcode,
                    description: item.description, image_data: item.image_data
                });
                count++;
            }
            alert(`Imported ${count} cards successfully.`);
            loadCards();
        } catch (err) { alert("Error parsing JSON: " + err.message); }
    };
    reader.readAsText(file);
    input.value = ''; 
}

// --- CRYPTO ---
async function loadCrypto() {
    const list = document.getElementById('crypto-list'); list.innerHTML = '<div style="color:#888; text-align:center;">Syncing...</div>';
    fetchPrices();
    const res = await fetch(`${apiRoot}items/crypto`, { headers: { 'X-WP-Nonce': nonce } });
    const items = await res.json(); list.innerHTML = '';
    if(items.length===0) list.innerHTML = '<div style="text-align:center; padding:20px; color:var(--text-dim);">No wallets. Add one below.</div>';
    
    for (const item of items) {
        let balance = '0.00'; let icon = 'btc'; let ticker = 'BTC';
        if (item.item_name.includes('ETH')) { icon = 'eth'; ticker = 'ETH'; }
        if (item.item_name.includes('LOL')) { icon = 'lol'; ticker = 'LOL'; }

        // Fetch Balance
        if (ticker === 'BTC' && item.barcode) {
            try { 
                const r = await fetch(`https://blockchain.info/q/addressbalance/${item.barcode}?cors=true`); 
                if(r.ok) balance = (parseInt(await r.text())/100000000).toFixed(8);
            } catch(e){}
        } else if (ticker === 'ETH' && item.barcode) {
            try {
                const r = await fetch('https://cloudflare-eth.com', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({jsonrpc:"2.0",method:"eth_getBalance",params:[item.barcode,"latest"],id:1}) });
                const d = await r.json();
                if(d.result) balance = (parseInt(d.result, 16)/1e18).toFixed(5);
            } catch(e){}
        }

        const div = document.createElement('div'); div.className = 'crypto-card';
        div.innerHTML = `<div class="crypto-icon ${icon}">$</div><div class="crypto-info"><div class="crypto-name">${item.item_name}</div><div class="crypto-address">${item.barcode.substring(0,16)}...</div></div><div class="crypto-balance">${balance} <span style="font-size:0.8rem;">${ticker}</span></div>`;
        div.onclick = () => openEditor('crypto', item, ticker);
        list.appendChild(div);
    }
}

async function fetchPrices() {
    try {
        const r = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum&vs_currencies=usd');
        const d = await r.json();
        if(d.bitcoin) { 
            fetchedRates.BTC = d.bitcoin.usd;
            document.getElementById('price-btc').innerText = '$' + d.bitcoin.usd.toLocaleString();
        }
        if(d.ethereum) { 
            fetchedRates.ETH = d.ethereum.usd;
            document.getElementById('price-eth').innerText = '$' + d.ethereum.usd.toLocaleString();
        }
    } catch(e) {}
}

async function connectMetaMask() {
    if (typeof window.ethereum !== 'undefined') {
        try {
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            const account = accounts[0];
            await apiAdd({ name: "MetaMask Wallet", type: "crypto", barcode: account, description: "Imported via MetaMask" });
            alert("MetaMask Wallet Connected!");
            loadCrypto();
        } catch (error) { alert("Connection failed or rejected."); }
    } else { alert("MetaMask is not installed."); }
}

async function fetchHistory(address, type) {
    const cont = document.getElementById('tx-history-list'); cont.innerHTML = 'Loading...';
    let html = '';
    try {
        if (type === 'BTC') {
            const r = await fetch(`https://blockchain.info/rawaddr/${address}?limit=5&cors=true`);
            const d = await r.json();
            d.txs.forEach(tx => {
                const sent = tx.inputs.some(i => i.prev_out.addr === address);
                const val = sent ? tx.inputs.find(i => i.prev_out.addr === address).prev_out.value : tx.out.find(o => o.addr === address).value;
                const dir = sent ? 'out' : 'in';
                html += `<div class="tx-item ${dir}"><span>${dir=='in'?'↓ Recv':'↑ Sent'} ${(val/100000000).toFixed(6)} BTC</span><span class="tx-hash">${new Date(tx.time*1000).toLocaleDateString()}</span></div>`;
            });
        } else if (type === 'ETH') {
            const r = await fetch(`https://api.blockcypher.com/v1/eth/main/addrs/${address}`);
            const d = await r.json();
            if(d.txrefs) {
                d.txrefs.slice(0,5).forEach(tx => {
                    const dir = (tx.tx_input_n === -1) ? 'in' : 'out'; 
                    html += `<div class="tx-item ${dir}"><span>${dir=='in'?'↓ Recv':'↑ Sent'} ${(tx.value/1e18).toFixed(5)} ETH</span><span class="tx-hash">${new Date(tx.confirmed).toLocaleDateString()}</span></div>`;
                });
            } else html = '<div style="padding:10px; text-align:center; color:#666;">No recent transactions.</div>';
        }
    } catch(e) { html = '<div style="padding:10px; text-align:center; color:#666;">History unavailable.</div>'; }
    cont.innerHTML = html || '<div style="padding:10px; text-align:center; color:#666;">No recent transactions found.</div>';
}

// --- SEND LOGIC ---
function openSendModal() {
    const subtype = document.getElementById('edit-subtype').value;
    document.getElementById('send-subtitle').innerText = "Sending " + subtype;
    document.getElementById('send-addr').value = '';
    document.getElementById('send-amount').value = '';
    document.getElementById('send-modal').style.display = 'flex';
}

async function confirmSend() {
    const subtype = document.getElementById('edit-subtype').value;
    const addr = document.getElementById('send-addr').value;
    const amount = document.getElementById('send-amount').value;
    const privKey = document.getElementById('edit-description').value; // Stored in desc
    const myAddr = document.getElementById('edit-code').value;

    if(!addr || !amount) return alert("Please fill all fields");

    // 1. ETH Sending (Local Key or MetaMask)
    if (subtype === 'ETH' || subtype === 'LOL') {
        // A. MetaMask Send
        if (privKey === "Imported via MetaMask" && typeof window.ethereum !== 'undefined') {
            const weiAmount = BigInt(Math.floor(amount * 1e18)).toString(16);
            try {
                await window.ethereum.request({
                    method: 'eth_sendTransaction',
                    params: [{ from: myAddr, to: addr, value: weiAmount }]
                });
                alert("Transaction sent via MetaMask!");
                document.getElementById('send-modal').style.display = 'none';
            } catch(e) { alert("MetaMask Error: " + e.message); }
        } 
        // B. Local Private Key Send (Using Ethers.js + Cloudflare RPC)
        else if (privKey.length === 66 || privKey.length === 64) {
            try {
                const provider = new ethers.JsonRpcProvider('https://cloudflare-eth.com');
                const wallet = new ethers.Wallet(privKey, provider);
                const tx = await wallet.sendTransaction({
                    to: addr,
                    value: ethers.parseEther(amount.toString())
                });
                alert("Sent! Hash: " + tx.hash);
                document.getElementById('send-modal').style.display = 'none';
            } catch(e) { alert("Send Error: " + e.message); }
        } else {
            alert("No valid private key or MetaMask connection found.");
        }
    } 
    // 2. BTC Sending (Fallback to App Link)
    else if (subtype === 'BTC') {
        const confirmMsg = "For security, we will launch your native Bitcoin wallet to complete this transaction.\n\nSend " + amount + " BTC to " + addr + "?";
        if (confirm(confirmMsg)) {
            window.location.href = `bitcoin:${addr}?amount=${amount}`;
            document.getElementById('send-modal').style.display = 'none';
        }
    }
}

// --- GENERATION & DETECTION ---
function detectWalletType() {
    const code = document.getElementById('edit-code').value;
    const name = document.getElementById('edit-name');
    // ETH Address
    if (code.startsWith('0x') && code.length === 42) {
        document.getElementById('edit-subtype').value = 'ETH';
        if(!name.value) name.value = "Imported ETH Wallet";
    } 
    // BTC Address
    else if (code.startsWith('1') || code.startsWith('3') || code.startsWith('bc1')) {
        document.getElementById('edit-subtype').value = 'BTC';
        if(!name.value) name.value = "Imported BTC Wallet";
    }
    // ETH Priv Key (64 chars hex)
    if (/^[0-9a-fA-F]{64}$/.test(code)) {
        if(typeof ethers !== 'undefined') {
            try {
                const w = new ethers.Wallet(code);
                document.getElementById('edit-code').value = w.address;
                document.getElementById('edit-description').value = w.privateKey;
                document.getElementById('edit-subtype').value = 'ETH';
                if(!name.value) name.value = "Imported ETH Key";
                alert("Private Key Detected! Address derived.");
            } catch(e){}
        }
    }
}

function generateWalletAddress() {
    const subtype = document.getElementById('edit-subtype').value;
    const nameInput = document.getElementById('edit-name');
    const codeInput = document.getElementById('edit-code');
    const descInput = document.getElementById('edit-description');
    const privDisplay = document.getElementById('privkey-display');
    
    if (subtype === 'ETH' || subtype === 'LOL') {
        if (typeof ethers !== 'undefined') {
            const w = ethers.Wallet.createRandom();
            codeInput.value = w.address; descInput.value = w.privateKey; privDisplay.innerText = w.privateKey; privDisplay.style.display = 'block';
            if(!nameInput.value) nameInput.value = "New ETH Wallet";
        } else alert("Ethers.js not ready.");
    } else if (subtype === 'BTC') {
        if (typeof bitcoin !== 'undefined') {
            const kp = bitcoin.ECPair.makeRandom();
            const { address } = bitcoin.payments.p2pkh({ pubkey: kp.publicKey });
            codeInput.value = address;
            descInput.value = kp.toWIF();
            privDisplay.innerText = kp.toWIF();
            privDisplay.style.display = 'block';
            if(!nameInput.value) nameInput.value = "New BTC Wallet";
        } else alert("BitcoinJS not ready.");
    }
}

// --- EDITOR ---
function openEditor(type, item = null, subType = null) {
    document.getElementById('edit-id').value = item ? item.id : '';
    let dbType = type === 'cards' ? 'wallet' : type;
    document.getElementById('edit-type').value = dbType;
    const computedSubtype = subType || (item ? (item.item_name.includes('ETH')?'ETH':'BTC') : '');
    document.getElementById('edit-subtype').value = computedSubtype;

    document.getElementById('edit-name').value = item ? item.item_name : (subType ? subType + ' Wallet' : '');
    document.getElementById('edit-code').value = item ? item.barcode : '';
    document.getElementById('edit-price').value = item ? item.price : '';
    document.getElementById('edit-description').value = item ? item.description : ''; 
    document.getElementById('edit-photo').value = ''; 
    
    const isCrypto = type === 'crypto';
    document.getElementById('field-image-container').style.display = (type === 'cards') ? 'block' : 'none';
    document.getElementById('field-barcode-container').style.display = (['cards','coupon','crypto'].includes(type)) ? 'block' : 'none';
    document.getElementById('field-calendar-container').style.display = (type === 'calendar') ? 'block' : 'none';
    document.getElementById('field-value-container').style.display = (type === 'coupon') ? 'block' : 'none';
    
    document.getElementById('btn-gen-wallet').style.display = (isCrypto && !item) ? 'block' : 'none';
    document.getElementById('field-privkey-container').style.display = isCrypto ? 'block' : 'none';
    
    // Key display
    const savedKey = (item && item.description && (item.description.startsWith('0x') || item.description.startsWith('L') || item.description.startsWith('K') || item.description.startsWith('5'))) ? item.description : '';
    document.getElementById('privkey-display').innerText = savedKey; 
    document.getElementById('privkey-display').style.display = 'none';
    
    // QR / Barcode visibility
    document.getElementById('btn-show-code').style.display = (item && item.barcode && !isCrypto) ? 'block' : 'none';
    document.getElementById('btn-show-qr-addr').style.display = (isCrypto && item && item.barcode) ? 'block' : 'none';

    // History
    const historyCont = document.getElementById('field-history-container');
    historyCont.style.display = (isCrypto && item) ? 'block' : 'none';
    if(isCrypto && item) fetchHistory(item.barcode, computedSubtype);

    document.getElementById('editor-modal').style.display = 'flex';
    if (type === 'calendar') setTimeout(()=> { mapInstance = L.map('modal-map').setView([48.8566, 2.3522], 13); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapInstance); }, 200);
}

function closeEditor() { document.getElementById('editor-modal').style.display = 'none'; }

function showAddrQR() {
    const addr = document.getElementById('edit-code').value;
    if(!addr) return;
    document.getElementById('qr-addr-overlay').style.display = 'flex';
    document.getElementById('addr-qr-canvas').innerHTML = '';
    new QRCode(document.getElementById('addr-qr-canvas'), { text: addr, width: 256, height: 256 });
}

async function saveItem() {
    const id = document.getElementById('edit-id').value;
    const type = document.getElementById('edit-type').value; 
    const data = {
        name: document.getElementById('edit-name').value, 
        item_name: document.getElementById('edit-name').value, 
        type: type,
        barcode: document.getElementById('edit-code').value,
        price: document.getElementById('edit-price').value,
        description: document.getElementById('edit-description').value,
        image_data: editorImageBase64
    };
    if (type === 'calendar') {
        data.date = document.getElementById('edit-start').value;
        data.end_date = document.getElementById('edit-end').value;
        data.location = document.getElementById('edit-location').value;
        data.attendees = document.getElementById('edit-attendees').value;
    }
    if (id) { data.id = id; await fetch(`${apiRoot}update`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify(data) }); } 
    else { await fetch(`${apiRoot}add`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify(data) }); }
    closeEditor();
    if (type === 'wallet') loadCards(); if (type === 'crypto') loadCrypto(); if (type === 'coupon') loadCoupons(); if (type === 'calendar') loadCalendar();
}

// ... [Existing CRUD, grocery, cards, qr logic preserved] ...
async function deleteFromEditor() {
    const id = document.getElementById('edit-id').value;
    const type = document.getElementById('edit-type').value;
    if(!id || !confirm('Delete?')) return;
    await fetch(`${apiRoot}delete`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify({ id: id }) });
    closeEditor();
    if (type === 'wallet') loadCards(); if (type === 'crypto') loadCrypto(); if (type === 'coupon') loadCoupons(); if (type === 'calendar') loadCalendar();
}
async function loadGrocery() {
    const list = document.getElementById('grocery-list'); list.innerHTML = 'Loading...';
    const [resItems, resHist] = await Promise.all([ fetch(`${apiRoot}items/grocery`, { headers: { 'X-WP-Nonce': nonce } }), fetch(`${apiRoot}suggestions`, { headers: { 'X-WP-Nonce': nonce } }) ]);
    const items = await resItems.json(); list.innerHTML = '';
    items.forEach(item => { list.innerHTML += `<li class="todo-item"><div class="todo-checkbox" onclick="toggleItem(${item.id})"></div><div class="item-details"><span class="item-name">${item.item_name}</span></div><div style="display:flex; gap:5px;"><input class="inline-input qty" value="${item.quantity}" onchange="updateItem(${item.id},'quantity',this.value)"><input class="inline-input price" value="${item.price}" onchange="updateItem(${item.id},'price',this.value)"></div></li>`; });
    let t = 0; document.querySelectorAll('.inline-input.price').forEach(i => t += (parseFloat(i.value)||0)); document.getElementById('grand-total').innerText = '$' + t.toFixed(2);
    const hList = document.getElementById('grocery-history'); hList.innerHTML = '';
    (await resHist.json()).forEach(item => { if(item.list_type === 'grocery') hList.innerHTML += `<li class="history-item"><span>${item.item_name}</span><button class="btn-icon" style="color:var(--success);" onclick="restoreItem('${item.item_name.replace(/'/g,"\\'")}','${item.quantity}','${item.price}')">+</button></li>`; });
}
async function apiAdd(data) { return fetch(`${apiRoot}add`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify(data) }); }
async function toggleItem(id) { await fetch(`${apiRoot}toggle`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify({ id: id }) }); loadGrocery(); }
async function restoreItem(n,q,p) { await apiAdd({name:n,type:'grocery',qty:q,price:p}); loadGrocery(); }
async function updateItem(id,f,v) { await fetch(`${apiRoot}update`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify({ id: id, field: f, value: v }) }); loadGrocery(); }
function handleGroceryEnter(e) { if(e.key === 'Enter') addGrocery(); }
async function addGrocery() { const n = document.getElementById('g-name').value; if(!n) return; await apiAdd({name:n,type:'grocery',qty:document.getElementById('g-qty').value,price:document.getElementById('g-price').value}); document.getElementById('g-name').value=''; loadGrocery(); }
async function loadCards() { const g=document.getElementById('cards-grid'); g.innerHTML='Loading...'; const r=await fetch(`${apiRoot}items/wallet`,{headers:{'X-WP-Nonce':nonce}}); const i=await r.json(); g.innerHTML=''; i.forEach(it=>{ const c=document.createElement('div'); c.className='wallet-card'; if(it.image_data)c.innerHTML=`<img src="${it.image_data}">`; c.innerHTML+=`<div class="wallet-content"><div class="wallet-title">${it.item_name}</div><div class="wallet-code">${it.barcode||'****'}</div></div>`; c.onclick=()=>openEditor('cards',it); g.appendChild(c); }); }
async function loadCoupons() { const l=document.getElementById('coupons-list'); l.innerHTML='Loading...'; const r=await fetch(`${apiRoot}items/coupon`,{headers:{'X-WP-Nonce':nonce}}); const i=await r.json(); l.innerHTML=''; i.forEach(it=>{ const d=document.createElement('div'); d.className='coupon-card'; if(parseFloat(it.price)<=0.01)d.style.filter='grayscale(1)'; d.innerHTML=`<div class="coupon-top"><div><div style="font-weight:bold;">${it.item_name}</div><small>${it.barcode||'Tap to edit'}</small></div><div class="coupon-val">$${parseFloat(it.price).toFixed(2)}</div></div><div class="coupon-rip"></div>`; d.onclick=()=>openEditor('coupon',it); l.appendChild(d); }); }
function switchQRTab(tab) { ['scan', 'gen', 'lib'].forEach(t => { document.getElementById('tab-qr-'+t).classList.remove('active'); document.getElementById('view-qr-'+t).style.display = 'none'; }); document.getElementById('tab-qr-'+tab).classList.add('active'); document.getElementById('view-qr-'+tab).style.display = 'block'; if(tab==='lib') loadQRLibrary(); }
async function loadQRLibrary() { const grid=document.getElementById('qr-library-grid'); grid.innerHTML='Loading...'; const res=await fetch(`${apiRoot}items/qrcode`,{headers:{'X-WP-Nonce':nonce}}); const items=await res.json(); grid.innerHTML=''; items.forEach(item=>{ grid.innerHTML+=`<div class="qr-card"><img src="${item.image_data}"><div style="padding:10px; background:#f1f5f9; font-weight:bold;">${item.item_name}</div><button class="qr-card-btn-del" onclick="deleteQR(${item.id})">Del</button></div>`; }); }
async function deleteQR(id) { if(!confirm("Delete?")) return; await fetch(`${apiRoot}delete`,{method:'POST',headers:{'Content-Type':'application/json','X-WP-Nonce':nonce},body:JSON.stringify({id:id})}); loadQRLibrary(); }
function startScanner(app) { document.getElementById('scanner-modal').style.display='flex'; html5QrcodeScanner=new Html5Qrcode("reader"); html5QrcodeScanner.start({facingMode:"environment"},{fps:15,qrbox:250},(t)=>{ stopScanner(); if(app==='editor') { document.getElementById('edit-code').value=t; detectWalletType(); } else if(app==='send') { document.getElementById('send-addr').value=t; } else if(app==='grocery') document.getElementById('g-name').value="Scanned: "+t; }); }
function stopScanner() { if(html5QrcodeScanner) html5QrcodeScanner.stop().then(()=>document.getElementById('scanner-modal').style.display='none'); }
function showFullscreenBarcode() { const c=document.getElementById('edit-code').value; if(!c)return; document.getElementById('fs-barcode-overlay').style.display='flex'; document.getElementById('fs-barcode-text').innerText=c; try{JsBarcode("#fs-barcode-svg",c,{displayValue:false});}catch(e){} }
function closeFullscreenBarcode() { document.getElementById('fs-barcode-overlay').style.display='none'; }
function loadCalendar() { const calendarEl=document.getElementById('calendar-container'); if(calendarInstance)calendarInstance.destroy(); calendarInstance=new FullCalendar.Calendar(calendarEl,{initialView:'dayGridMonth',headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek,timeGridDay'},height:'auto',contentHeight:600,editable:true,selectable:true,events:async function(info,successCallback){const res=await fetch(`${apiRoot}items/calendar`,{headers:{'X-WP-Nonce':nonce}});const data=await res.json();successCallback(data.map(item=>({id:item.id,title:item.item_name,start:item.due_date,end:item.end_date,extendedProps:{description:item.description,location:item.location,attendees:item.attendees}})));},dateClick:function(info){openEditor('calendar');setTimeout(()=>{document.getElementById('edit-start').value=info.dateStr+'T09:00';},50);},eventClick:function(info){openEditor('calendar',{id:info.event.id,item_name:info.event.title,due_date:info.event.startStr,end_date:info.event.endStr,description:info.event.extendedProps.description,location:info.event.extendedProps.location,attendees:info.event.extendedProps.attendees});}});calendarInstance.render(); }
function handleEditorImage() { const file=document.getElementById('edit-photo').files[0]; if(file){const reader=new FileReader();reader.onloadend=function(){editorImageBase64=reader.result;document.getElementById('edit-photo-box').style.backgroundImage=`url(${editorImageBase64})`;document.getElementById('edit-photo-label').style.display='none';};reader.readAsDataURL(file);} }
</script>

<?php get_footer(); ?>
