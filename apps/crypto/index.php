<!-- APP: CRYPTO WALLET -->
<div id="app-crypto" class="app-module" style="display:none; padding-bottom:20px;">
    <div class="crypto-ticker">
        <div><div style="font-size:0.8rem; color:var(--text-dim);">BTC Price</div><div id="price-btc" style="font-weight:bold;">Loading...</div></div>
        <div><div style="font-size:0.8rem; color:var(--text-dim);">ETH Price</div><div id="price-eth" style="font-weight:bold;">Loading...</div></div>
    </div>
    
    <div id="crypto-list"></div>
    
    <div class="add-wallet-section">
        <h3 style="margin-top:0;">Add Wallet</h3>
        <button class="btn-primary" style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px; background:#f6851b; border:none; border-radius:12px; padding:12px; margin-bottom:15px;" onclick="connectMetaMask()">
            <img src="https://upload.wikimedia.org/wikipedia/commons/3/36/MetaMask_Fox.svg" width="24"> Connect MetaMask
        </button>
        <div style="display:flex; gap:10px;">
            <button class="btn-outline" style="flex:1;" onclick="openEditor('crypto', null, 'BTC')">➕ BTC</button>
            <button class="btn-outline" style="flex:1;" onclick="openEditor('crypto', null, 'ETH')">➕ ETH</button>
            <button class="btn-outline" style="flex:1;" onclick="openEditor('crypto', null, 'LOL')">➕ LOL</button>
        </div>
    </div>
</div>

<script>
async function loadCrypto() {
    const list = document.getElementById('crypto-list'); 
    list.innerHTML = '<div style="color:#888; text-align:center;">Syncing...</div>';
    fetchPrices();
    const res = await fetch(`${apiRoot}items/crypto`, { headers: { 'X-WP-Nonce': nonce } });
    const items = await res.json(); 
    list.innerHTML = '';
    
    if(items.length===0) list.innerHTML = '<div style="text-align:center; padding:20px; color:var(--text-dim);">No wallets. Add one below.</div>';
    
    for (const item of items) {
        let balance = '0.00'; let icon = 'btc'; let ticker = 'BTC';
        if (item.item_name.includes('ETH')) { icon = 'eth'; ticker = 'ETH'; }
        if (item.item_name.includes('LOL')) { icon = 'lol'; ticker = 'LOL'; }

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
        if(d.bitcoin) { fetchedRates.BTC = d.bitcoin.usd; document.getElementById('price-btc').innerText = '$' + d.bitcoin.usd.toLocaleString(); }
        if(d.ethereum) { fetchedRates.ETH = d.ethereum.usd; document.getElementById('price-eth').innerText = '$' + d.ethereum.usd.toLocaleString(); }
    } catch(e) {}
}

async function connectMetaMask() {
    if (typeof window.ethereum !== 'undefined') {
        try {
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            await apiAdd({ name: "MetaMask Wallet", type: "crypto", barcode: accounts[0], description: "Imported via MetaMask" });
            alert("Connected!"); loadCrypto();
        } catch (error) { alert("Connection failed."); }
    } else { alert("MetaMask is not installed."); }
}

async function fetchHistory(address, type) {
    const cont = document.getElementById('tx-history-list'); 
    cont.innerHTML = '<div style="text-align:center; padding:10px;">Loading history...</div>';
    let html = '';
    try {
        if (type === 'BTC') {
            const r = await fetch(`https://blockchain.info/rawaddr/${address}?limit=5&cors=true`);
            const d = await r.json();
            d.txs.forEach(tx => {
                const sent = tx.inputs.some(i => i.prev_out.addr === address);
                const val = sent ? tx.inputs.find(i => i.prev_out.addr === address).prev_out.value : tx.out.find(o => o.addr === address).value;
                html += `<div class="history-item"><span style="color:${sent?'#ef4444':'#10b981'}">${sent?'Sent':'Recv'} ${(val/100000000).toFixed(6)}</span><span style="font-size:0.75rem;">${new Date(tx.time*1000).toLocaleDateString()}</span></div>`;
            });
        } else if (type === 'ETH') {
            const r = await fetch(`https://api.blockcypher.com/v1/eth/main/addrs/${address}`);
            const d = await r.json();
            if(d.txrefs) {
                d.txrefs.slice(0,5).forEach(tx => {
                    const dir = (tx.tx_input_n === -1) ? 'in' : 'out'; 
                    html += `<div class="history-item"><span style="color:${dir=='out'?'#ef4444':'#10b981'}">${dir=='in'?'Recv':'Sent'} ${(tx.value/1e18).toFixed(5)}</span><span style="font-size:0.75rem;">${new Date(tx.confirmed).toLocaleDateString()}</span></div>`;
                });
            }
        }
    } catch(e) {}
    cont.innerHTML = html || '<div style="padding:10px; text-align:center; color:#666;">No recent transactions.</div>';
}
</script>
