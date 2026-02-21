<!-- SYSTEM MODALS -->

<!-- 1. EDITOR MODAL -->
<div id="editor-modal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h2 id="editor-title">Edit Item</h2>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-id"><input type="hidden" id="edit-type"><input type="hidden" id="edit-subtype">

            <div id="field-image-container" style="display:none; margin-bottom:20px;">
            <label class="modal-label">Image</label>
            <label id="edit-photo-box" class="photo-uploader">
                <span id="edit-photo-label" style="color:var(--text-dim);">Tap to snap / upload</span>
                <input type="file" id="edit-photo" accept="image/*" capture="environment" style="display:none;" onchange="handleEditorImage()">
            </label>
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

        <div id="field-privkey-container" style="display:none;">
            <label class="modal-label" style="color:var(--danger);">Private Key (Saved Locally)</label>
            <div style="background:rgba(239, 68, 68, 0.1); padding:10px; border-radius:8px; word-break:break-all; font-family:monospace; color:var(--danger); font-size:0.8rem; display:none;" id="privkey-display"></div>
            <button class="btn-full" onclick="document.getElementById('privkey-display').style.display='block'" style="background:transparent; border:1px solid var(--danger); color:var(--danger); margin-top:5px; font-size:0.8rem;">⚠️ Reveal Key</button>
            <button class="btn-full" style="background:linear-gradient(135deg, #10b981, #059669); color:white; margin-top:20px; font-weight:bold; padding:15px;" onclick="openSendModal()">💸 SEND / PAY</button>
        </div>
        
        <div id="field-history-container" style="display:none;">
            <label class="modal-label">Recent Transactions</label>
            <div id="tx-history-list" style="margin-top:10px; border-top:1px solid var(--border);"></div>
        </div>

        <div id="field-calendar-container" style="display:none;">
            <div style="display:flex; gap:15px;">
                <div style="flex:1;"><label class="modal-label">Start</label><input type="datetime-local" id="edit-start" class="modal-input"></div>
                <div style="flex:1;"><label class="modal-label">End</label><input type="datetime-local" id="edit-end" class="modal-input"></div>
            </div>
            <label class="modal-label">Location</label><input type="text" id="edit-location" class="modal-input">
            <div id="modal-map"></div>
            <label class="modal-label">Description</label><textarea id="edit-description" class="modal-input" rows="3"></textarea>
        </div>
        
            <div id="field-value-container" style="display:none;"><label class="modal-label">Value ($)</label><input type="number" id="edit-price" class="modal-input" step="0.01"></div>
        </div>
        <div class="modal-actions">
            <button class="btn-full btn-delete" id="btn-editor-delete" onclick="deleteFromEditor()" style="display:none;">Delete</button>
            <button class="btn-full btn-cancel" onclick="closeEditor()">Cancel</button>
            <button class="btn-full btn-save" onclick="saveItem()">Save</button>
        </div>
    </div>
</div>

<!-- 2. SEND MODAL -->
<div id="send-modal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h2 style="margin-top:0; margin-bottom:5px;">Send Payment</h2>
            <p style="color:var(--text-dim); margin:0;" id="send-subtitle">Sending...</p>
        </div>
        <div class="modal-body">
            <label class="modal-label">Recipient Address</label>
        <div style="display:flex; gap:10px;">
            <input type="text" id="send-addr" class="modal-input" placeholder="0x... or 1..." style="margin-bottom:0;">
            <button class="btn-scan" onclick="startScanner('send')">📷</button>
        </div>
            <label class="modal-label">Amount</label>
            <input type="number" id="send-amount" class="modal-input" placeholder="0.00" step="0.000001">
        </div>
        <div class="modal-actions">
            <button class="btn-full btn-cancel" onclick="document.getElementById('send-modal').style.display='none'">Cancel</button>
            <button class="btn-full btn-primary" onclick="confirmSend()">Confirm Send</button>
        </div>
    </div>
</div>

<!-- 3. SCANNER & OVERLAYS -->
<div id="fs-barcode-overlay" onclick="closeFullscreenBarcode()">
    <div class="barcode-rotate-wrapper">
        <svg id="fs-barcode-svg"></svg>
        <div id="fs-barcode-text"></div>
    </div>
</div>

<div id="qr-addr-overlay" onclick="document.getElementById('qr-addr-overlay').style.display='none'">
    <div id="addr-qr-canvas"></div>
    <div style="margin-top:20px; color:black; font-weight:bold;">Wallet Address</div>
</div>

<div id="scanner-modal">
    <div class="scanner-header">Point Camera at Code</div>
    <div class="scanner-body">
        <div id="reader" style="width:100%; max-width:400px; border-radius:12px; overflow:hidden;"></div>
    </div>
    <div class="scanner-footer">
        <button class="close-scan" onclick="stopScanner()">Cancel</button>
    </div>
