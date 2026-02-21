<!-- APP: QR TOOLS -->
<div id="app-qr" class="app-module" style="display:none;">
    <div class="app-tabs">
        <div class="app-tab active" onclick="switchQRTab('scan')" id="tab-qr-scan">Scan</div>
        <div class="app-tab" onclick="switchQRTab('gen')" id="tab-qr-gen">Generate</div>
        <div class="app-tab" onclick="switchQRTab('lib')" id="tab-qr-lib">Library</div>
    </div>
    
    <!-- SCAN TAB -->
    <div id="view-qr-scan" class="qr-view">
        <div class="scan-trigger-container">
            <button class="btn-scan-trigger" onclick="startScanner('qr')">
                <div style="font-size:2.5rem;">📷</div>
                <div style="margin-top:5px; font-weight:bold; font-size:0.9rem;">Scan QR</div>
            </button>
            <div id="qr-scan-result">
                <p>DETECTED CONTENT:</p>
                <div id="qr-content-text"></div>
                <div style="display:flex; gap:10px;">
                    <button class="btn-full btn-primary" id="btn-qr-visit" style="display:none;" onclick="visitQRLink()">Open Link</button>
                    <button class="btn-full btn-save" onclick="saveScannedQR()">Save to Library</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- GENERATE TAB -->
    <div id="view-qr-gen" class="qr-view" style="display:none;">
        <textarea id="qr-input-text" class="modal-input" rows="3" placeholder="Enter text or URL..."></textarea>
        <button class="btn-full btn-primary" onclick="generateQR()" style="margin-top:15px;">Generate Code</button>
        <div id="qr-gen-output">
            <div id="qrcode-canvas" style="display:flex; justify-content:center;"></div>
        </div>
        <button id="btn-save-gen" class="btn-full btn-save" style="display:none; margin-top:15px;" onclick="saveGeneratedQR()">Save to Library</button>
    </div>
    
    <!-- LIBRARY TAB -->
    <div id="view-qr-lib" class="qr-view" style="display:none;">
        <div id="qr-library-grid"></div>
    </div>
</div>

<script>
function switchQRTab(tab) { 
    ['scan', 'gen', 'lib'].forEach(t => { 
        document.getElementById('tab-qr-'+t).classList.remove('active'); 
        document.getElementById('view-qr-'+t).style.display = 'none'; 
    }); 
    document.getElementById('tab-qr-'+tab).classList.add('active'); 
    document.getElementById('view-qr-'+tab).style.display = 'block'; 
    if(tab==='lib') loadQRLibrary(); 
}

async function loadQRLibrary() { 
    const grid=document.getElementById('qr-library-grid'); 
    grid.innerHTML='Loading...'; 
    try {
        const res=await fetch(`${apiRoot}items/qrcode`,{headers:{'X-WP-Nonce':nonce}}); 
        const items=await res.json(); 
        grid.innerHTML=''; 
        items.forEach(item=>{ 
            grid.innerHTML+=`<div class="qr-card"><img src="${item.image_data}"><div style="padding:5px; font-weight:bold; font-size:0.8rem;">${item.item_name}</div><button class="btn-delete" style="width:100%; border:none; padding:5px; border-radius:4px; margin-top:5px;" onclick="deleteQR(${item.id})">Delete</button></div>`; 
        }); 
    } catch(e){ grid.innerHTML='Error'; }
}

async function deleteQR(id) { 
    if(!confirm("Delete?")) return; 
    await fetch(`${apiRoot}delete`,{method:'POST',headers:{'Content-Type':'application/json','X-WP-Nonce':nonce},body:JSON.stringify({id:id})}); 
    loadQRLibrary(); 
}

function generateQR() { 
    const t=document.getElementById('qr-input-text').value; 
    if(!t)return; 
    const c=document.getElementById('qrcode-canvas'); c.innerHTML=''; 
    new QRCode(c,{text:t,width:200,height:200}); 
    document.getElementById('qr-gen-output').style.display='block'; 
    document.getElementById('btn-save-gen').style.display='block'; 
}

async function saveGeneratedQR() { 
    const t=document.getElementById('qr-input-text').value; 
    const img=document.querySelector('#qrcode-canvas img').src; 
    await apiAdd({name:t.substring(0,15),type:'qrcode',image_data:img}); 
    alert('Saved'); 
}

async function saveScannedQR() { 
    if(!qrScannedContent)return; 
    await apiAdd({name:qrScannedContent.substring(0,15),type:'qrcode',barcode:qrScannedContent}); 
    alert('Saved'); 
}

function visitQRLink() { 
    if(qrScannedContent && (qrScannedContent.startsWith('http') || qrScannedContent.startsWith('www'))) window.open(qrScannedContent, '_blank'); 
}
</script>
