<!-- APP: CONVERTER -->
<div id="app-converter" class="app-module" style="display:none; padding-bottom:20px;">
    <div class="currency-ticker-wrap" id="live-currency-ticker">
        <span style="color:var(--text-dim);">Fetching live rates...</span>
    </div>
    <div class="app-tabs">
        <div class="app-tab active" onclick="switchConvTab('currency')" id="tab-conv-currency">Currency</div>
        <div class="app-tab" onclick="switchConvTab('length')" id="tab-conv-length">Length</div>
        <div class="app-tab" onclick="switchConvTab('weight')" id="tab-conv-weight">Weight</div>
        <div class="app-tab" onclick="switchConvTab('temp')" id="tab-conv-temp">Temp</div>
    </div>
    <div class="converter-grid">
        <div class="input-group" style="margin:0;">
            <input type="number" id="conv-in-1" class="app-input conv-input" value="1" oninput="convert('1')">
            <select id="conv-sel-1" class="modal-input" onchange="convert('1')" style="padding:5px;"></select>
        </div>
        <div class="conv-equals">=</div>
        <div class="input-group" style="margin:0;">
            <input type="number" id="conv-in-2" class="app-input conv-input" value="1" oninput="convert('2')">
            <select id="conv-sel-2" class="modal-input" onchange="convert('1')" style="padding:5px;"></select>
        </div>
    </div>
    <div style="text-align:center; font-size:0.8rem; color:var(--text-dim); margin-top:20px;">Rates relative to USD (Live)</div>
</div>

<script>
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
            const ticker = document.getElementById('live-currency-ticker');
            let html = '';
            ['TND', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD'].forEach(c => {
                if(fiatRates[c]) html += `<span class="ticker-item">USD/${c} <strong style="color:var(--success);">${fiatRates[c].toFixed(3)}</strong></span>`;
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
    
    units[cat].forEach(u => { sel1.add(new Option(u, u)); sel2.add(new Option(u, u)); });
    
    if(cat==='currency') { sel1.value='USD'; sel2.value='EUR'; }
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
</script>
