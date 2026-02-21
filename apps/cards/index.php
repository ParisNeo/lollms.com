<!-- APP: CARDS -->
<div id="app-cards" class="app-module" style="display:none; padding-bottom: 20px;">
    <div style="display:flex; gap:10px; margin-bottom:15px; justify-content:flex-end;">
        <button class="btn-tool-sm" onclick="importCards()">⬆ Import</button>
        <button class="btn-tool-sm" onclick="exportCards()">⬇ Export</button>
        <input type="file" id="import-file" style="display:none" accept=".json" onchange="handleImportFile(this)">
    </div>
    <div id="cards-grid" class="wallet-grid"></div>
</div>

<script>
async function loadCards() { 
    const g=document.getElementById('cards-grid'); 
    g.innerHTML='Loading...'; 
    try {
        const r=await fetch(`${apiRoot}items/wallet`,{headers:{'X-WP-Nonce':nonce}}); 
        const i=await r.json(); 
        g.innerHTML=''; 
        if(i.length===0) g.innerHTML='<div style="grid-column:1/-1; text-align:center; color:gray;">No cards found.</div>';
        i.forEach(it=>{ 
            const c=document.createElement('div'); c.className='wallet-card'; 
            if(it.image_data) c.innerHTML=`<img src="${it.image_data}">`; 
            c.innerHTML+=`<div class="wallet-content"><div class="wallet-title">${it.item_name}</div><div class="wallet-code">${it.barcode||'****'}</div></div>`; 
            c.onclick=()=>openEditor('cards',it); 
            g.appendChild(c); 
        });
    } catch(e){ g.innerHTML='<div style="color:red">Error loading cards</div>'; }
}

async function exportCards() {
    const res = await fetch(`${apiRoot}items/wallet`, { headers: { 'X-WP-Nonce': nonce } });
    const items = await res.json();
    const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(items));
    const downloadAnchorNode = document.createElement('a');
    downloadAnchorNode.setAttribute("href", dataStr);
    downloadAnchorNode.setAttribute("download", "lollms_cards.json");
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
            if(!confirm(`Import ${items.length} cards?`)) return;
            for (const item of items) {
                await apiAdd({
                    name: item.item_name, type: 'wallet', barcode: item.barcode,
                    description: item.description, image_data: item.image_data
                });
            }
            alert(`Imported successfully.`);
            loadCards();
        } catch (err) { alert("Error parsing JSON: " + err.message); }
    };
    reader.readAsText(file);
    input.value = ''; 
}
</script>
