<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="description" content="{{ $description ?? 'Ledger is a modern bank built on an old idea: every dollar should be accounted for. Open an account, move money, and grow your balance in one calm, honest ledger.' }}">
<title>{{ $title ?? 'Ledger Banking, kept in order' }}</title>
<link rel="icon" href="{{ asset('img/favicon.svg') }}" type="image/svg+xml" sizes="18x18">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,72,400;0,72,500;0,72,600;0,72,700;1,72,400&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
@include('partials.sweetalert')
<style>
  :root{
    --ink:#10202F;
    --ink-2:#1B3145;
    --paper:#F6F4EE;
    --paper-2:#FFFFFF;
    --sage:#2F6F62;
    --sage-light:#E4EEE9;
    --wheat:#C9A24B;
    --wheat-light:#F7EDD9;
    --coral:#C1503C;
    --coral-light:#F5E4DF;
    --mist:#DDD9CC;
    --text-1:#10202F;
    --text-2:#5C6B72;
    --text-3:#8C9298;
    --container:1180px;
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  html{scroll-behavior:smooth;}
  body{
    background:var(--paper);
    font-family:'Inter', sans-serif;
    -webkit-font-smoothing:antialiased;
    color:var(--text-1);
    overflow-x:hidden;
  }
  img{max-width:100%; display:block;}
  a{color:inherit;}
  ul{list-style:none;}
  button{font-family:inherit;}
  .container{max-width:var(--container); margin:0 auto; padding:0 24px;}
  .container-lg{max-width:1320px; margin:0 auto; padding:0 24px;}

  h1,h2,h3,h4{font-family:'Newsreader', serif; font-weight:500; color:var(--text-1); line-height:1.15;}
  .eyebrow{
    display:inline-flex; align-items:center; gap:8px;
    font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.08em;
    color:var(--sage); margin-bottom:14px;
  }
  .eyebrow .dot{width:6px; height:6px; border-radius:50%; background:var(--wheat); flex-shrink:0;}
  .eyebrow.on-dark{color:var(--wheat);}
  .h1{font-size:clamp(34px, 5.2vw, 58px); letter-spacing:-0.01em;}
  .h2{font-size:clamp(28px, 3.6vw, 42px); letter-spacing:-0.01em;}
  .h3{font-size:clamp(20px, 2.2vw, 26px);}
  .lede{font-size:17px; color:var(--text-2); line-height:1.6;}
  .on-dark{color:var(--paper);}
  .on-dark .lede, p.on-dark{color:#B9C8D2;}
  .mono{font-family:'IBM Plex Mono', monospace;}

  /* ============ SECTION SPACING ============ */
  .section{padding:88px 0;}
  .section-sm{padding:56px 0;}
  .section-lg{padding:120px 0;}
  @media (max-width:899px){
    .section{padding:56px 0;}
    .section-lg{padding:72px 0;}
  }
  .section-head{
    display:flex; align-items:flex-end; justify-content:space-between; gap:40px;
    margin-bottom:56px; flex-wrap:wrap;
  }
  .section-head .lede{max-width:420px;}
  @media (max-width:768px){ .section-head{margin-bottom:36px;} }

  /* ============ BUTTONS ============ */
  .btn{
    display:inline-flex; align-items:center; justify-content:center; gap:9px;
    padding:14px 26px; border-radius:100px;
    font-family:'Inter', sans-serif; font-size:14.5px; font-weight:600;
    text-decoration:none; cursor:pointer; border:1px solid transparent;
    transition:background .15s ease, border-color .15s ease, color .15s ease, transform .1s ease;
    white-space:nowrap;
  }
  .btn:active{transform:scale(0.97);}
  .btn svg{width:13px; height:13px; stroke:currentColor; stroke-width:2; flex-shrink:0; transition:transform .2s ease;}
  .btn:hover svg{transform:translate(2px, -2px);}
  .btn-primary{background:var(--sage); color:var(--paper); border-color:var(--sage);}
  .btn-primary:hover{background:#28594e; box-shadow:0 6px 18px rgba(47,111,98,0.32);}
  .btn-wheat{background:var(--wheat); color:var(--ink); border-color:var(--wheat);}
  .btn-wheat:hover{background:#b98f3d; box-shadow:0 6px 18px rgba(201,162,75,0.32);}
  .btn-outline{background:var(--paper-2); color:var(--text-1); border-color:var(--mist);}
  .btn-outline:hover{border-color:var(--sage); color:var(--sage);}
  .btn-outline-white{background:transparent; color:var(--paper); border-color:rgba(246,244,238,0.35);}
  .btn-outline-white:hover{border-color:var(--wheat); color:var(--wheat);}
  .btn-block{width:100%;}

  /* button ripple */
  .btn{position:relative; overflow:hidden;}
  .btn .ripple{
    position:absolute; border-radius:50%; transform:scale(0);
    background:rgba(255,255,255,0.55); animation:rippleEffect .6s ease-out;
    pointer-events:none;
  }
  .btn-outline .ripple, .btn-outline-white .ripple{background:rgba(47,111,98,0.18);}
  @keyframes rippleEffect{ to{transform:scale(2.6); opacity:0;} }

  /* ============ SCROLL PROGRESS ============ */
  .scroll-progress{
    position:fixed; top:0; left:0; height:3px; width:0%; z-index:1200;
    background:linear-gradient(90deg, var(--sage), var(--wheat));
    transition:width .12s ease-out;
  }

  /* ============ BACK TO TOP ============ */
  .back-to-top{
    position:fixed; right:24px; bottom:24px; z-index:900;
    width:46px; height:46px; border-radius:50%; border:1px solid var(--mist);
    background:var(--ink); color:var(--paper); display:flex; align-items:center; justify-content:center;
    cursor:pointer; opacity:0; visibility:hidden; transform:translateY(10px);
    transition:opacity .25s ease, transform .25s ease, visibility .25s ease, background .15s ease;
  }
  .back-to-top.visible{opacity:1; visibility:visible; transform:translateY(0);}
  .back-to-top:hover{background:var(--sage);}
  .back-to-top svg{width:17px; height:17px; stroke:currentColor; stroke-width:2;}

  /* On desktop this sits safely above .gs-launcher (right:24px/bottom:24px
     vs. bottom:86px — see guest-support-widget.blade.php). But that widget
     drops to right:16px/bottom:16px on phones (its own @media rule) while
     this button had no matching override, so the two ended up stacked
     almost exactly on top of each other on mobile. Moving this one up
     instead keeps both visible with a clear gap between them. */
  @media (max-width:480px){
    .back-to-top{ right:16px; bottom:78px; }
  }

  /* ============ HEADER ============ */
  .site-header{
    position:sticky; top:0; z-index:999;
    background:rgba(246,244,238,0.9);
    backdrop-filter:blur(10px);
    border-bottom:1px solid var(--mist);
    transition:box-shadow .25s ease, border-color .25s ease;
  }
  .site-header.scrolled{box-shadow:0 6px 24px rgba(16,32,47,0.08); border-bottom-color:transparent;}
  .header-row{display:flex; align-items:center; justify-content:space-between; gap:24px; padding:16px 0;}
  .brand{display:flex; align-items:center; gap:10px; text-decoration:none; flex-shrink:0;}
  .brand .mark{
    width:34px; height:34px; border-radius:9px; background:var(--ink);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-weight:600; font-size:16px; color:var(--wheat);
  }
  .brand .word{font-family:'Newsreader', serif; font-size:19px; font-weight:600; color:var(--text-1);}

  .main-nav{display:flex; align-items:center; gap:2px;}
  /* the ">" (direct-child) combinator is deliberate: it keeps these nav-link-only
     styles from reaching the "Log in" / "Open an account" buttons nested one level
     deeper inside .nav-mobile-actions, which need their normal .btn colors instead. */
  .main-nav > a{
    position:relative;
    padding:10px 16px; border-radius:100px; text-decoration:none;
    font-size:14px; font-weight:500; color:var(--text-2);
    transition:background .15s ease, color .15s ease;
  }
  .main-nav > a:hover, .main-nav > a.active{background:var(--sage-light); color:var(--sage);}
  .main-nav > a::after{
    content:''; position:absolute; left:16px; right:16px; bottom:3px; height:2px;
    background:var(--wheat); border-radius:2px; transform:scaleX(0); transform-origin:left;
    transition:transform .2s ease;
  }
  .main-nav > a:hover::after, .main-nav > a.active::after{transform:scaleX(1);}

  .header-cta{display:flex; align-items:center; gap:10px; flex-shrink:0;}
  .menu-toggle{
    display:none; width:40px; height:40px; border-radius:50%;
    background:var(--paper-2); border:1px solid var(--mist);
    align-items:center; justify-content:center; cursor:pointer; flex-shrink:0;
    transition:transform .2s ease;
    position:relative; z-index:1001; /* stay above the fixed .main-nav drawer so it's always clickable */
  }
  .menu-toggle svg{width:17px; height:17px; stroke:var(--ink); stroke-width:1.8;}
  .menu-toggle:active{transform:scale(0.92);}

  .nav-mobile-actions{display:none;}

  /* ============ NAV LANGUAGE SELECTOR ============ */
  .sr-only{
    position:absolute; width:1px; height:1px; padding:0; margin:-1px;
    overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0;
  }
  .nav-lang-form{display:flex; align-items:center; margin:0 4px;}
  .nav-lang-select-wrap{position:relative; display:flex; align-items:center; cursor:pointer;}
  .nav-lang-select{
    appearance:none; -webkit-appearance:none;
    font-family:'Inter', sans-serif; font-size:13px; font-weight:500; color:var(--text-2);
    background:transparent; border:1px solid var(--mist); border-radius:100px;
    padding:9px 30px 9px 14px; cursor:pointer;
    transition:background .15s ease, color .15s ease, border-color .15s ease;
  }
  .nav-lang-select:hover, .nav-lang-select:focus{
    background:var(--sage-light); color:var(--sage); border-color:var(--sage); outline:none;
  }
  .nav-lang-chev{
    position:absolute; right:10px; width:13px; height:13px;
    stroke:currentColor; color:var(--text-3); pointer-events:none;
  }
  @media (max-width:960px){
    .nav-lang-form{margin:4px 0 0; width:100%;}
    .nav-lang-select-wrap{width:100%;}
    .nav-lang-select{width:100%; padding:12px 30px 12px 16px;}
  }

  .nav-backdrop{
    display:none; position:fixed; inset:0; background:rgba(16,32,47,0.35);
    z-index:998; opacity:0; transition:opacity .25s ease;
  }
  .nav-backdrop.open{display:block; opacity:1;}

  @media (max-width:960px){
    .main-nav{
      position:fixed; inset:0 0 0 auto; width:min(320px, 84vw); height:100vh;
      background:var(--paper-2); flex-direction:column; align-items:stretch;
      padding:90px 24px 24px; gap:4px; transform:translateX(100%);
      transition:transform .3s cubic-bezier(.4,0,.2,1); box-shadow:-10px 0 30px rgba(16,32,47,0.12);
      overflow-y:auto; z-index:1000;
    }
    .main-nav.open{transform:translateX(0);}
    .main-nav > a{padding:14px 16px;}
    .main-nav > a::after{display:none;}
    .menu-toggle{display:flex;}
    .header-cta .btn-outline, .header-cta .btn-primary{display:none;}
    .nav-mobile-actions{
      display:flex; flex-direction:column; gap:10px;
      margin-top:20px; padding-top:20px; border-top:1px solid var(--mist);
    }
  }

  /* ============ LEDGER CARD (shared motif from the app) ============ */
  .ledger-card{
    background:var(--ink); border-radius:22px; color:var(--paper);
    position:relative; overflow:hidden; padding:36px;
  }
  .ledger-card::before{
    content:''; position:absolute; top:0; left:0; right:0; height:10px;
    background-image:radial-gradient(circle, var(--paper) 3px, transparent 3.5px);
    background-size:16px 10px; background-position:-2px -5px; background-repeat:repeat-x;
  }
  .ledger-card .k-label{font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:#9FB3C2; font-weight:600;}
  .ledger-card .k-value{font-family:'Newsreader', serif; font-size:40px; font-weight:500; margin-top:8px;}
  .ledger-card .k-underline{width:48px; height:2px; background:var(--wheat); margin:14px 0;}
  .chip{
    font-family:'IBM Plex Mono', monospace; font-size:11px; color:#B9C8D2;
    background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14);
    padding:4px 11px; border-radius:20px; display:inline-block;
  }

  /* generic bordered card */
  .card{background:var(--paper-2); border:1px solid var(--mist); border-radius:18px;}

  /* ============ TICKER ============ */
  .ticker-strip{background:var(--sage); padding:14px 0; overflow:hidden;}
  .ticker-track{display:flex; align-items:center; gap:36px; width:max-content; animation:tickerScroll 32s linear infinite;}
  .ticker-track span{
    font-family:'Inter', sans-serif; font-size:14px; font-weight:600; color:var(--paper);
    white-space:nowrap; display:flex; align-items:center; gap:36px;
  }
  .ticker-track span::after{content:'✦'; color:var(--wheat); font-size:12px;}
  @keyframes tickerScroll{ from{transform:translateX(0);} to{transform:translateX(-50%);} }
  @media (prefers-reduced-motion:reduce){ .ticker-track{animation:none;} }

  /* ============ FOOTER ============ */
  .site-footer{background:var(--ink); color:var(--paper); padding:76px 0 0;}
  .footer-top{
    display:flex; justify-content:space-between; gap:40px; padding-bottom:48px;
    border-bottom:1px solid rgba(255,255,255,0.1); flex-wrap:wrap;
  }
  .footer-brand{max-width:340px;}
  .footer-brand p{color:#9FB0BC; margin-top:16px; font-size:14.5px; line-height:1.6;}
  .footer-cols{display:flex; gap:56px; flex-wrap:wrap;}
  .footer-col h4{font-family:'Newsreader', serif; font-weight:500; font-size:16px; margin-bottom:16px; color:var(--paper);}
  .footer-col a{
    display:block; color:#9FB0BC; text-decoration:none; font-size:14px; margin-bottom:11px;
    transition:color .15s ease;
  }
  .footer-col a:hover{color:var(--wheat);}
  .footer-bottom{
    display:flex; justify-content:space-between; align-items:center; gap:16px;
    padding:26px 0; flex-wrap:wrap; font-size:13px; color:#8494A0;
  }
  .footer-social{display:flex; gap:10px;}
  .footer-social a{
    width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.06);
    display:flex; align-items:center; justify-content:center; color:var(--paper);
    text-decoration:none; transition:background .15s ease;
  }
  .footer-social a:hover{background:var(--sage);}
  .footer-social svg{width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:1.7;}

  /* ============ SHARED GRID / UTILITIES used across sections ============ */
  .grid-2{display:grid; grid-template-columns:1fr 1fr; gap:24px;}
  .grid-3{display:grid; grid-template-columns:repeat(3,1fr); gap:24px;}
  @media (max-width:900px){ .grid-2, .grid-3{grid-template-columns:1fr;} }
  @media (min-width:601px) and (max-width:900px){ .grid-3{grid-template-columns:1fr 1fr;} }

  /* scroll-triggered now, via IntersectionObserver below — elements sit hidden
     until they enter the viewport, instead of all animating at once on load
     (which made anything below the fold "finish" animating before you ever saw it). */
  .fade-up{
    opacity:0; transform:translateY(14px);
    transition:opacity .7s cubic-bezier(.16,.84,.44,1), transform .7s cubic-bezier(.16,.84,.44,1);
  }
  .fade-up.in-view{opacity:1; transform:translateY(0);}
  @media (prefers-reduced-motion:reduce){ .fade-up{opacity:1; transform:none; transition:none;} }

  /* ============ PRELOADER ============ */
  .ledger-loader{
    position:fixed; inset:0; z-index:2000;
    display:flex; flex-direction:column; align-items:center; justify-content:center; gap:20px;
    background:var(--ink);
    transition:opacity .5s ease, visibility .5s ease;
  }
  .ledger-loader.is-hidden{opacity:0; visibility:hidden; pointer-events:none;}
  .ledger-loader-mark{
    position:relative;
    width:60px; height:60px; border-radius:16px;
    background:var(--sage);
    display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader', serif; font-weight:600; font-size:28px; color:var(--paper);
    box-shadow:0 10px 30px rgba(47,111,98,0.4);
    animation:ledgerLoaderPulse 1.3s ease-in-out infinite;
  }
  .ledger-loader-ring{
    position:absolute; inset:-8px; border-radius:20px;
    border:2px solid transparent; border-top-color:var(--wheat); border-right-color:var(--wheat);
    animation:ledgerLoaderSpin 1s linear infinite;
  }
  .ledger-loader-label{
    font-family:'IBM Plex Mono', monospace; font-size:11px; letter-spacing:.14em; text-transform:uppercase;
    color:#9FB3C2;
  }
  @keyframes ledgerLoaderPulse{ 0%,100%{transform:scale(1);} 50%{transform:scale(1.08);} }
  @keyframes ledgerLoaderSpin{ to{transform:rotate(360deg);} }
  @media (prefers-reduced-motion:reduce){
    .ledger-loader-mark, .ledger-loader-ring{animation:none;}
  }
</style>
@stack('styles')
</head>
<body>

<div class="ledger-loader" id="ledgerLoader">
  <div class="ledger-loader-mark">
    <div class="ledger-loader-ring"></div>
    L
  </div>
  <span class="ledger-loader-label">Ledger</span>
</div>

<div class="scroll-progress" id="scrollProgress"></div>

@include('layouts.partials.headers')

<main>
@yield('content')
</main>

@include('layouts.partials.footers')

<button class="back-to-top" id="backToTop" aria-label="Back to top">
  <svg viewBox="0 0 24 24" fill="none"><path d="M12 19V5M5 12l7-7 7 7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
</button>

@include('layouts.partials.guest-support-widget')

<script>
  // ---------- Preloader ----------
  // Hides once the page (images, fonts, etc.) has actually finished loading, with
  // a timed fallback in case a slow third-party asset holds the 'load' event up.
  (function(){
    var loader = document.getElementById('ledgerLoader');
    if(!loader) return;

    document.documentElement.style.overflow = 'hidden';

    function hideLoader(){
      loader.classList.add('is-hidden');
      document.documentElement.style.overflow = '';
      window.setTimeout(function(){
        if(loader.parentNode) loader.parentNode.removeChild(loader);
      }, 550);
    }

    if(document.readyState === 'complete'){
      hideLoader();
    } else {
      window.addEventListener('load', hideLoader);
      window.setTimeout(hideLoader, 2500);
    }
  })();

  // ---------- Scroll-triggered reveal for .fade-up elements ----------
  // Replaces the old "animate once on page load" behavior: elements now stay
  // hidden until they actually scroll into view.
  (function(){
    var items = document.querySelectorAll('.fade-up');
    if(!items.length) return;

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if(reduceMotion || !('IntersectionObserver' in window)){
      items.forEach(function(el){ el.classList.add('in-view'); });
      return;
    }

    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(!entry.isIntersecting) return;
        entry.target.classList.add('in-view');
        io.unobserve(entry.target);
      });
    }, {threshold:0.15, rootMargin:'0px 0px -8% 0px'});

    items.forEach(function(el){ io.observe(el); });
  })();

  // ---------- Magnetic primary/wheat buttons ----------
  // A subtle cursor-follow nudge on the main call-to-action buttons, desktop +
  // real pointer only.
  (function(){
    var canHover = window.matchMedia && window.matchMedia('(hover: hover)').matches;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if(!canHover || reduceMotion) return;

    document.querySelectorAll('.btn-primary, .btn-wheat').forEach(function(btn){
      btn.addEventListener('mousemove', function(e){
        var r = btn.getBoundingClientRect();
        var x = (e.clientX - r.left - r.width / 2) * 0.12;
        var y = (e.clientY - r.top - r.height / 2) * 0.25;
        btn.style.transform = 'translate(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px)';
      });
      btn.addEventListener('mouseleave', function(){
        btn.style.transform = '';
      });
    });
  })();

  // ---------- Scroll progress bar + sticky header shadow + back-to-top ----------
  (function(){
    var progress = document.getElementById('scrollProgress');
    var header = document.querySelector('.site-header');
    var backToTop = document.getElementById('backToTop');
    var ticking = false;

    function onScroll(){
      var scrollTop = window.scrollY || document.documentElement.scrollTop;
      var docHeight = document.documentElement.scrollHeight - window.innerHeight;
      var pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      if(progress) progress.style.width = pct + '%';
      if(header) header.classList.toggle('scrolled', scrollTop > 8);
      if(backToTop) backToTop.classList.toggle('visible', scrollTop > 600);
      ticking = false;
    }

    window.addEventListener('scroll', function(){
      if(!ticking){
        requestAnimationFrame(onScroll);
        ticking = true;
      }
    }, {passive:true});
    onScroll();

    if(backToTop){
      backToTop.addEventListener('click', function(){
        window.scrollTo({top:0, behavior:'smooth'});
      });
    }
  })();

  // ---------- Button ripple effect ----------
  (function(){
    document.addEventListener('click', function(e){
      var btn = e.target.closest('.btn');
      if(!btn) return;
      var rect = btn.getBoundingClientRect();
      var size = Math.max(rect.width, rect.height);
      var ripple = document.createElement('span');
      ripple.className = 'ripple';
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
      ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
      btn.appendChild(ripple);
      ripple.addEventListener('animationend', function(){ ripple.remove(); });
    });
  })();
</script>

@stack('scripts')
</body>
</html>
