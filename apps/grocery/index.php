<!-- APP: GROCERY (FORCE VERTICAL) -->
<div id="app-grocery" class="app-module" style="display:none; padding-bottom: 120px;">
    <div class="grocery-v2-container">
        
        <!-- INPUT SECTION -->
        <div class="grocery-v2-form-card">
            <div class="v2-input-row">
                <input type="text" id="g-name" placeholder="What to buy?" onkeypress="handleGroceryEnter(event)" style="flex:1;">
                <button class="v2-scan-btn" onclick="startScanner('grocery')" title="Scan Barcode">📷</button>
            </div>
            
            <div class="v2-meta-grid">
                <input type="number" id="g-qty" inputmode="numeric" placeholder="Qty (1)">
                <input type="number" id="g-price" inputmode="decimal" placeholder="Price ($)">
                <button class="v2-add-btn" onclick="addGrocery()">ADD</button>
            </div>
        </div>

        <div class="v2-list-section">
            <h3 class="v2-section-label">Active Items</h3>
            <ul id="grocery-list" class="v2-item-list"></ul>
        </div>

        <div class="v2-list-section">
            <h3 class="v2-section-label">Recently Bought</h3>
            <ul id="grocery-history" class="v2-item-list v2-history-mode"></ul>
        </div>

    </div>

    <!-- TOTAL BAR -->
    <div class="total-bar">
        <div class="v2-total-info">
            <span class="v2-total-label">Estimated Total</span>
            <span id="grand-total" class="total-amount">$0.00</span>
        </div>
    </div>
</div>

<script>
async function loadGrocery() {
    const list = document.getElementById('grocery-list'); 
    list.innerHTML = '<div class="v2-loader">Syncing...</div>';
    try {
        const [resItems, resHist] = await Promise.all([ 
            fetch(`${apiRoot}items/grocery`, { headers: { 'X-WP-Nonce': nonce } }), 
            fetch(`${apiRoot}suggestions`, { headers: { 'X-WP-Nonce': nonce } }) 
        ]);
        const items = await resItems.json(); 
        list.innerHTML = '';
        
        let t = 0; 
        items.forEach(item => { 
            const price = parseFloat(item.price) || 0;
            t += price;
            const li = document.createElement('li'); 
            li.className = 'v2-list-item';
            li.innerHTML = `
                <div class="v2-item-check" onclick="toggleItem(${item.id})"></div>
                <div class="v2-item-content">
                    <div class="v2-item-name">${item.item_name}</div>
                    <div class="v2-item-controls">
                        <div class="v2-ctrl-group"><span>Qty</span><input type="number" value="${item.quantity}" onchange="updateItem(${item.id},'quantity',this.value)"></div>
                        <div class="v2-ctrl-group"><span>$</span><input type="number" value="${item.price}" onchange="updateItem(${item.id},'price',this.value)" class="v2-price-input"></div>
                    </div>
                </div>
            `;
            list.appendChild(li);
        });
        
        document.getElementById('grand-total').innerText = '$' + t.toFixed(2);
        
        const hList = document.getElementById('grocery-history'); 
        hList.innerHTML = '';
        const historyData = await resHist.json();
        historyData.filter(i => i.list_type === 'grocery').forEach(item => { 
            const li = document.createElement('li'); 
            li.className = 'v2-history-item';
            li.innerHTML = `<span>${item.item_name}</span><button onclick="restoreItem('${item.item_name.replace(/'/g,"\\'")}','${item.quantity}','${item.price}')">+</button>`;
            hList.appendChild(li);
        });
    } catch(e) { list.innerHTML = 'Error loading list.'; }
}

async function apiAdd(data) { return fetch(`${apiRoot}add`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify(data) }); }
async function toggleItem(id) { await fetch(`${apiRoot}toggle`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify({ id: id }) }); loadGrocery(); }
async function restoreItem(n,q,p) { await apiAdd({name:n,type:'grocery',qty:q,price:p}); loadGrocery(); }
async function updateItem(id,f,v) { await fetch(`${apiRoot}update`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }, body: JSON.stringify({ id: id, field: f, value: v }) }); }
function handleGroceryEnter(e) { if(e.key === 'Enter') addGrocery(); }

async function addGrocery() { 
    const n = document.getElementById('g-name').value; 
    if(!n) return; 
    await apiAdd({name:n,type:'grocery',qty:document.getElementById('g-qty').value||1,price:document.getElementById('g-price').value||0}); 
    document.getElementById('g-name').value=''; document.getElementById('g-qty').value=''; document.getElementById('g-price').value='';
    loadGrocery(); 
}
</script>