</div>

<script>
// --- INIT ---
document.addEventListener("DOMContentLoaded", () => {
    // Relocate modals to body to break out of any scrollable container traps
    const mIds = ['editor-modal', 'send-modal', 'scanner-modal', 'fs-barcode-overlay', 'qr-addr-overlay'];
    mIds.forEach(id => {
        const el = document.getElementById(id);
        if(el) document.body.appendChild(el);
    });
});

// --- EDITOR CORE ---
function openEditor(type, item = null, subType = null) {
    document.getElementById('edit-id').value = item ? item.id : '';
    let dbType = type === 'cards' || type === 'generic' ? 'wallet' : type;
    document.getElementById('edit-type').value = dbType;
    const computedSubtype = subType || (item ? (item.item_name.includes('ETH')?'ETH':'BTC') : '');
    document.getElementById('edit-subtype').value = computedSubtype;

    document.getElementById('edit-name').value = item ? item.item_name : (subType ? subType + ' Wallet' : '');
    document.getElementById('edit-code').value = item ? item.barcode : '';
    document.getElementById('edit-price').value = item ? item.price : '';
    document.getElementById('edit-description').value = item ? item.description : ''; 
    document.getElementById('edit-photo').value = ''; 
    document.getElementById('edit-photo-box').style.backgroundImage = (item && item.image_data) ? `url(${item.image_data})` : 'none';
    editorImageBase64 = (item && item.image_data) ? item.image_data : null;
    
    const isCrypto = type === 'crypto';
    const isCard = type === 'cards' || type === 'generic' || type === 'wallet';
    
    document.getElementById('field-image-container').style.display = isCard ? 'block' : 'none';
    document.getElementById('field-barcode-container').style.display = (isCard || isCrypto || type==='coupon') ? 'block' : 'none';
    document.getElementById('field-calendar-container').style.display = (type === 'calendar') ? 'block' : 'none';
    document.getElementById('field-value-container').style.display = (type === 'coupon') ? 'block' : 'none';
    document.getElementById('btn-gen-wallet').style.display = (isCrypto && !item) ? 'block' : 'none';
    document.getElementById('field-privkey-container').style.display = isCrypto ? 'block' : 'none';
    document.getElementById('btn-show-code').style.display = (item && item.barcode && !isCrypto) ? 'block' : 'none';
    document.getElementById('btn-show-qr-addr').style.display = (isCrypto && item && item.barcode) ? 'block' : 'none';
    document.getElementById('field-history-container').style.display = (isCrypto && item) ? 'block' : 'none';
    if(isCrypto && item) fetchHistory(item.barcode, computedSubtype);

    document.getElementById('editor-modal').style.display = 'flex';
    if (type === 'calendar' && !mapInstance) setTimeout(()=> { mapInstance = L.map('modal-map').setView([48.8566, 2.3522], 13); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapInstance); }, 200);
}

function closeEditor() { document.getElementById('editor-modal').style.display = 'none'; }

async function saveItem() {
    const id = document.getElementById('edit-id').value;
    const type = document.getElementById('edit-type').value; 
    const data = {
        name: document.getElementById('edit-name').value, item_name: document.getElementById('edit-name').value, type: type,
        barcode: document.getElementById('edit-code').value, price: document.getElementById('edit-price').value,
        description: document.getElementById('edit-description').value, image_data: editorImageBase64
    };
    if (type === 'calendar') {
        data.date = document.getElementById('edit-start').value; data.end_date = document.getElementById('edit-end').value;
        data.location = document.getElementById('edit-location').value;
    }
    const endpoint = id ? `${apiRoot}update` : `${apiRoot}add`;
    if (id) data.id = id;
    await fetch(endpoint, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify(data) });
    closeEditor();
    if (type === 'wallet') loadCards(); if (type === 'crypto') loadCrypto(); if (type === 'coupon') loadCoupons(); if (type === 'calendar') loadCalendar();
}

async function deleteFromEditor() {
    const id = document.getElementById('edit-id').value;
    if(!id || !confirm('Delete?')) return;
    await fetch(`${apiRoot}delete`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify({ id: id }) });
    closeEditor();
    if (document.getElementById('app-cards').style.display === 'block') loadCards();
    if (document.getElementById('app-crypto').style.display === 'block') loadCrypto();
    if (document.getElementById('app-coupons').style.display === 'block') loadCoupons();
}

function handleEditorImage() { 
    const file=document.getElementById('edit-photo').files[0]; 
    if(file){const reader=new FileReader();reader.onloadend=function(){editorImageBase64=reader.result;document.getElementById('edit-photo-box').style.backgroundImage=`url(${editorImageBase64})`;document.getElementById('edit-photo-label').style.display='none';};reader.readAsDataURL(file);} 
}

