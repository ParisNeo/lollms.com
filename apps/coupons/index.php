<!-- APP: COUPONS -->
<div id="app-coupons" class="app-module" style="display:none;">
    <div id="coupons-list" class="coupons-container-list"></div>
</div>

<script>
async function loadCoupons() { 
    const l=document.getElementById('coupons-list'); 
    l.innerHTML='<div class="coupon-loader">Fetching coupons...</div>'; 
    try {
        const r=await fetch(`${apiRoot}items/coupon`,{headers:{'X-WP-Nonce':nonce}}); 
        const i=await r.json(); 
        l.innerHTML=''; 
        if(i.length===0) {
            l.innerHTML='<div class="coupon-empty">No coupons found. Tap + to add one.</div>';
            return;
        }
        i.forEach(it=>{ 
            const d=document.createElement('div'); 
            d.className='coupon-card'; 
            if(parseFloat(it.price)<=0.01) d.classList.add('coupon-spent'); 
            
            d.innerHTML=`
                <div class="coupon-top">
                    <div class="coupon-info">
                        <div class="coupon-name">${it.item_name}</div>
                        <small class="coupon-code">${it.barcode||'Tap to edit'}</small>
                    </div>
                    <div class="coupon-val">$${parseFloat(it.price).toFixed(2)}</div>
                </div>
                <div class="coupon-rip"></div>
            `; 
            d.onclick=()=>openEditor('coupon',it); 
            l.appendChild(d); 
        });
    } catch(e){ l.innerHTML='<div class="coupon-error">Error loading coupons</div>'; }
}
</script>
