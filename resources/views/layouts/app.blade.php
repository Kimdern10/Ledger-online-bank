<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'Online Banking' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,72,400;0,72,500;0,72,600;1,72,400&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root{
    --ink:#10202F;
    --ink-2:#1B3145;
    --paper:#F6F4EE;
    --paper-2:#FFFFFF;
    --sage:#2F6F62;
    --sage-light:#E4EEE9;
    --wheat:#C9A24B;
    --coral:#C1503C;
    --mist:#DDD9CC;
    --text-1:#10202F;
    --text-2:#5C6B72;
    --text-3:#8C9298;
    --nav-w:230px;
    --bottom-nav-h:78px;
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  html,body{height:100%;}
  body{
    background:var(--paper);
    font-family:'Inter', sans-serif;
    -webkit-font-smoothing:antialiased;
    color:var(--text-1);
  }

  /* ============ APP SHELL ============ */
  .app{
    display:flex;
    min-height:100vh;
  }

  /* ============ SIDEBAR (desktop) / BOTTOM NAV (mobile) ============ */
  .sidenav{
    width:var(--nav-w);
    flex-shrink:0;
    background:var(--ink);
    color:var(--paper);
    padding:28px 20px;
    display:flex;
    flex-direction:column;
    gap:6px;
  }
  .brand{
    display:flex; align-items:center; gap:10px;
    padding:0 6px 26px;
  }
  .brand .mark{
    width:30px; height:30px; border-radius:8px;
    background:var(--wheat);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-weight:600; font-size:15px; color:var(--ink);
  }
  .brand span{font-family:'Newsreader', serif; font-size:17px; font-weight:500;}

  .nav-item{
    display:flex; align-items:center; gap:12px;
    padding:11px 12px;
    border-radius:10px;
    font-size:14px; font-weight:500;
    color:#9FB0BC;
    cursor:pointer;
    transition:background .15s ease, color .15s ease;
  }
  .nav-item svg{width:19px; height:19px; stroke:currentColor; stroke-width:1.6; flex-shrink:0;}
  {{-- Was white-space:nowrap, which quietly assumed every label would
       always be short English text. Now that these labels can be any of
       the app's languages (see the language-switcher form further down
       this file), a longer translation just wraps onto a second line
       inside the sidebar instead of overflowing past its fixed width —
       min-width:0 is what actually lets a flex child shrink and wrap
       rather than being pushed out to its unwrapped content width. --}}
  .nav-item span.label{white-space:normal; overflow-wrap:break-word; line-height:1.25; flex:1; min-width:0;}
  .nav-item{text-decoration:none;}
  .nav-item:hover{background:rgba(255,255,255,0.06); color:var(--paper);}
  .nav-item.active{background:rgba(255,255,255,0.1); color:var(--paper);}

  .nav-fab-row{margin-top:auto; padding-top:16px; border-top:1px solid rgba(255,255,255,0.1);}
  .nav-fab-row .nav-item.cta{
    background:var(--sage); color:var(--paper);
  }
  .nav-fab-row .nav-item.cta:hover{background:#356f60;}

  /* mobile-only fab, hidden on desktop */
  .mobile-fab{display:none;}

  /* ============ MAIN ============ */
  .main{
    flex:1;
    min-width:0;
    padding:28px 32px 60px;
    max-width:1180px;
  }

  .topbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:22px;
  }
  .greeting-row{display:flex; align-items:center; gap:12px;}
  .avatar{
    width:42px; height:42px; border-radius:50%;
    background:var(--ink);
    color:var(--paper);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-size:16px;
    flex-shrink:0;
  }
  .greeting p{font-size:11px; letter-spacing:.06em; text-transform:uppercase; color:var(--text-3); font-weight:500;}
  .greeting h2{font-size:17px; font-weight:500; color:var(--text-1); margin-top:2px;}
  .bell{
    width:38px; height:38px; border-radius:50%;
    background:var(--paper-2);
    border:1px solid var(--mist);
    display:flex; align-items:center; justify-content:center;
    position:relative;
    cursor:pointer;
    flex-shrink:0;
  }
  {{-- The always-on dot that used to live here (.bell::after) was static
       decoration from before the dashboard bell had a real unread count —
       dashboard.blade.php's own .bell-badge (#notifBadge) is the real,
       conditional indicator now, so this one is gone rather than the two
       showing side by side. --}}
  .bell svg{width:17px; height:17px; stroke:var(--ink);}

  /* ============ TOP GRID: ledger + spend (dashboard) ============ */
  .top-grid{
    display:grid;
    grid-template-columns:1fr;
    gap:16px;
    margin-bottom:8px;
  }

  .ledger{
    background:var(--ink);
    border-radius:20px;
    padding:26px 24px 22px;
    position:relative;
    overflow:hidden;
    color:var(--paper);
  }
  .ledger::before{
    content:'';
    position:absolute; top:0; left:0; right:0; height:10px;
    background-image: radial-gradient(circle, var(--paper) 3px, transparent 3.5px);
    background-size: 16px 10px;
    background-position: -2px -5px;
    background-repeat: repeat-x;
  }
  .ledger-top{
    display:flex; justify-content:space-between; align-items:flex-start;
    margin-bottom:18px;
  }
  .ledger-label{font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:#9FB3C2; font-weight:500;}
  .acct-chip{
    font-family:'IBM Plex Mono', monospace;
    font-size:11px;
    color:#B9C8D2;
    background:rgba(255,255,255,0.08);
    border:1px solid rgba(255,255,255,0.14);
    padding:4px 10px;
    border-radius:20px;
  }
  .balance{
    font-family:'Newsreader', serif;
    font-size:44px;
    font-weight:500;
    line-height:1;
    letter-spacing:-0.01em;
  }
  .balance sup{font-size:20px; font-weight:400; opacity:.7;}
  .balance-underline{
    width:52px; height:2px; background:var(--wheat); margin:12px 0 16px;
  }
  .ledger-bottom{
    display:flex; justify-content:space-between; align-items:center;
  }
  .ledger-meta{font-family:'IBM Plex Mono', monospace; font-size:11px; color:#93A5B2;}

  .spend-card{
    background:var(--paper-2);
    border:1px solid var(--mist);
    border-radius:18px;
    padding:20px;
    display:flex;
    align-items:center;
    gap:18px;
  }
  .ring{position:relative; width:76px; height:76px; flex-shrink:0;}
  .ring-label{
    position:absolute; inset:0;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
  }
  .ring-label strong{font-size:15px; font-family:'Newsreader', serif; color:var(--text-1);}
  .ring-label span{font-size:9px; color:var(--text-3); text-transform:uppercase; letter-spacing:.05em;}
  .spend-detail h4{font-size:13px; font-weight:600; color:var(--text-1); margin-bottom:6px;}
  .spend-detail p{font-size:12px; color:var(--text-2); line-height:1.5;}
  .spend-detail .figs{display:flex; gap:14px; margin-top:8px; flex-wrap:wrap;}
  .fig{font-size:11px; color:var(--text-2); display:flex; align-items:center; gap:5px;}
  .dot{width:7px; height:7px; border-radius:50%; flex-shrink:0;}

  /* ============ ACTIONS (dashboard) ============ */
  .actions{
    display:flex;
    justify-content:flex-start;
    gap:14px;
    padding:20px 0 4px;
    flex-wrap:wrap;
  }
  .action{
    display:flex; flex-direction:column; align-items:center; gap:8px;
    background:none; border:none; cursor:pointer;
    font-family:'Inter', sans-serif;
    width:66px;
  }
  .action .circle{
    width:52px; height:52px; border-radius:50%;
    background:var(--paper-2);
    border:1px solid var(--mist);
    display:flex; align-items:center; justify-content:center;
    transition:transform .15s ease, border-color .15s ease;
  }
  .action:hover .circle{border-color:var(--sage);}
  .action:active .circle{transform:scale(0.94);}
  .action.primary .circle{background:var(--sage); border-color:var(--sage);}
  .action .circle svg{width:20px; height:20px; stroke:var(--ink); stroke-width:1.6;}
  .action.primary .circle svg{stroke:var(--paper);}
  .action span{font-size:12px; color:var(--text-2); font-weight:500;}
  .action{text-decoration:none;}

  /* ============ SECTION / TRANSACTIONS (dashboard) ============ */
  .section-head{
    display:flex; justify-content:space-between; align-items:baseline;
    padding:22px 0 12px;
  }
  .section-head h3{font-family:'Newsreader', serif; font-size:18px; font-weight:500; color:var(--text-1);}
  .section-head a{font-size:12px; color:var(--sage); font-weight:500; text-decoration:none;}

  .tx-card{
    background:var(--paper-2);
    border:1px solid var(--mist);
    border-radius:18px;
    padding:6px 20px;
  }
  .tx-row{
    display:flex; align-items:center; gap:13px;
    padding:14px 0;
    border-bottom:1px solid var(--mist);
  }
  .tx-row:last-child{border-bottom:none;}
  .tx-bar{width:2px; align-self:stretch; border-radius:2px; background:var(--sage); flex-shrink:0;}
  .tx-row.out .tx-bar{background:var(--coral);}
  .tx-row.in .tx-bar{background:var(--sage);}
  .tx-mid{flex:1; min-width:0;}
  .tx-mid p.name{font-size:14px; font-weight:500; color:var(--text-1);}
  .tx-mid p.meta{font-size:11.5px; color:var(--text-3); font-family:'IBM Plex Mono', monospace; margin-top:2px;}
  .tx-amt{font-family:'Newsreader', serif; font-size:15px; font-weight:500; color:var(--text-1); white-space:nowrap;}
  .tx-row.out .tx-amt{color:var(--coral);}
  .tx-row.in .tx-amt{color:var(--sage);}

  /* ============ MOBILE BOTTOM NAV OVERRIDE ============ */
  .bottom-nav{display:none;}

  /* ============================================================
     SEND MONEY / FORM FLOW SHARED STYLES
     ============================================================ */
  .send-wrap{width:100%; max-width:520px;}

  .page-header{
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:22px;
  }
  .icon-btn{
    width:38px; height:38px; border-radius:50%;
    background:var(--paper-2);
    border:1px solid var(--mist);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; text-decoration:none; flex-shrink:0;
    transition:border-color .15s ease;
  }
  .icon-btn:hover{border-color:var(--sage);}
  .icon-btn svg{width:17px; height:17px; stroke:var(--ink); stroke-width:1.7;}
  .page-header h1{
    font-family:'Newsreader', serif; font-size:19px; font-weight:500; color:var(--text-1);
  }

  /* ---- recipient / amount card (ledger style) ---- */
  .send-card{
    background:var(--ink);
    border-radius:20px;
    padding:30px 26px 26px;
    position:relative;
    overflow:hidden;
    color:var(--paper);
    text-align:center;
    margin-bottom:18px;
  }
  .send-card::before{
    content:'';
    position:absolute; top:0; left:0; right:0; height:10px;
    background-image: radial-gradient(circle, var(--paper) 3px, transparent 3.5px);
    background-size: 16px 10px;
    background-position: -2px -5px;
    background-repeat: repeat-x;
  }
  .recipient-avatar{
    width:72px; height:72px; border-radius:50%;
    margin:6px auto 14px;
    background:var(--wheat);
    color:var(--ink);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-size:26px; font-weight:600;
    border:3px solid rgba(255,255,255,0.12);
  }
  .recipient-name{font-family:'Newsreader', serif; font-size:19px; font-weight:500;}
  .recipient-card-no{
    font-family:'IBM Plex Mono', monospace; font-size:12px; color:#9FB3C2;
    margin-top:6px; letter-spacing:.03em;
  }

  .amount-field{
    margin:24px 0 6px;
    display:flex; align-items:baseline; justify-content:center; gap:6px;
  }
  .amount-field .cur{font-family:'Newsreader', serif; font-size:30px; color:#9FB3C2; font-weight:400;}
  .amount-field input{
    background:transparent; border:none; outline:none;
    font-family:'Newsreader', serif; font-size:46px; font-weight:500;
    color:var(--paper); text-align:center;
    width:auto; max-width:230px;
    letter-spacing:-0.01em;
  }
  .amount-underline{
    width:52px; height:2px; background:var(--wheat); margin:10px auto 16px;
  }
  .send-date{font-family:'IBM Plex Mono', monospace; font-size:11.5px; color:#93A5B2;}

  /* ---- from / receiving-account (bank) selector ---- */
  .from-label{
    font-size:12px; font-weight:600; color:var(--text-2);
    margin-bottom:8px; text-transform:uppercase; letter-spacing:.04em;
  }
  .bank-select{
    display:flex; align-items:center; gap:12px;
    background:var(--paper-2);
    border:1px solid var(--mist);
    border-radius:16px;
    padding:12px 14px;
    cursor:pointer;
    margin-bottom:18px;
    transition:border-color .15s ease;
  }
  .bank-select:hover{border-color:var(--sage);}
  .bank-select .bank-mark{
    width:40px; height:40px; border-radius:10px;
    background:var(--ink);
    color:var(--wheat);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-weight:600; font-size:16px;
    flex-shrink:0;
  }
  .bank-select .bank-info{flex:1; min-width:0;}
  .bank-select .bank-info p.name{font-size:14px; font-weight:600; color:var(--text-1);}
  .bank-select .bank-info p.meta{
    font-size:11.5px; color:var(--text-3); font-family:'IBM Plex Mono', monospace; margin-top:2px;
  }
  .bank-select .bank-balance{text-align:right; margin-right:2px;}
  .bank-select .bank-balance p.amt{font-size:13px; font-weight:600; color:var(--sage); font-family:'Newsreader', serif;}
  .bank-select .bank-balance p.tag{font-size:10px; color:var(--text-3); text-transform:uppercase; letter-spacing:.04em; margin-top:1px;}
  .bank-select .chev-btn{
    width:26px; height:26px; border-radius:50%;
    background:var(--paper);
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
  }
  .bank-select .chev-btn svg{width:14px; height:14px; stroke:var(--text-2); stroke-width:1.8;}
  .bank-select.to .bank-mark{background:var(--sage-light); color:var(--sage);}
  .linked-tag{
    display:inline-flex; align-items:center; gap:4px;
    font-size:9.5px; font-weight:600; color:var(--sage);
    background:var(--sage-light);
    padding:2px 7px; border-radius:20px;
    text-transform:uppercase; letter-spacing:.03em;
    margin-left:6px; vertical-align:middle;
  }

  /* ---- editable recipient fields inside the ink send-card ---- */
  .recipient-field{margin-bottom:2px;}
  .recipient-field input{
    background:transparent; border:none; border-bottom:1px solid rgba(255,255,255,0.18);
    outline:none; text-align:center; color:var(--paper);
    font-family:'Newsreader', serif; font-size:19px; font-weight:500;
    padding:6px 4px; width:100%; max-width:260px;
    transition:border-color .15s ease;
  }
  .recipient-field input::placeholder{color:rgba(255,255,255,0.4); font-weight:400;}
  .recipient-field input:focus{border-bottom-color:var(--wheat);}

  .account-field{margin-top:10px; margin-bottom:4px;}
  .account-field input{
    background:transparent; border:none; border-bottom:1px solid rgba(255,255,255,0.18);
    outline:none; text-align:center; color:#B9C8D2;
    font-family:'IBM Plex Mono', monospace; font-size:12.5px; letter-spacing:.04em;
    padding:6px 4px; width:100%; max-width:240px;
    transition:border-color .15s ease, color .15s ease;
  }
  .account-field input::placeholder{color:rgba(255,255,255,0.32);}
  .account-field input:focus{border-bottom-color:var(--wheat); color:var(--paper);}

  /* ---- linked-bank quick chips ---- */
  .linked-chips{display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px;}
  .linked-chip{
    display:flex; align-items:center; gap:7px;
    background:var(--paper-2); border:1px solid var(--mist);
    border-radius:20px; padding:7px 12px 7px 7px;
    cursor:pointer; font-size:12.5px; font-weight:600; color:var(--text-1);
    transition:border-color .15s ease, background .15s ease;
  }
  .linked-chip:hover{border-color:var(--sage);}
  .linked-chip.selected{border-color:var(--sage); background:var(--sage-light);}
  .linked-chip .chip-mark{
    width:22px; height:22px; border-radius:6px;
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-size:11px; font-weight:600; color:#fff;
    flex-shrink:0;
  }
  .see-all-banks{
    display:flex; align-items:center; justify-content:center; gap:6px;
    width:100%; padding:11px; margin-bottom:10px;
    background:var(--paper-2); border:1px dashed var(--mist); border-radius:14px;
    font-size:12.5px; font-weight:600; color:var(--sage);
    cursor:pointer; font-family:'Inter', sans-serif;
  }
  .see-all-banks svg{width:14px; height:14px; stroke:var(--sage); stroke-width:1.8; flex-shrink:0;}

  /* ---- static "From" row (single account, non-interactive) ---- */
  .from-row{
    display:flex; align-items:center; gap:12px;
    background:var(--paper-2); border:1px solid var(--mist); border-radius:16px;
    padding:14px 16px; margin-bottom:24px;
  }
  .from-row .bank-mark{
    width:40px; height:40px; border-radius:10px;
    background:var(--ink); color:var(--wheat);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-weight:600; font-size:16px;
    flex-shrink:0;
  }
  .from-row .bank-info{flex:1; min-width:0;}
  .from-row .bank-info p.name{font-size:14px; font-weight:600; color:var(--text-1);}
  .from-row .bank-info p.meta{font-size:11.5px; color:var(--text-3); font-family:'IBM Plex Mono', monospace; margin-top:2px;}
  .from-row .bank-balance{text-align:right;}
  .from-row .bank-balance p.amt{font-size:13px; font-weight:600; color:var(--sage); font-family:'Newsreader', serif;}
  .from-row .bank-balance p.tag{font-size:10px; color:var(--text-3); text-transform:uppercase; letter-spacing:.04em; margin-top:1px;}

  .section-label{
    font-size:12px; font-weight:600; color:var(--text-2);
    text-transform:uppercase; letter-spacing:.04em; margin-bottom:10px;
  }

  /* ---- recent recipients (Citi-style avatar row) ---- */
  .recipients-scroll{
    display:flex; gap:16px; overflow-x:auto; padding:2px 2px 8px;
    margin-bottom:6px; scrollbar-width:none;
  }
  .recipients-scroll::-webkit-scrollbar{display:none;}
  .recipient-chip{
    display:flex; flex-direction:column; align-items:center; gap:6px;
    flex-shrink:0; width:64px; cursor:pointer;
    background:none; border:none; font-family:'Inter', sans-serif;
  }
  .recipient-chip .rc-avatar{
    width:52px; height:52px; border-radius:50%;
    background:var(--ink); color:var(--wheat);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-size:17px; font-weight:600;
    border:2px solid transparent; transition:border-color .15s ease;
  }
  .recipient-chip.selected .rc-avatar{border-color:var(--sage);}
  .recipient-chip span.rc-name{
    font-size:11px; color:var(--text-2); font-weight:500;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:64px;
  }
  .recipient-chip.add .rc-avatar{
    background:var(--paper-2); border:1.5px dashed var(--mist); color:var(--text-3);
  }
  .recipient-chip.add:hover .rc-avatar{border-color:var(--sage); color:var(--sage);}

  /* ---- recipient details form (plain card, not the old ink ledger card) ---- */
  .recipient-form{
    background:var(--paper-2); border:1px solid var(--mist); border-radius:16px;
    padding:16px; margin-bottom:18px;
  }
  .recipient-form .text-input{margin-bottom:12px;}
  .recipient-form .text-input:last-child{margin-bottom:0;}

  /* ---- amount card (plain white, Citi-style quick-amount chips) ---- */
  .amount-card{
    background:var(--paper-2); border:1px solid var(--mist); border-radius:18px;
    padding:24px 20px 20px; text-align:center; margin-bottom:18px;
  }
  .amount-card .amount-field{margin:4px 0 8px;}
  .amount-card .amount-field .cur{color:var(--text-3);}
  .amount-card .amount-field input{color:var(--text-1);}
  .amount-card .amount-underline{background:var(--wheat); margin:0 auto 16px;}
  .quick-amounts{display:flex; gap:8px; justify-content:center; flex-wrap:wrap;}
  .quick-amt-btn{
    padding:8px 16px; border-radius:20px; border:1px solid var(--mist);
    background:var(--paper); font-family:'Inter', sans-serif;
    font-size:13px; font-weight:600; color:var(--text-2); cursor:pointer;
    transition:border-color .15s ease, background .15s ease, color .15s ease;
  }
  .quick-amt-btn:hover, .quick-amt-btn.active{
    border-color:var(--sage); background:var(--sage-light); color:var(--sage);
  }
  .fee-breakdown{
    margin-top:16px; padding-top:14px; border-top:1px dashed var(--mist);
    display:none; text-align:left;
  }
  .fee-breakdown.show{display:block;}
  .fee-row{
    display:flex; justify-content:space-between; align-items:center;
    font-size:12.5px; color:var(--text-2); padding:3px 0;
  }
  .fee-row.total{
    font-weight:600; color:var(--text-1);
    font-family:'Newsreader', serif; font-size:14.5px;
    padding-top:8px; margin-top:6px; border-top:1px solid var(--mist);
  }

  /* ============================================================
     RECEIVE MONEY
     ============================================================ */
  .acct-detail{margin-bottom:16px; text-align:left;}
  .acct-detail:last-of-type{margin-bottom:0;}
  .acct-detail .ad-label{
    font-size:10.5px; text-transform:uppercase; letter-spacing:.06em;
    color:#93A5B2; margin-bottom:4px;
  }
  .acct-detail .ad-row{display:flex; align-items:center; justify-content:space-between; gap:10px;}
  .acct-detail .ad-value{
    font-family:'IBM Plex Mono', monospace; font-size:14.5px; color:var(--paper);
    letter-spacing:.03em; word-break:break-all;
  }
  .acct-detail.name .ad-value{
    font-family:'Newsreader', serif; font-size:17px; font-weight:500; letter-spacing:0;
  }
  .copy-icon-btn{
    width:28px; height:28px; border-radius:50%; flex-shrink:0;
    background:rgba(255,255,255,0.1); border:none;
    display:flex; align-items:center; justify-content:center; cursor:pointer;
    transition:background .15s ease;
  }
  .copy-icon-btn:hover{background:rgba(255,255,255,0.18);}
  .copy-icon-btn svg{width:13px; height:13px; stroke:var(--paper); stroke-width:1.8;}
  .acct-divider{height:1px; background:rgba(255,255,255,0.12); margin:16px 0;}

  .qr-box{
    background:var(--paper); border-radius:16px; padding:16px;
    display:flex; align-items:center; justify-content:center;
    margin:20px auto 8px; width:fit-content;
  }
  .qr-caption{text-align:center; font-size:11.5px; color:#93A5B2; margin-top:6px;}

  .btn-row{display:flex; gap:10px; margin-top:18px;}
  .btn-outline{
    flex:1; display:flex; align-items:center; justify-content:center; gap:7px;
    padding:14px; background:var(--paper-2); border:1px solid var(--mist);
    border-radius:16px; font-family:'Inter', sans-serif; font-size:14px; font-weight:600;
    color:var(--text-1); cursor:pointer; text-decoration:none;
    transition:border-color .15s ease;
  }
  .btn-outline:hover{border-color:var(--sage);}
  .btn-outline svg{width:16px; height:16px; stroke:currentColor; stroke-width:1.7; flex-shrink:0;}
  .btn-row .pay-btn{flex:1;}

  .ledger-toast{
    position:fixed; left:50%; bottom:26px; transform:translateX(-50%) translateY(20px);
    background:var(--ink); color:var(--paper); font-size:13px; font-weight:500;
    padding:12px 20px; border-radius:30px; opacity:0; pointer-events:none;
    transition:opacity .2s ease, transform .2s ease; z-index:30;
    display:flex; align-items:center; gap:8px; white-space:nowrap;
  }
  .ledger-toast.show{opacity:1; transform:translateX(-50%) translateY(0);}
  .ledger-toast svg{width:15px; height:15px; stroke:var(--sage); stroke-width:2; flex-shrink:0;}

  /* ============================================================
     PAY BILLS
     ============================================================ */
  .bill-card{
    background:var(--paper-2); border:1px solid var(--mist); border-radius:18px;
    padding:6px 18px; margin-bottom:18px;
  }
  .bill-row{display:flex; align-items:center; gap:13px; padding:15px 0; border-bottom:1px solid var(--mist);}
  .bill-row:last-child{border-bottom:none;}
  .bill-row .b-icon{
    width:40px; height:40px; border-radius:12px; background:var(--sage-light);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
  }
  .bill-row .b-icon svg{width:19px; height:19px; stroke:var(--sage); stroke-width:1.6;}
  .bill-row .b-mid{flex:1; min-width:0;}
  .bill-row .b-mid p.name{font-size:14px; font-weight:600; color:var(--text-1);}
  .bill-row .b-mid p.meta{font-size:11.5px; color:var(--text-3); margin-top:2px;}
  .bill-row .b-right{text-align:right; flex-shrink:0;}
  .bill-row .b-right p.amt{font-family:'Newsreader', serif; font-size:15px; font-weight:500; color:var(--text-1);}
  .bill-pay-btn{
    margin-top:6px; padding:6px 14px; border-radius:20px; border:none;
    background:var(--sage); color:var(--paper); font-size:11.5px; font-weight:600;
    cursor:pointer; font-family:'Inter', sans-serif;
  }
  .bill-pay-btn:hover{background:#356f60;}

  /* ============================================================
     LINK BANK / CARD
     ============================================================ */
  .remove-btn{
    width:26px; height:26px; border-radius:50%; background:#F5E4DF; border:none;
    display:flex; align-items:center; justify-content:center; flex-shrink:0; cursor:pointer;
  }
  .remove-btn svg{width:12px; height:12px; stroke:var(--coral); stroke-width:2;}

  .card-visual{
    background:linear-gradient(135deg, var(--ink), var(--ink-2));
    border-radius:18px; padding:22px; color:var(--paper); position:relative; overflow:hidden;
    margin-bottom:18px; min-height:140px; display:flex; flex-direction:column; justify-content:space-between;
  }
  .card-visual .cv-top{display:flex; justify-content:space-between; align-items:flex-start;}
  .card-visual .cv-number{font-family:'IBM Plex Mono', monospace; font-size:16px; letter-spacing:.12em; margin:14px 0 4px;}
  .card-visual .cv-bottom{display:flex; justify-content:space-between; align-items:flex-end; font-size:11px; color:#B9C8D2;}
  .card-visual .cv-bottom .cv-name{
    font-family:'Inter', sans-serif; font-size:12.5px; color:var(--paper); font-weight:500;
    text-transform:uppercase; letter-spacing:.03em;
  }

  /* ============================================================
     MOBILE PAY / SCAN
     ============================================================ */
  .scan-viewport{
    position:relative; width:100%; aspect-ratio:1/1; max-width:320px; margin:0 auto;
    background:var(--ink); border-radius:20px; overflow:hidden;
  }
  .scan-viewport video{width:100%; height:100%; object-fit:cover; display:block;}
  .scan-frame{position:absolute; inset:14%; pointer-events:none;}
  .scan-corner{position:absolute; width:28px; height:28px; border:3px solid var(--wheat);}
  .scan-corner.tl{top:0; left:0; border-right:none; border-bottom:none; border-radius:8px 0 0 0;}
  .scan-corner.tr{top:0; right:0; border-left:none; border-bottom:none; border-radius:0 8px 0 0;}
  .scan-corner.bl{bottom:0; left:0; border-right:none; border-top:none; border-radius:0 0 0 8px;}
  .scan-corner.br{bottom:0; right:0; border-left:none; border-top:none; border-radius:0 0 8px 0;}
  .scan-status{text-align:center; font-size:12.5px; color:var(--text-2); margin-top:14px;}
  .scan-fallback{margin-top:20px; display:flex; gap:8px;}
  .scan-fallback .text-input{flex:1;}
  .scan-fallback .pay-btn{width:auto; padding:14px 18px; flex-shrink:0;}

  /* ============================================================
     HISTORY
     ============================================================ */
  .summary-row{display:flex; gap:12px; margin-bottom:20px;}
  .summary-stat{
    flex:1; background:var(--paper-2); border:1px solid var(--mist); border-radius:16px;
    padding:16px; text-align:center;
  }
  .summary-stat .ss-label{
    font-size:11px; color:var(--text-3); text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px;
  }
  .summary-stat .ss-value{font-family:'Newsreader', serif; font-size:20px; font-weight:500;}
  .summary-stat.in .ss-value{color:var(--sage);}
  .summary-stat.out .ss-value{color:var(--coral);}

  .filter-row{display:flex; gap:8px; overflow-x:auto; margin-bottom:20px; padding-bottom:2px; scrollbar-width:none;}
  .filter-row::-webkit-scrollbar{display:none;}

  .date-group-label{
    font-size:12px; font-weight:600; color:var(--text-3);
    text-transform:uppercase; letter-spacing:.05em; margin:20px 2px 10px;
  }
  .date-group-label:first-child{margin-top:0;}

  /* ============================================================
     MY CARDS
     ============================================================ */
  .card-scroll{
    display:flex; gap:14px; overflow-x:auto; padding:4px 4px 10px;
    scroll-snap-type:x mandatory; scrollbar-width:none; margin-bottom:8px;
  }
  .card-scroll::-webkit-scrollbar{display:none;}
  .card-scroll .card-visual{
    min-width:260px; width:260px; flex-shrink:0; scroll-snap-align:start;
    cursor:pointer; border:2px solid transparent;
    transition:border-color .15s ease, opacity .15s ease, filter .15s ease;
    margin-bottom:0;
  }
  .card-scroll .card-visual.selected{border-color:var(--wheat);}
  .card-scroll .card-visual.frozen{opacity:.55; filter:grayscale(65%);}
  .card-scroll .card-visual.virtual{background:linear-gradient(135deg, var(--sage), #1f4a40);}
  .cv-top.left-align{justify-content:flex-start; gap:10px;}

  .card-badge{
    position:absolute; top:14px; right:14px;
    font-size:9.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase;
    padding:3px 9px; border-radius:20px;
    background:rgba(255,255,255,0.14); color:var(--paper);
  }
  .card-badge.frozen{background:rgba(193,80,60,0.3); color:#F3C7BC;}

  .add-card-ghost{
    min-width:260px; width:260px; flex-shrink:0; scroll-snap-align:start;
    border:1.5px dashed var(--mist); border-radius:18px; min-height:150px;
    display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px;
    color:var(--text-3); cursor:pointer; background:var(--paper-2);
    transition:border-color .15s ease, color .15s ease; text-decoration:none;
    font-family:'Inter', sans-serif; font-size:12.5px; font-weight:600;
  }
  .add-card-ghost:hover{border-color:var(--sage); color:var(--sage);}
  .add-card-ghost .ac-circle{
    width:38px; height:38px; border-radius:50%; border:1.5px dashed currentColor;
    display:flex; align-items:center; justify-content:center;
  }
  .add-card-ghost .ac-circle svg{width:16px; height:16px; stroke:currentColor; stroke-width:1.8;}

  .card-detail-card{
    background:var(--paper-2); border:1px solid var(--mist); border-radius:18px;
    padding:6px 20px; margin-bottom:18px;
  }
  .card-detail-row{
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:14px 0; border-bottom:1px solid var(--mist);
  }
  .card-detail-row:last-child{border-bottom:none;}
  .card-detail-row .cd-label{
    font-size:10.5px; color:var(--text-3); text-transform:uppercase; letter-spacing:.05em; margin-bottom:3px;
  }
  .card-detail-row .cd-value{
    font-family:'IBM Plex Mono', monospace; font-size:14.5px; color:var(--text-1); letter-spacing:.03em;
  }
  .card-detail-row .cd-actions{display:flex; gap:6px; flex-shrink:0;}
  .icon-mini-btn{
    width:30px; height:30px; border-radius:50%; background:var(--paper); border:1px solid var(--mist);
    display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0;
    transition:border-color .15s ease;
  }
  .icon-mini-btn:hover{border-color:var(--sage);}
  .icon-mini-btn svg{width:14px; height:14px; stroke:var(--text-2); stroke-width:1.7;}

  .freeze-row{display:flex; align-items:center; justify-content:space-between; padding:14px 0;}
  .freeze-row .fr-text p.t{font-size:14px; font-weight:600; color:var(--text-1);}
  .freeze-row .fr-text p.d{font-size:11.5px; color:var(--text-3); margin-top:2px;}

  .toggle-switch{position:relative; width:44px; height:26px; flex-shrink:0; cursor:pointer; display:inline-block;}
  .toggle-switch input{position:absolute; opacity:0; width:0; height:0;}
  .toggle-switch .track{
    position:absolute; inset:0; background:var(--mist); border-radius:20px; transition:background .15s ease;
  }
  .toggle-switch .thumb{
    position:absolute; top:3px; left:3px; width:20px; height:20px; border-radius:50%;
    background:var(--paper-2); box-shadow:0 1px 3px rgba(16,32,47,0.25); transition:transform .15s ease;
  }
  .toggle-switch input:checked ~ .track{background:var(--coral);}
  .toggle-switch input:checked ~ .thumb{transform:translateX(18px);}

  /* ============================================================
     SUPPORT CHAT
     ============================================================ */
  .support-status{display:flex; align-items:center; gap:10px; margin-bottom:18px;}
  .support-status .sa-avatar{
    width:38px; height:38px; border-radius:50%; background:var(--ink); color:var(--wheat);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-weight:600; font-size:14px; flex-shrink:0; position:relative;
  }
  .support-status .sa-avatar::after{
    content:''; position:absolute; bottom:-1px; right:-1px; width:10px; height:10px; border-radius:50%;
    background:var(--sage); border:2px solid var(--paper);
  }
  .support-status .sa-text p.n{font-size:14px; font-weight:600; color:var(--text-1);}
  .support-status .sa-text p.s{font-size:11.5px; color:var(--sage); margin-top:1px;}

  .chat-thread{display:flex; flex-direction:column; gap:14px; padding-bottom:12px;}
  .msg-row{display:flex; gap:8px; max-width:85%;}
  .msg-row.support{align-self:flex-start;}
  .msg-row.user{align-self:flex-end; flex-direction:row-reverse;}
  .msg-avatar{
    width:28px; height:28px; border-radius:50%; background:var(--ink); color:var(--wheat);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-size:11px; font-weight:600; flex-shrink:0;
  }
  .msg-col{display:flex; flex-direction:column; gap:4px;}
  .msg-row.user .msg-col{align-items:flex-end;}
  .msg-bubble{padding:11px 14px; border-radius:16px; font-size:13.5px; line-height:1.45;}
  .msg-row.support .msg-bubble{
    background:var(--paper-2); border:1px solid var(--mist); color:var(--text-1);
    border-top-left-radius:4px;
  }
  .msg-row.user .msg-bubble{
    background:var(--sage); color:var(--paper);
    border-top-right-radius:4px;
  }
  .msg-time{font-size:10px; color:var(--text-3); padding:0 4px;}

  .typing-row{display:flex; gap:8px; align-self:flex-start;}
  .typing-bubble{
    background:var(--paper-2); border:1px solid var(--mist);
    border-radius:16px; border-top-left-radius:4px;
    padding:12px 16px; display:flex; gap:4px; align-items:center;
  }
  .typing-dot{width:6px; height:6px; border-radius:50%; background:var(--text-3); animation:typingBounce 1.2s infinite ease-in-out;}
  .typing-dot:nth-child(2){animation-delay:.15s;}
  .typing-dot:nth-child(3){animation-delay:.3s;}
  @keyframes typingBounce{
    0%,60%,100%{transform:translateY(0); opacity:.5;}
    30%{transform:translateY(-4px); opacity:1;}
  }

  .quick-replies{display:flex; gap:8px; flex-wrap:wrap; margin:2px 0 20px;}

  .chat-input-bar{
    display:flex; align-items:center; gap:10px;
    background:var(--paper-2); border:1px solid var(--mist); border-radius:26px;
    padding:8px 8px 8px 18px;
  }
  .chat-input-bar input{
    flex:1; border:none; outline:none; background:transparent;
    font-family:'Inter', sans-serif; font-size:13.5px; color:var(--text-1);
  }
  .chat-input-bar input::placeholder{color:var(--text-3);}
  .chat-send-btn{
    width:36px; height:36px; border-radius:50%; background:var(--sage); border:none;
    display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0;
    transition:background .15s ease;
  }
  .chat-send-btn:hover{background:#356f60;}
  .chat-send-btn svg{width:16px; height:16px; stroke:var(--paper); stroke-width:2;}

  .chat-input-wrap{margin-top:18px;}
  @media (max-width:899px){
    .support-wrap{padding-bottom:calc(var(--bottom-nav-h) + 90px);}
    .chat-input-wrap{
      position:fixed; bottom:var(--bottom-nav-h); left:0; right:0;
      background:var(--paper); padding:10px 18px;
      border-top:1px solid var(--mist); margin-top:0; z-index:10;
    }
  }

  /* ---- when section reuses .segmented / .seg-btn ---- */
  .when-block{margin-bottom:18px;}
  .text-input{
    width:100%; background:var(--paper-2); border:1px solid var(--mist); border-radius:14px;
    padding:14px 16px; font-family:'Inter', sans-serif; font-size:14px; color:var(--text-1); outline:none;
    transition:border-color .15s ease;
  }
  .text-input::placeholder{color:var(--text-3);}
  .text-input:focus{border-color:var(--sage);}

  /* ---- receive-via segmented toggle ---- */
  .receive-via-block{margin-bottom:18px;}
  .segmented{
    display:flex;
    background:var(--paper);
    border:1px solid var(--mist);
    border-radius:14px;
    padding:4px;
    gap:4px;
    margin-bottom:10px;
  }
  .seg-btn{
    flex:1;
    display:flex; align-items:center; justify-content:center; gap:7px;
    padding:10px 8px;
    border-radius:11px;
    border:none; background:transparent;
    font-family:'Inter', sans-serif; font-size:13px; font-weight:600;
    color:var(--text-2);
    cursor:pointer;
    transition:background .15s ease, color .15s ease;
  }
  .seg-btn svg{width:15px; height:15px; stroke:currentColor; stroke-width:1.8;}
  .seg-btn.active{background:var(--ink); color:var(--paper);}
  .segmented.three{gap:3px; padding:3px;}
  .segmented.three .seg-btn{
    flex-direction:column; gap:4px;
    padding:10px 4px; font-size:11.5px;
  }
  .segmented.three .seg-btn svg{width:16px; height:16px;}
  .segmented.four{gap:3px; padding:3px;}
  .segmented.four .seg-btn{
    flex-direction:column; gap:3px;
    padding:8px 2px; font-size:10.5px;
  }
  .segmented.four .seg-btn svg{width:15px; height:15px;}

  .receive-note{
    display:flex; align-items:center; gap:6px;
    font-size:11.5px; color:var(--text-3);
    padding:0 2px; margin-bottom:2px;
  }
  .receive-note svg{width:12px; height:12px; stroke:currentColor; stroke-width:2; flex-shrink:0;}
  .receive-note.instant{color:var(--sage);}

  /* ---- form fields (category / note) ---- */
  .field-group{margin-bottom:16px;}
  .field-group p.label{
    font-size:12px; font-weight:600; color:var(--text-2);
    margin-bottom:8px; text-transform:uppercase; letter-spacing:.04em;
  }
  .field-group p.label span{
    text-transform:none; font-weight:400; color:var(--text-3); letter-spacing:0;
  }
  .select-wrap{position:relative;}
  .select-wrap select{
    appearance:none; -webkit-appearance:none;
    width:100%;
    background:var(--paper-2);
    border:1px solid var(--mist);
    border-radius:14px;
    padding:14px 40px 14px 16px;
    font-family:'Inter', sans-serif; font-size:14px; color:var(--text-1);
    cursor:pointer;
  }
  .select-wrap select:focus{outline:none; border-color:var(--sage);}
  .select-wrap .chev{
    position:absolute; right:14px; top:50%; transform:translateY(-50%);
    pointer-events:none;
  }
  .select-wrap .chev svg{width:16px; height:16px; stroke:var(--text-2); stroke-width:1.6;}

  .textarea-wrap{position:relative;}
  .textarea-wrap textarea{
    width:100%; min-height:90px; resize:none;
    background:var(--paper-2);
    border:1px solid var(--mist);
    border-radius:14px;
    padding:14px 42px 14px 16px;
    font-family:'Inter', sans-serif; font-size:14px; color:var(--text-1);
  }
  .textarea-wrap textarea::placeholder{color:var(--text-3);}
  .textarea-wrap textarea:focus{outline:none; border-color:var(--sage);}
  .textarea-wrap .clip{
    position:absolute; right:14px; top:14px;
    width:26px; height:26px; border-radius:50%;
    background:var(--sage-light);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer;
  }
  .textarea-wrap .clip svg{width:14px; height:14px; stroke:var(--sage); stroke-width:1.7;}

  /* ============================================================
     PROFILE / SETTINGS
     ============================================================ */
  .profile-card{
    background:var(--paper-2);
    border:1px solid var(--mist);
    border-radius:20px;
    padding:28px 20px 24px;
    text-align:center;
    margin-bottom:22px;
  }
  .profile-avatar{
    width:76px; height:76px; border-radius:50%;
    margin:0 auto 14px;
    object-fit:cover;
    background:var(--ink);
    color:var(--wheat);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-size:28px; font-weight:600;
    border:3px solid var(--sage-light);
  }
  .profile-name-row{
    display:flex; align-items:center; justify-content:center; gap:8px;
  }
  .profile-name-row h3{font-family:'Newsreader', serif; font-size:19px; font-weight:500; color:var(--text-1);}
  .profile-edit{
    width:26px; height:26px; border-radius:50%;
    background:var(--sage-light);
    display:flex; align-items:center; justify-content:center;
    text-decoration:none; flex-shrink:0;
  }
  .profile-edit svg{width:13px; height:13px; stroke:var(--sage); stroke-width:1.8;}
  .profile-email{
    font-family:'IBM Plex Mono', monospace; font-size:12px; color:var(--text-3);
    margin-top:6px;
  }

  .setting-list{
    background:var(--paper-2);
    border:1px solid var(--mist);
    border-radius:18px;
    padding:4px 18px;
  }
  .setting-item{
    display:flex; align-items:center; gap:14px;
    padding:15px 0;
    border-bottom:1px solid var(--mist);
    text-decoration:none;
    color:var(--text-1);
  }
  .setting-item:last-child{border-bottom:none;}
  .setting-item .s-icon{
    width:36px; height:36px; border-radius:10px;
    background:var(--sage-light);
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
  }
  .setting-item .s-icon svg{width:17px; height:17px; stroke:var(--sage); stroke-width:1.7;}
  .setting-item span.label{flex:1; font-size:14px; font-weight:500;}
  .setting-item .s-chev{width:16px; height:16px; stroke:var(--text-3); stroke-width:1.8; flex-shrink:0;}
  .setting-item.danger .s-icon{background:#F5E4DF;}
  .setting-item.danger .s-icon svg{stroke:var(--coral);}
  .setting-item.danger span.label{color:var(--coral);}
  .setting-group-label{
    font-size:11px; font-weight:600; color:var(--text-3);
    text-transform:uppercase; letter-spacing:.06em;
    margin:20px 4px 10px;
  }
  .setting-group-label:first-child{margin-top:0;}

  /* ---- pay button ---- */
  .pay-btn-wrap{margin-top:26px;}
  .pay-btn{
    display:flex; align-items:center; justify-content:center;
    width:100%; padding:16px;
    background:var(--sage); color:var(--paper);
    border:none; border-radius:16px;
    font-family:'Inter', sans-serif; font-size:15px; font-weight:600;
    letter-spacing:.01em;
    cursor:pointer;
    text-decoration:none;
    transition:background .15s ease, transform .1s ease;
  }
  .pay-btn:hover{background:#356f60;}
  .pay-btn:active{transform:scale(0.98);}

  /* fixed bottom bar only on mobile */
  .fixed-bottom-btn{display:none;}

  /* ---- picker modal (compact, centered — not pinned to the bottom) ---- */
  .sheet-overlay{
    display:none;
    position:fixed; inset:0;
    background:rgba(16,32,47,0.45);
    z-index:20;
    align-items:flex-start;
    justify-content:center;
    padding-top:12vh;
  }
  .sheet-overlay.open{display:flex;}
  .sheet{
    background:var(--paper-2);
    width:100%; max-width:440px;
    margin:0 20px;
    border-radius:22px;
    padding:8px 18px 18px;
    max-height:60vh;
    overflow-y:auto;
    animation:sheetIn .18s ease both;
  }
  @keyframes sheetIn{from{transform:translateY(-10px); opacity:0;} to{transform:translateY(0); opacity:1;}}
  .sheet-handle{display:none;}
  .sheet-head{display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;}
  .sheet-head h4{font-family:'Newsreader', serif; font-size:17px; font-weight:500;}
  .sheet-head .icon-btn{width:32px; height:32px;}
  .sheet-head .icon-btn svg{width:14px; height:14px;}
  .sheet .pay-btn-wrap{
    position:sticky; bottom:0;
    margin:16px -18px -18px;
    padding:14px 18px calc(14px + env(safe-area-inset-bottom));
    background:var(--paper-2);
    border-top:1px solid var(--mist);
    border-radius:0 0 22px 22px;
  }
  @media (max-width:899px){
    .sheet-overlay{padding-top:6vh;}
    .sheet{max-height:80vh;}
  }
  .sheet-search{position:relative; margin-bottom:12px;}
  .sheet-search input{
    width:100%; background:var(--paper); border:1px solid var(--mist); border-radius:12px;
    padding:11px 14px 11px 38px; font-family:'Inter', sans-serif; font-size:13.5px; color:var(--text-1);
    outline:none; transition:border-color .15s ease;
  }
  .sheet-search input:focus{border-color:var(--sage);}
  .sheet-search input::placeholder{color:var(--text-3);}
  .sheet-search svg{
    position:absolute; left:13px; top:50%; transform:translateY(-50%);
    width:15px; height:15px; stroke:var(--text-3); stroke-width:1.8;
  }
  .bank-option{
    display:flex; align-items:center; gap:12px;
    padding:12px 8px; border-radius:14px;
    cursor:pointer;
  }
  .bank-option:hover{background:var(--paper);}
  .bank-option .mark{
    width:38px; height:38px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-weight:600; font-size:14px;
    flex-shrink:0; color:var(--paper);
  }
  .bank-option .info p.name{font-size:13.5px; font-weight:600; color:var(--text-1);}
  .bank-option .info p.meta{font-size:11px; color:var(--text-3); font-family:'IBM Plex Mono', monospace; margin-top:1px;}
  .bank-option.selected{background:var(--sage-light);}
  .bank-option .check{margin-left:auto; width:18px; height:18px; flex-shrink:0;}
  .bank-option .check svg{width:18px; height:18px; stroke:var(--sage); stroke-width:2;}

  /* ============ SHARED ANIMATION ============ */
  .fade-in{animation:fadeUp .5s ease both;}
  .d1{animation-delay:.03s;} .d2{animation-delay:.08s;} .d3{animation-delay:.13s;} .d4{animation-delay:.18s;} .d5{animation-delay:.23s;}
  @keyframes fadeUp{from{opacity:0; transform:translateY(8px);} to{opacity:1; transform:translateY(0);}}
  @media (prefers-reduced-motion: reduce){ .fade-in{animation:none;} }

  /* ============================================================
     DESKTOP (>=900px): sidebar visible, 2-col top grid, centered forms
     ============================================================ */
  @media (min-width:900px){
    .top-grid{grid-template-columns:1.3fr 1fr;}
    .main{padding:36px 44px 60px;}
    .tx-card{max-width:640px;}
    .send-wrap{margin:10px auto 0;}
    .sidenav{position:sticky; top:0; height:100vh; overflow-y:auto;}
  }

  /* ============================================================
     MOBILE (<900px): sidebar becomes bottom tab bar, forms fill width
     ============================================================ */
  @media (max-width:899px){
    .app{flex-direction:column;}
    .sidenav{display:none;}
    .main{
      padding:20px 18px 110px;
      max-width:520px;
      margin:0 auto;
      width:100%;
    }
    .bottom-nav{
      display:flex;
      position:fixed;
      bottom:0; left:0; right:0;
      min-height:var(--bottom-nav-h);
      background:var(--paper-2);
      border-top:1px solid var(--mist);
      justify-content:space-around;
      align-items:center;
      padding:10px 8px calc(14px + env(safe-area-inset-bottom));
      z-index:10;
    }
    .bottom-nav .nav-item{
      flex-direction:column; gap:4px;
      padding:4px 8px;
      color:var(--text-3);
      font-size:10.5px;
      background:none;
      flex:1;
      min-width:0;
    }
    .bottom-nav .nav-item svg{width:20px; height:20px; stroke:var(--text-3);}
    .bottom-nav .nav-item.active{background:none; color:var(--ink);}
    .bottom-nav .nav-item.active svg{stroke:var(--ink);}
    {{-- Same "wrap instead of overflow" fix as .nav-item span.label above —
         several tab labels sitting side by side on a narrow phone screen
         is exactly where a long translation would otherwise run into its
         neighbor. flex:1/min-width:0 on .bottom-nav .nav-item above is
         what gives each tab an even, shrinkable share of the bar to wrap
         within. --}}
    .bottom-nav .nav-item .label{
      display:block; white-space:normal; overflow-wrap:break-word;
      text-align:center; line-height:1.15; max-width:100%;
    }
    .mobile-fab{
      display:flex;
      width:50px; height:50px; margin-top:-32px;
      border-radius:50%;
      background:var(--ink);
      align-items:center; justify-content:center;
      box-shadow:0 8px 18px rgba(16,32,47,0.35);
    }
    .mobile-fab svg{width:20px; height:20px; stroke:var(--paper); stroke-width:1.8;}

    /* send-money specific mobile behavior */
    .send-wrap{max-width:520px; margin:0 auto; width:100%;}
    .pay-btn-wrap.has-fixed-duplicate{display:none;} /* only hide when a .fixed-bottom-btn duplicate exists */
    .fixed-bottom-btn{
      display:block;
      position:fixed; bottom:var(--bottom-nav-h); left:0; right:0;
      background:var(--paper-2);
      border-top:1px solid var(--mist);
      padding:14px 18px;
      z-index:10;
    }
    .fixed-bottom-btn .pay-btn{max-width:520px; margin:0 auto;}
    .send-wrap{padding-bottom:80px;}
  }

  /* extra-narrow phones */
  @media (max-width:380px){
    .balance{font-size:38px;}
    .actions{gap:8px; justify-content:space-between;}
    .action{width:auto;}
    .amount-field input{font-size:38px;}
  }

  /* ============================================================
     DASHBOARD LANGUAGE SWITCHER — a floating pill, always on screen,
     same pattern as the marketing site's header selector (see
     layouts/partials/headers.blade.php + apps.blade.php's .nav-lang-*
     rules) but fixed-position instead of sitting inline in a nav bar,
     since app.blade.php has no single shared topbar every page includes
     (see dashboard.blade.php's own .topbar, which is local to that one
     page). Included once here so it shows up on every authenticated
     page without editing each one individually. Bottom-right corner is
     the one spot nothing else on this layout ever floats in — no
     scroll-to-top or support bubble exists on the dashboard side of the
     app (those are marketing-page-only, see apps.blade.php) — and it
     sits above the bottom tab bar on mobile rather than behind it.
     ============================================================ */
  .dash-lang-float{
    position:fixed; right:16px; bottom:20px; z-index:30;
  }
  .sr-only{
    position:absolute; width:1px; height:1px; padding:0; margin:-1px;
    overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0;
  }
  .dash-lang-select-wrap{
    position:relative; display:flex; align-items:center; cursor:pointer;
  }
  .dash-lang-select{
    appearance:none; -webkit-appearance:none;
    font-family:'Inter', sans-serif; font-size:12.5px; font-weight:600; color:var(--text-1);
    background:var(--paper-2); border:1px solid var(--mist); border-radius:100px;
    box-shadow:0 4px 14px rgba(16,32,47,0.1);
    padding:9px 30px 9px 14px; cursor:pointer; max-width:132px;
    transition:background .15s ease, border-color .15s ease;
  }
  .dash-lang-select:hover, .dash-lang-select:focus{border-color:var(--sage); outline:none;}
  .dash-lang-globe{
    position:absolute; left:12px; width:14px; height:14px;
    stroke:var(--sage); pointer-events:none;
  }
  .dash-lang-select{padding-left:32px;}
  .dash-lang-chev{
    position:absolute; right:10px; width:12px; height:12px;
    stroke:var(--text-3); pointer-events:none;
  }
  @media (max-width:899px){
    .dash-lang-float{bottom:calc(var(--bottom-nav-h) + 14px); right:14px;}
    .dash-lang-select{max-width:118px; font-size:12px;}
  }

  /* ---------- Pagination (shared across any page listing records) ---------- */
  .app-pagination{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    padding:16px 2px 4px;
  }
  .app-pagination-summary{ font-size:12.5px; color:var(--text-2); }
  .app-pagination-links{ display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
  .app-page-link{
    display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px;
    padding:0 8px; border-radius:9px; font-size:12.5px; font-weight:600; color:var(--text-2);
    border:1px solid var(--mist); background:var(--paper-2);
  }
  .app-page-link:hover{ background:var(--sage-light); color:var(--text-1); }
  .app-page-link.active{ background:var(--sage); color:#fff; border-color:var(--sage); }
  .app-page-link.disabled{ opacity:0.4; pointer-events:none; }
  .app-page-ellipsis{ padding:0 4px; color:var(--text-2); font-size:12.5px; }
</style>
</head>
<body>

@include('partials.sweetalert')

<div class="app">

    @include('layouts.partials.header')

    <main class="main">
        @yield('content')
    </main>

</div>

@include('layouts.partials.footer')

{{-- See the .dash-lang-float rules above for why this lives here, floating,
     rather than inside header.blade.php/footer.blade.php themselves — those
     two partials swap for each other at the 900px breakpoint (sidebar vs.
     bottom tab bar) and neither has room to spare for a language picker
     alongside its existing items. Posts to the authenticated
     setting.language.update endpoint — unlike the marketing header's
     guest-facing version, everyone who ever sees this layout is already
     signed in. --}}
<div class="dash-lang-float">
  <form method="POST" action="{{ route('setting.language.update') }}">
    @csrf
    <label class="dash-lang-select-wrap">
      <span class="sr-only">{{ __('welcome.language_label') }}</span>
      <svg class="dash-lang-globe" viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20Z"/></svg>
      <select name="language" class="dash-lang-select" onchange="this.form.submit()">
        @foreach(\App\Http\Controllers\LanguageSettingController::LANGUAGES as $code => $label)
          <option value="{{ $code }}" {{ auth()->user() && auth()->user()->language === $code ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
      <svg class="dash-lang-chev" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </label>
    <noscript><button type="submit" class="btn btn-primary" style="margin-top:8px;">{{ __('welcome.language_label') }}</button></noscript>
  </form>
</div>

</body>
</html>
