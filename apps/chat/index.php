<!-- APP: LOLLMS NODE LAUNCHER -->
<div id="app-chat" class="app-module" style="display:none; height:100%; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px;">
    
    <div style="font-size:4rem; margin-bottom:20px;">🤖</div>
    
    <h1 style="margin:0 0 10px 0;">Lollms Node</h1>
    <p style="color:var(--text-dim); margin-bottom:40px; max-width:400px;">
        Connect to your local or remote Large Language Model orchestration server.
    </p>

    <!-- STATUS CARD -->
    <div id="node-status-card" style="background:var(--surface); padding:20px; border-radius:16px; border:1px solid var(--border); width:100%; max-width:400px; margin-bottom:20px;">
        <div style="font-size:0.8rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-dim); margin-bottom:5px;">Target Address</div>
        <div id="display-host" style="font-family:monospace; font-size:1.2rem; color:var(--primary); margin-bottom:20px; word-break:break-all;">
            Not Configured
        </div>
        
        <button id="btn-launch" class="btn-primary" onclick="launchNode()" style="width:100%; padding:15px; font-size:1.1rem; border-radius:10px; display:none;">
            🚀 Open Interface
        </button>
        <button id="btn-configure" class="btn-outline" onclick="document.getElementById('node-settings-modal').style.display='flex'" style="width:100%; padding:15px; border-radius:10px;">
            ⚙️ Configure Link
        </button>
    </div>

    <!-- SETTINGS MODAL -->
    <div id="node-settings-modal" class="modal-overlay" style="z-index: 3000;">
        <div class="modal-card">
            <div class="modal-header">
                <h2>Node Configuration</h2>
            </div>
            <div class="modal-body">
                <label class="modal-label">Lollms Host URL</label>
            <input type="text" id="set-host" class="modal-input" placeholder="http://localhost:9600" oninput="validateHostInput(this)">
            <small style="color:var(--text-dim);">The address where your Lollms server is running.</small>

            <label class="modal-label">API Key (Optional)</label>
            <div class="input-group">
                <input type="password" id="set-key" class="modal-input" placeholder="Secret Key">
                <button class="btn-icon-group" onclick="toggleKeyVisibility()" title="Show/Hide Key">👁️</button>
            </div>
            <small style="color:var(--text-dim);">Stored securely for future integrations.</small>
            </div>

            <div class="modal-actions">
                <button class="btn-full btn-cancel" onclick="document.getElementById('node-settings-modal').style.display='none'">Cancel</button>
                <button class="btn-full btn-save" onclick="saveNodeSettings()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentNodeSettings = { host: '', key: '' };

async function initChat() {
    // Re-using the naming convention 'initChat' so the Launcher calls it correctly
    try {
        const res = await fetch(`${apiRoot}settings`, { headers: {'X-WP-Nonce':nonce} });
        const data = await res.json();
        
        currentNodeSettings = data;
        
        if(data.host) {
            document.getElementById('set-host').value = data.host;
            updateDisplay(data.host);
        } else {
            // Default check for localhost
            document.getElementById('set-host').value = "http://localhost:9600";
        }

        if(data.key) document.getElementById('set-key').value = data.key; 

    } catch(e) {}
}

function updateDisplay(host) {
    const display = document.getElementById('display-host');
    const launchBtn = document.getElementById('btn-launch');
    const configBtn = document.getElementById('btn-configure');

    if (host && host.length > 5) {
        display.innerText = host;
        launchBtn.style.display = 'block';
        configBtn.style.display = 'none';
        
        // Add a small "edit" text below
        if(!document.getElementById('btn-edit-link')) {
            const editLink = document.createElement('div');
            editLink.id = 'btn-edit-link';
            editLink.innerHTML = `<span style="cursor:pointer; font-size:0.8rem; color:var(--text-dim); text-decoration:underline; margin-top:10px; display:block;" onclick="document.getElementById('node-settings-modal').style.display='flex'">Edit Connection</span>`;
            document.getElementById('node-status-card').appendChild(editLink);
        }
    } else {
        display.innerText = "Not Configured";
        launchBtn.style.display = 'none';
        configBtn.style.display = 'block';
    }
}

function launchNode() {
    let url = document.getElementById('set-host').value;
    if(!url) return;
    if(!url.startsWith('http')) url = 'http://' + url;
    window.open(url, '_blank');
}

function validateHostInput(input) {
    // Basic helper to ensure protocol
    // if(input.value.length > 4 && !input.value.startsWith('http')) { ... }
}

async function toggleKeyVisibility() {
    const input = document.getElementById('set-key');
    const isPass = input.type === 'password';
    
    if (isPass) {
        input.type = 'text';
        if (input.value === '******') {
            input.value = "Fetching...";
            try {
                const res = await fetch(`${apiRoot}settings?reveal=true`, { headers: {'X-WP-Nonce':nonce} });
                const data = await res.json();
                input.value = data.key || "";
            } catch(e) {
                input.value = "";
            }
        }
    } else {
        input.type = 'password';
    }
}

async function saveNodeSettings() {
    const data = {
        host: document.getElementById('set-host').value,
        key: document.getElementById('set-key').value
    };
    
    const btn = document.querySelector('#node-settings-modal .btn-save');
    btn.innerText = "Saving...";
    
    await fetch(`${apiRoot}settings`, {
        method: 'POST',
        headers: {'Content-Type':'application/json', 'X-WP-Nonce':nonce},
        body: JSON.stringify(data)
    });
    
    updateDisplay(data.host);
    
    btn.innerText = "Saved";
    setTimeout(() => { 
        btn.innerText = "Save Configuration"; 
        document.getElementById('node-settings-modal').style.display='none'; 
    }, 1000);
}
</script>