// --- SCANNER & UTILS ---
function startScanner(app) { 
    document.getElementById('scanner-modal').style.display='flex'; 
    html5QrcodeScanner=new Html5Qrcode("reader"); 
    html5QrcodeScanner.start({facingMode:"environment"},{fps:15,qrbox:250,aspectRatio:1.0},(t)=>{ 
        stopScanner(); 
        if(app==='editor') { document.getElementById('edit-code').value=t; detectWalletType(); } 
        else if(app==='send') { document.getElementById('send-addr').value=t; } 
        else if(app==='grocery') { document.getElementById('g-name').value="Scanned: "+t; addGrocery(); } 
        else if(app==='qr') { document.getElementById('qr-scan-result').style.display='block'; document.getElementById('qr-content-text').innerText=t; qrScannedContent=t; } 
    }); 
}

function stopScanner() { if(html5QrcodeScanner) html5QrcodeScanner.stop().then(()=>document.getElementById('scanner-modal').style.display='none'); }

function showFullscreenBarcode() { 
    const c=document.getElementById('edit-code').value; if(!c)return; 
    document.getElementById('fs-barcode-overlay').style.display='flex'; 
    document.getElementById('fs-barcode-text').innerText=c; 
    try{JsBarcode("#fs-barcode-svg",c,{displayValue:false});}catch(e){} 
}
function closeFullscreenBarcode() { document.getElementById('fs-barcode-overlay').style.display='none'; }

function showAddrQR() {
    const addr = document.getElementById('edit-code').value;
    if(!addr) return;
    document.getElementById('qr-addr-overlay').style.display = 'flex'; document.getElementById('addr-qr-canvas').innerHTML = '';
    new QRCode(document.getElementById('addr-qr-canvas'), { text: addr, width: 256, height: 256 });
}

// --- WALLET UTILS ---
function detectWalletType() {
    const code = document.getElementById('edit-code').value;
    const name = document.getElementById('edit-name');
    if (code.startsWith('0x') && code.length === 42) {
        document.getElementById('edit-subtype').value = 'ETH';
        if(!name.value) name.value = "Imported ETH Wallet";
    } else if (code.startsWith('1') || code.startsWith('3') || code.startsWith('bc1')) {
        document.getElementById('edit-subtype').value = 'BTC';
        if(!name.value) name.value = "Imported BTC Wallet";
    }
    if (/^[0-9a-fA-F]{64}$/.test(code) && typeof ethers !== 'undefined') {
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
            codeInput.value = address; descInput.value = kp.toWIF(); privDisplay.innerText = kp.toWIF(); privDisplay.style.display = 'block';
            if(!nameInput.value) nameInput.value = "New BTC Wallet";
        } else alert("BitcoinJS not ready.");
    }
}

function openSendModal() {
    document.getElementById('send-subtitle').innerText = "Sending " + document.getElementById('edit-subtype').value;
    document.getElementById('send-addr').value = '';
    document.getElementById('send-amount').value = '';
    document.getElementById('send-modal').style.display = 'flex';
}

async function confirmSend() {
    const subtype = document.getElementById('edit-subtype').value;
    const addr = document.getElementById('send-addr').value;
    const amount = document.getElementById('send-amount').value;
    const privKey = document.getElementById('edit-description').value;
    const myAddr = document.getElementById('edit-code').value;

    if(!addr || !amount) return alert("Please fill all fields");

    if (subtype === 'ETH' || subtype === 'LOL') {
        if (privKey === "Imported via MetaMask" && typeof window.ethereum !== 'undefined') {
            const weiAmount = BigInt(Math.floor(amount * 1e18)).toString(16);
            try {
                await window.ethereum.request({ method: 'eth_sendTransaction', params: [{ from: myAddr, to: addr, value: weiAmount }] });
                alert("Transaction sent via MetaMask!"); document.getElementById('send-modal').style.display = 'none';
            } catch(e) { alert("MetaMask Error: " + e.message); }
        } else if (privKey.length === 66 || privKey.length === 64) {
            try {
                const provider = new ethers.JsonRpcProvider('https://cloudflare-eth.com');
                const wallet = new ethers.Wallet(privKey, provider);
                const tx = await wallet.sendTransaction({ to: addr, value: ethers.parseEther(amount.toString()) });
                alert("Sent! Hash: " + tx.hash); document.getElementById('send-modal').style.display = 'none';
            } catch(e) { alert("Send Error: " + e.message); }
        } else { alert("No valid private key or MetaMask connection."); }
    } else if (subtype === 'BTC') {
        if (confirm("Launch external wallet to send " + amount + " BTC?")) {
            window.location.href = `bitcoin:${addr}?amount=${amount}`;
            document.getElementById('send-modal').style.display = 'none';
        }
    }
}
</script>
