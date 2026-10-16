<style>
/* ===== Design system bersama halaman Koperasi (admin) ===== */
.kop-page{
    --kop-primary:#3a6450; --kop-primary-dark:#2f5b45;
    --kop-text:#1c2a23; --kop-muted:#647067;
    --kop-border:#e5e9e3; --kop-bg:#f8faf6;
    --kop-radius:14px;
    display:flex; flex-direction:column; gap:14px;
}
.kop-page *, .kop-page *::before, .kop-page *::after{ box-sizing:border-box; }

/* ---------- Header ---------- */
.kop-header{ display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap; }
.kop-kicker{ font-size:11px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; color:var(--kop-primary); margin:0 0 4px; }
.kop-title{ font-size:24px; font-weight:800; color:var(--kop-text); margin:0; letter-spacing:-.02em; }
.kop-subtitle{ font-size:13.5px; color:var(--kop-muted); margin:6px 0 0; }
.kop-subtitle strong{ color:var(--kop-primary); }
.kop-header-actions{ display:flex; gap:8px; flex-wrap:wrap; }

/* ---------- Tombol ---------- */
.kop-btn{
    display:inline-flex; align-items:center; gap:7px; padding:9px 16px;
    border-radius:10px; font-weight:700; font-size:13px; text-decoration:none;
    border:1.5px solid transparent; cursor:pointer; font-family:inherit;
    transition:all .18s ease; white-space:nowrap;
}
.kop-btn svg{ width:15px; height:15px; }
.kop-btn-primary{ background:var(--kop-primary); color:#fff; box-shadow:0 4px 12px rgba(58,100,80,.18); }
.kop-btn-primary:hover{ background:var(--kop-primary-dark); transform:translateY(-1px); }
.kop-btn-outline{ background:#fff; color:var(--kop-primary); border-color:#d8e0d6; }
.kop-btn-outline:hover{ border-color:var(--kop-primary); background:#f3f7f2; }
.kop-btn-ghost{ background:#fff; color:var(--kop-muted); border-color:#d8e0d6; }
.kop-btn-ghost:hover{ color:var(--kop-text); border-color:#b9c4b7; }
.kop-btn-danger{ background:#fee2e2; color:#b91c1c; }
.kop-btn-danger:hover{ background:#fecaca; }

/* ---------- Statistik ---------- */
.kop-stats{ display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
.kop-stat{
    display:flex; align-items:center; gap:13px; background:#fff;
    border:1px solid var(--kop-border); border-radius:var(--kop-radius);
    padding:16px; transition:all .2s ease; min-width:0;
}
.kop-stat:hover{ transform:translateY(-2px); box-shadow:0 8px 20px rgba(35,55,42,.08); }
.kop-stat-icon{
    width:44px; height:44px; border-radius:12px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
}
.kop-stat-icon svg{ width:20px; height:20px; }
.kop-stat-info{ display:flex; flex-direction:column; min-width:0; }
.kop-stat-label{ font-size:10.5px; font-weight:800; letter-spacing:.07em; text-transform:uppercase; color:var(--kop-muted); }
.kop-stat-value{ font-size:19px; font-weight:800; color:var(--kop-text); line-height:1.25; overflow-wrap:anywhere; }
.kop-stat-foot{ font-size:11px; color:var(--kop-muted); font-weight:600; margin-top:1px; }

/* ---------- Panel & tabel ---------- */
.kop-panel{ background:#fff; border:1px solid var(--kop-border); border-radius:var(--kop-radius); overflow:hidden; }
.kop-panel-head{ display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 20px; border-bottom:1px solid var(--kop-border); flex-wrap:wrap; }
.kop-panel-title{ font-size:15px; font-weight:800; color:var(--kop-text); margin:0; }
.kop-panel-sub{ font-size:12px; color:var(--kop-muted); margin:2px 0 0; }
.kop-panel-body{ padding:16px 20px; }
.kop-panel-foot{ padding:14px 20px; border-top:1px solid var(--kop-border); display:flex; justify-content:flex-end; }
.kop-table-wrap{ overflow-x:auto; }
.kop-table{ width:100%; border-collapse:collapse; }
.kop-table thead th{
    font-size:10.5px; font-weight:800; letter-spacing:.07em; text-transform:uppercase;
    color:var(--kop-muted); text-align:left; padding:11px 16px;
    background:var(--kop-bg); border-bottom:1px solid var(--kop-border); white-space:nowrap;
}
.kop-table tbody td, .kop-table tfoot td{
    padding:12px 16px; border-bottom:1px solid #f0f3ee;
    vertical-align:middle; font-size:13px; color:var(--kop-text);
}
.kop-table tfoot td{ background:var(--kop-bg); border-bottom:none; font-size:13.5px; font-weight:800; }
.kop-table tbody tr{ transition:background .15s ease; }
.kop-table tbody tr:hover{ background:#fafcf9; }
.kop-nowrap{ white-space:nowrap; }
.kop-hide-sm{ /* kolom disembunyikan di layar kecil */ }

/* ---------- Isi sel ---------- */
.kop-kode{ font-weight:800; font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; font-size:12.5px; }
.kop-pickup{
    font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
    background:var(--kop-bg); border:1px dashed #cfd9cd; padding:3px 9px;
    border-radius:7px; font-size:12px; font-weight:700; letter-spacing:.08em; color:var(--kop-primary);
}
.kop-user{ display:flex; align-items:center; gap:9px; min-width:0; }
.kop-avatar{
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    background:#e8f0e6; color:var(--kop-primary); font-weight:800; font-size:12px;
    display:flex; align-items:center; justify-content:center;
}
.kop-user-text{ min-width:0; }
.kop-user-name{ display:block; font-weight:700; font-size:13px; }
.kop-user-sub{ display:block; font-size:11px; color:var(--kop-muted); }
.kop-total{ font-weight:800; white-space:nowrap; }
.kop-date{ display:block; font-weight:700; font-size:12.5px; }
.kop-time{ display:block; font-size:11px; color:var(--kop-muted); }
.kop-thumb{
    width:44px; height:44px; border-radius:10px; background:var(--kop-bg);
    border:1px solid #eef1ec; display:flex; align-items:center; justify-content:center;
    font-size:20px; overflow:hidden; flex-shrink:0;
}
.kop-thumb img{ width:100%; height:100%; object-fit:cover; }
.kop-cat{
    display:inline-block; padding:3px 10px; border-radius:999px;
    background:var(--kop-bg); border:1px solid #e3e8e1;
    font-size:11.5px; font-weight:700; color:var(--kop-muted); white-space:nowrap;
}

/* ---------- Ranking & bar (laporan) ---------- */
.kop-rank-row{ display:flex; align-items:center; gap:10px; min-width:0; }
.kop-rank{
    width:26px; height:26px; border-radius:8px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:800; background:#f1f4f0; color:var(--kop-muted);
}
.kop-rank.top-1{ background:#fef3c7; color:#b45309; }
.kop-rank.top-2{ background:#eef1f4; color:#64748b; }
.kop-rank.top-3{ background:#fde8d8; color:#c2410c; }
.kop-bar{ height:5px; border-radius:999px; background:#eef2ec; margin-top:5px; overflow:hidden; }
.kop-bar i{ display:block; height:100%; border-radius:999px; background:linear-gradient(90deg,#3a6450,#4a7a62); }
.kop-bar.bar-blue i{ background:linear-gradient(90deg,#2563eb,#3b82f6); }
.kop-two-col{ display:grid; grid-template-columns:minmax(0,3fr) minmax(0,2fr); gap:14px; align-items:start; }

/* ---------- Badge ---------- */
.kop-badge{
    display:inline-flex; align-items:center; gap:6px; padding:4px 11px;
    border-radius:999px; font-size:11.5px; font-weight:800; white-space:nowrap;
}
.kop-badge i{ width:6px; height:6px; border-radius:50%; background:currentColor; }
.kop-badge--pending{ background:#fef3c7; color:#b45309; }
.kop-badge--dibayar{ background:#dbeafe; color:#1d4ed8; }
.kop-badge--diambil{ background:#dcfce7; color:#166534; }
.kop-badge--batal{ background:#f1f5f9; color:#64748b; }
.kop-badge--expired{ background:#fee2e2; color:#b91c1c; }

/* ---------- Aksi dalam tabel ---------- */
.kop-act{ display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.kop-act form{ display:inline-flex; margin:0; }
.kop-act-btn{
    display:inline-flex; align-items:center; padding:6px 12px; border-radius:8px;
    font-size:12px; font-weight:700; text-decoration:none; border:none; cursor:pointer;
    font-family:inherit; transition:all .15s ease; white-space:nowrap;
}
.kop-act--view{ background:#eef5ff; color:#1d4ed8; }
.kop-act--view:hover{ background:#dbeafe; }
.kop-act--edit{ background:#fef3c7; color:#b45309; }
.kop-act--edit:hover{ background:#fde68a; }
.kop-act--ok{ background:#e8f0e6; color:#166534; }
.kop-act--ok:hover{ background:#d5e8d5; }
.kop-act--danger{ background:#fee2e2; color:#b91c1c; }
.kop-act--danger:hover{ background:#fecaca; }

/* ---------- Filter & chip ---------- */
.kop-filter{ background:#fff; border:1px solid var(--kop-border); border-radius:var(--kop-radius); padding:14px 16px; }
.kop-filter-row{ display:flex; gap:10px; flex-wrap:wrap; }
.kop-search{
    flex:1; min-width:220px; display:flex; align-items:center; gap:9px;
    background:var(--kop-bg); border:1.5px solid #dfe6dc; border-radius:10px;
    padding:0 12px; transition:all .18s ease; color:var(--kop-muted); cursor:text;
}
.kop-search:focus-within{ border-color:var(--kop-primary); background:#fff; box-shadow:0 0 0 3px rgba(58,100,80,.1); }
.kop-search svg{ width:16px; height:16px; flex-shrink:0; }
.kop-search input{ flex:1; min-width:0; border:none; background:transparent; outline:none; padding:10px 0; font-size:13.5px; color:var(--kop-text); font-family:inherit; }
.kop-filter-btns{ display:flex; gap:8px; }
.kop-chips{ display:flex; gap:8px; flex-wrap:wrap; }
.kop-chip{
    display:inline-flex; align-items:center; gap:7px; padding:7px 14px;
    border-radius:999px; background:#fff; border:1.5px solid #dfe6dc;
    font-size:12.5px; font-weight:700; color:var(--kop-muted);
    text-decoration:none; transition:all .18s ease;
}
.kop-chip:hover{ border-color:var(--kop-primary); color:var(--kop-primary); }
.kop-chip.active{ background:var(--kop-primary); border-color:var(--kop-primary); color:#fff; }
.kop-chip-count{ background:rgba(0,0,0,.07); border-radius:999px; padding:1px 7px; font-size:11px; font-weight:800; }
.kop-chip.active .kop-chip-count{ background:rgba(255,255,255,.22); }

/* ---------- Form ---------- */
.kop-form-layout{ display:grid; grid-template-columns:minmax(0,1fr) 320px; gap:14px; align-items:start; }
.kop-form-side{ display:flex; flex-direction:column; gap:14px; position:sticky; top:16px; }
.kop-field{ margin-bottom:12px; }
.kop-field:last-child{ margin-bottom:0; }
.kop-label{ display:block; margin-bottom:5px; font-size:11px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--kop-primary); }
.kop-label .opt{ color:var(--kop-muted); font-weight:600; text-transform:none; letter-spacing:0; }
.kop-input, .kop-select, .kop-textarea{
    width:100%; padding:9px 12px; border:1.5px solid #dfe4dd; border-radius:10px;
    font-family:inherit; font-size:13.5px; color:var(--kop-text); background:#fff;
    transition:all .18s ease;
}
.kop-input:focus, .kop-select:focus, .kop-textarea:focus{
    outline:none; border-color:var(--kop-primary); box-shadow:0 0 0 3px rgba(58,100,80,.1);
}
.kop-textarea{ resize:vertical; min-height:90px; }
.kop-input.has-error, .kop-select.has-error, .kop-textarea.has-error{ border-color:#f3b3b3; }
.kop-field-error{ display:block; margin-top:5px; font-size:12px; font-weight:600; color:#b91c1c; }
.kop-affix{
    display:flex; align-items:stretch; border:1.5px solid #dfe4dd; border-radius:10px;
    overflow:hidden; background:#fff; transition:all .18s ease;
}
.kop-affix:focus-within{ border-color:var(--kop-primary); box-shadow:0 0 0 3px rgba(58,100,80,.1); }
.kop-affix > span{
    display:flex; align-items:center; padding:0 12px; background:var(--kop-bg);
    font-size:12.5px; font-weight:800; color:var(--kop-muted); border-right:1px solid #e8ece6;
}
.kop-affix input{ flex:1; min-width:0; border:none; outline:none; padding:9px 12px; font:inherit; font-size:13.5px; color:var(--kop-text); background:transparent; }
.kop-check{ display:flex; align-items:flex-start; gap:10px; font-size:13px; font-weight:600; color:var(--kop-text); cursor:pointer; }
.kop-check input{ width:17px; height:17px; accent-color:var(--kop-primary); margin-top:1px; flex-shrink:0; }
.kop-upload-preview{ width:100%; border-radius:10px; border:1px solid var(--kop-border); margin-top:10px; display:block; }
.kop-emoji-preview{
    margin-top:8px; display:inline-flex; align-items:center; justify-content:center;
    width:52px; height:52px; font-size:26px; background:var(--kop-bg);
    border:1px solid #eef1ec; border-radius:10px;
}
.kop-form-actions{ display:flex; gap:8px; flex-wrap:wrap; }
.kop-form-actions .kop-btn{ flex:1; justify-content:center; }

/* ---------- Detail pesanan ---------- */
.kop-info-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:10px; margin-bottom:16px; }
.kop-info-item{ background:var(--kop-bg); border:1px solid #eef1ec; border-radius:10px; padding:11px 13px; min-width:0; }
.kop-info-label{ display:block; font-size:10px; font-weight:800; letter-spacing:.07em; text-transform:uppercase; color:var(--kop-muted); margin-bottom:3px; }
.kop-info-value{ font-size:13px; font-weight:700; color:var(--kop-text); word-break:break-word; }
.kop-info-value.mono{ font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; font-size:17px; letter-spacing:.14em; color:var(--kop-primary); }
.kop-steps{
    display:flex; align-items:center; padding:14px 16px; margin-bottom:16px;
    border:1px dashed #cfd9cd; border-radius:12px; background:#fbfdfa; flex-wrap:wrap; row-gap:10px;
}
.kop-step{ display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:700; color:var(--kop-muted); }
.kop-step i{
    width:24px; height:24px; border-radius:50%; font-style:normal;
    display:flex; align-items:center; justify-content:center;
    background:#e8ece6; color:var(--kop-muted); font-size:11px; font-weight:800; flex-shrink:0;
}
.kop-step.done{ color:var(--kop-primary); }
.kop-step.done i{ background:var(--kop-primary); color:#fff; }
.kop-step-sep{ flex:1 1 24px; min-width:24px; height:2px; margin:0 10px; border-radius:2px; background:#e2e8e0; }
.kop-step-sep.done{ background:var(--kop-primary); }
.kop-failed-note{
    padding:12px 16px; margin-bottom:16px; border:1px dashed #f3b3b3; border-radius:12px;
    background:#fef5f5; font-size:13px; font-weight:600; color:#b91c1c;
}
.kop-detail-actions{ display:flex; gap:8px; flex-wrap:wrap; padding-top:16px; border-top:1px solid var(--kop-border); margin-top:16px; }
.kop-detail-actions form{ margin:0; }

/* ---------- Empty state ---------- */
.kop-empty{ padding:56px 20px; text-align:center; }
.kop-empty-icon{
    width:64px; height:64px; margin:0 auto 12px; border-radius:18px;
    background:#e8f0e6; color:var(--kop-primary);
    display:flex; align-items:center; justify-content:center;
}
.kop-empty h3{ margin:0 0 4px; font-size:16px; font-weight:800; color:var(--kop-text); }
.kop-empty p{ margin:0 0 14px; font-size:13px; color:var(--kop-muted); }

/* ---------- Responsive ---------- */
@media (max-width:1024px){
    .kop-stats{ grid-template-columns:repeat(2,1fr); }
    .kop-two-col{ grid-template-columns:1fr; }
    .kop-form-layout{ grid-template-columns:1fr; }
    .kop-form-side{ position:static; }
}
@media (max-width:720px){
    .kop-hide-sm{ display:none; }
    .kop-stats{ grid-template-columns:1fr; }
    .kop-filter-row{ flex-direction:column; }
    .kop-filter-btns .kop-btn{ flex:1; justify-content:center; }
    .kop-panel-foot{ justify-content:center; }
}
</style>