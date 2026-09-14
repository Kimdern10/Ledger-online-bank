<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')

        {{-- Ledger auth theme: fonts + palette, scoped to this layout only. --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,600;1,400&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

        <style>
            :root{
                --ledger-ink:#10202F;
                --ledger-ink-2:#1B3145;
                --ledger-paper:#F6F4EE;
                --ledger-paper-2:#FFFFFF;
                --ledger-sage:#2F6F62;
                --ledger-sage-light:#E4EEE9;
                --ledger-wheat:#C9A24B;
                --ledger-wheat-light:#F7EDD9;
                --ledger-coral:#C1503C;
                --ledger-coral-light:#F5E4DF;
                --ledger-mist:#DDD9CC;
                --ledger-text-1:#10202F;
                --ledger-text-2:#5C6B72;
                --ledger-text-3:#8C9298;

                /*
                 * Re-theme Flux's accent color to Ledger sage for every flux:* component
                 * rendered inside this layout (buttons, checkboxes, links, focus rings).
                 * If your buttons don't pick up sage after this change, your build likely
                 * declares these inside a Tailwind @layer, which beats a later unlayered
                 * override — move this block into resources/css/app.css instead.
                 */
                --color-accent: var(--ledger-sage);
                --color-accent-content: var(--ledger-sage);
                --color-accent-foreground: var(--ledger-paper);
            }

            .ledger-auth-shell{
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                background: var(--ledger-paper);
            }

            @keyframes ledgerFadeUp{
                from{ opacity:0; transform:translateY(16px); }
                to{ opacity:1; transform:translateY(0); }
            }
            @keyframes ledgerDrift{
                0%,100%{ transform:translate(0,0) scale(1); }
                50%{ transform:translate(22px,16px) scale(1.08); }
            }
            @keyframes ledgerBob{
                0%,100%{ transform:translateY(0); }
                50%{ transform:translateY(-7px); }
            }
            @media (prefers-reduced-motion: reduce){
                .ledger-auth-panel-inner > *,
                .ledger-auth-panel-inner li,
                .ledger-auth-form-wrap > *,
                .ledger-auth-orb,
                .ledger-auth-card-mock-wrap{ animation:none !important; }
            }

            /* ---------- brand lockup (reused in the side panel + the mobile header) ---------- */
            .ledger-auth-brand{ display:flex; align-items:center; gap:12px; text-decoration:none; }
            .ledger-auth-mark{
                display:flex; align-items:center; justify-content:center; flex-shrink:0;
                width:40px; height:40px; border-radius:11px;
                background: linear-gradient(135deg, #3d8c7c 0%, var(--ledger-sage) 55%, #1f4d43 100%);
                color: var(--ledger-paper);
                box-shadow: 0 8px 20px rgba(47,111,98,0.4);
                font-family:'Newsreader', serif;
                font-weight:600;
                font-size:19px;
            }
            .ledger-auth-word{
                font-family:'Newsreader', serif;
                font-size:20px; font-weight:600; letter-spacing:-0.01em;
                color: var(--ledger-ink);
            }
            html.dark .ledger-auth-word{ color: var(--ledger-paper); }

            /* ---------- modern split layout ---------- */
            .ledger-auth-grid{
                min-height:100svh;
                display:grid;
                grid-template-columns: 1fr;
            }
            @media (min-width:1024px){
                .ledger-auth-grid{ grid-template-columns: 1.05fr 1fr; }
            }

            /* left: brand / story panel — always dark ink, independent of light/dark mode,
               so the sage + wheat accents stay consistent and the split reads as intentional. */
            .ledger-auth-panel{
                display:none;
                position:relative;
                overflow:hidden;
                background: linear-gradient(165deg, #0b1821 0%, #10202F 52%, #17293a 100%);
                color: var(--ledger-paper);
                padding: 56px 48px;
                perspective: 900px;
            }
            @media (min-width:1024px){
                .ledger-auth-panel{ display:flex; }
            }
            /* ambient, always-moving glows — separate elements from the tilting card
               so the JS-driven tilt transform and this CSS animation never fight over
               the same "transform" property on the same node. */
            .ledger-auth-orb{
                position:absolute; border-radius:50%; filter:blur(60px);
                pointer-events:none; opacity:.6;
            }
            .ledger-auth-orb-a{
                width:280px; height:280px; top:-70px; left:-70px;
                background: radial-gradient(circle, rgba(47,111,98,0.55), transparent 70%);
                animation: ledgerDrift 12s ease-in-out infinite;
            }
            .ledger-auth-orb-b{
                width:220px; height:220px; bottom:-50px; right:-50px;
                background: radial-gradient(circle, rgba(201,162,75,0.35), transparent 70%);
                animation: ledgerDrift 15s ease-in-out infinite reverse;
            }
            .ledger-auth-panel .ledger-auth-word{ color: var(--ledger-paper); }
            .ledger-auth-panel-inner{
                display:flex; flex-direction:column; justify-content:space-between;
                height:100%; width:100%; max-width:420px; gap:40px;
            }
            .ledger-auth-panel-inner > *{
                animation: ledgerFadeUp .7s cubic-bezier(.16,.84,.44,1) both;
            }
            .ledger-auth-panel-inner > *:nth-child(1){ animation-delay:.05s; }
            .ledger-auth-panel-inner > *:nth-child(2){ animation-delay:.16s; }
            .ledger-auth-panel-inner > *:nth-child(3){ animation-delay:.27s; }
            .ledger-auth-panel-inner > *:nth-child(4){ animation-delay:.38s; }

            .ledger-auth-eyebrow{
                font-family:'IBM Plex Mono', monospace;
                font-size:12px; letter-spacing:0.08em; text-transform:uppercase;
                color: var(--ledger-wheat);
                margin:0 0 14px;
            }
            .ledger-auth-headline{
                font-family:'Newsreader', serif;
                font-size:38px; line-height:1.15; font-weight:500;
                color: var(--ledger-paper);
                margin:0 0 16px;
            }
            .ledger-auth-headline .accent{ color: var(--ledger-wheat); font-style:italic; }
            .ledger-auth-sub{
                font-size:15px; line-height:1.6;
                color: rgba(246,244,238,0.68);
                max-width:360px;
                margin:0;
            }

            /*
             * Three nested elements on purpose, each owning its own "animation" property
             * so none of them silently override each other via the CSS cascade:
             *   -wrap  → entrance fade (set by the .ledger-auth-panel-inner > * rule below)
             *   -bob   → the continuous idle float, infinite
             *   -mock  → untouched by CSS animation; only ever gets an inline transform
             *            from the mousemove tilt script.
             */
            .ledger-auth-card-mock-bob{
                animation: ledgerBob 4.5s ease-in-out infinite;
            }
            .ledger-auth-card-mock{
                position:relative;
                background: rgba(246,244,238,0.06);
                border:1px solid rgba(246,244,238,0.14);
                border-radius:14px;
                padding:22px 20px 18px;
                transform-style:preserve-3d;
                transition: transform .25s ease-out;
                will-change: transform;
            }
            .ledger-auth-card-mock::before{
                content:'';
                position:absolute; top:0; left:20px; right:20px; height:1px;
                background-image: radial-gradient(circle, var(--ledger-ink) 2.5px, transparent 2.5px);
                background-size: 16px 1px;
                background-repeat: repeat-x;
                background-position: top center;
                transform: translateY(-1px);
            }
            .ledger-auth-card-mock-row{
                display:flex; justify-content:space-between; align-items:baseline;
                font-family:'IBM Plex Mono', monospace; font-size:14px;
                padding:7px 0;
            }
            .ledger-auth-card-mock-row:first-child{ font-size:21px; font-weight:500; color:var(--ledger-paper); padding-top:2px; }
            .ledger-auth-card-mock-row.muted{
                color: rgba(246,244,238,0.55);
                border-top:1px dashed rgba(246,244,238,0.18);
                margin-top:2px;
            }

            .ledger-auth-entries{
                list-style:none; margin:0; padding-top:18px;
                border-top:1px solid rgba(246,244,238,0.14);
                display:flex; flex-direction:column; gap:14px;
            }
            .ledger-auth-entries li{
                display:flex; align-items:baseline; gap:12px;
                font-size:14px; color: rgba(246,244,238,0.75);
                animation: ledgerFadeUp .5s cubic-bezier(.16,.84,.44,1) both;
            }
            .ledger-auth-entries li:nth-child(1){ animation-delay:.5s; }
            .ledger-auth-entries li:nth-child(2){ animation-delay:.58s; }
            .ledger-auth-entries li:nth-child(3){ animation-delay:.66s; }
            .ledger-auth-entries .num{
                font-family:'IBM Plex Mono', monospace;
                font-size:12px;
                color: var(--ledger-wheat);
                flex-shrink:0;
            }

            /* right: the actual form, unboxed — the split itself does the framing */
            .ledger-auth-form-side{
                display:flex; align-items:center; justify-content:center;
                padding: 40px 24px;
                background:
                    radial-gradient(720px circle at 100% 0%, var(--ledger-sage-light), transparent 55%),
                    var(--ledger-paper);
            }
            html.dark .ledger-auth-form-side{
                background:
                    radial-gradient(720px circle at 100% 0%, rgba(47,111,98,0.14), transparent 55%),
                    var(--ledger-ink);
            }

            .ledger-auth-form-wrap{
                position:relative;
                width:100%; max-width:380px;
                display:flex; flex-direction:column; gap:32px;
            }
            /* reacts to any field inside the form being focused — a soft glow behind
               the whole form, visible on every breakpoint (not just the ≥1024px panel). */
            .ledger-auth-form-wrap::before{
                content:'';
                position:absolute; inset:-28px; z-index:-1; border-radius:28px;
                background: radial-gradient(420px circle at 50% 30%, var(--ledger-sage-light), transparent 70%);
                opacity:0;
                transition: opacity .4s ease;
            }
            html.dark .ledger-auth-form-wrap::before{
                background: radial-gradient(420px circle at 50% 30%, rgba(47,111,98,0.2), transparent 70%);
            }
            .ledger-auth-form-wrap:focus-within::before{ opacity:1; }
            .ledger-auth-form-wrap :where(h1,h2,h3,h4){ font-family:'Newsreader', serif; }
            .ledger-auth-form-wrap > *{
                animation: ledgerFadeUp .6s cubic-bezier(.16,.84,.44,1) both;
            }
            .ledger-auth-form-wrap > *:nth-child(1){ animation-delay:.05s; }
            .ledger-auth-form-wrap > *:nth-child(2){ animation-delay:.12s; }
            .ledger-auth-form-wrap > *:nth-child(3){ animation-delay:.19s; }
            .ledger-auth-form-wrap > *:nth-child(4){ animation-delay:.26s; }
            .ledger-auth-form-wrap > *:nth-child(5){ animation-delay:.33s; }
            .ledger-auth-form-wrap > *:nth-child(6){ animation-delay:.40s; }

            /* scoped to submit buttons only, so the tiny password-visibility toggle
               button inside flux:input doesn't get the same lift/shadow treatment. */
            .ledger-auth-form-wrap button[type="submit"]{
                transition: transform .18s ease, box-shadow .18s ease;
            }
            .ledger-auth-form-wrap button[type="submit"]:hover{
                box-shadow: 0 10px 24px rgba(47,111,98,0.25);
            }

            .ledger-auth-brand-mobile{ }
            @media (min-width:1024px){
                .ledger-auth-brand-mobile{ display:none; }
            }

            /* Two-per-row field pairing (e.g. First/Last name, Middle name/Phone
               on the register form). Written as plain CSS instead of Tailwind's
               grid utilities because those weren't taking effect in this build —
               everything in this <style> block is proven to render reliably on
               this page, so the layout lives here instead. */
            .ledger-auth-field-grid{
                display:grid;
                grid-template-columns: 1fr;
                gap:20px;
            }
            @media (min-width:640px){
                .ledger-auth-field-grid{
                    grid-template-columns: 1fr 1fr;
                    gap:20px 16px;
                }
            }
        </style>
    </head>
    <body class="min-h-screen antialiased ledger-auth-shell">
        <div class="ledger-auth-grid">

            <div class="ledger-auth-panel">
                <div class="ledger-auth-orb ledger-auth-orb-a"></div>
                <div class="ledger-auth-orb ledger-auth-orb-b"></div>

                <div class="ledger-auth-panel-inner">
                    <a href="{{ route('home') }}" class="ledger-auth-brand" wire:navigate>
                        <span class="ledger-auth-mark">L</span>
                        <span class="ledger-auth-word">Ledger</span>
                    </a>

                    <div>
                        <p class="ledger-auth-eyebrow">{{ __('authpage.panel_eyebrow') }}</p>
                        <h2 class="ledger-auth-headline">{{ __('authpage.panel_headline_line1') }}<br><span class="accent">{{ __('authpage.panel_headline_accent') }}</span></h2>
                        <p class="ledger-auth-sub">{{ __('authpage.panel_sub') }}</p>
                    </div>

                    <div class="ledger-auth-card-mock-wrap">
                        <div class="ledger-auth-card-mock-bob">
                            <div class="ledger-auth-card-mock" id="ledgerCardMock">
                                <div class="ledger-auth-card-mock-row">
                                    <span>{{ __('authpage.mock_balance_label') }}</span>
                                    <strong data-count="24180.55">$0.00</strong>
                                </div>
                                <div class="ledger-auth-card-mock-row muted">
                                    <span>{{ __('authpage.mock_account_label') }}</span>
                                    <span>•••• 4471</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="ledger-auth-entries">
                        <li><span class="num">01</span><span>{{ __('authpage.entry_fdic') }}</span></li>
                        <li><span class="num">02</span><span>{{ __('authpage.entry_no_fees') }}</span></li>
                        <li><span class="num">03</span><span>{{ __('authpage.entry_instant') }}</span></li>
                    </ul>
                </div>
            </div>

            <div class="ledger-auth-form-side">
                <div class="ledger-auth-form-wrap">
                    <a href="{{ route('home') }}" class="ledger-auth-brand ledger-auth-brand-mobile" wire:navigate>
                        <span class="ledger-auth-mark">L</span>
                        <span class="ledger-auth-word">Ledger</span>
                    </a>

                    {{ $slot }}
                </div>
            </div>

        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        <script>
            (function(){
                var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                // subtle mouse-follow tilt on the mocked-up ledger card, mirroring the
                // hero card tilt on the marketing site. Desktop + real pointer only.
                var mock = document.getElementById('ledgerCardMock');
                var panel = document.querySelector('.ledger-auth-panel');
                var canHover = window.matchMedia && window.matchMedia('(hover: hover)').matches;

                if(mock && panel && canHover && !reduceMotion){
                    panel.addEventListener('mousemove', function(e){
                        var rect = panel.getBoundingClientRect();
                        var x = (e.clientX - rect.left) / rect.width - 0.5;
                        var y = (e.clientY - rect.top) / rect.height - 0.5;
                        mock.style.transform = 'rotateY(' + (x * 8).toFixed(2) + 'deg) rotateX(' + (y * -8).toFixed(2) + 'deg)';
                    });
                    panel.addEventListener('mouseleave', function(){
                        mock.style.transform = 'rotateY(0deg) rotateX(0deg)';
                    });
                }

                // magnetic buttons: the submit button nudges slightly toward the cursor
                // as you approach it. Runs on the form side, so it's visible on every
                // breakpoint, not just the desktop panel.
                var canHoverForm = window.matchMedia && window.matchMedia('(hover: hover)').matches;
                if(canHoverForm && !reduceMotion){
                    document.querySelectorAll('.ledger-auth-form-wrap button[type="submit"]').forEach(function(btn){
                        btn.addEventListener('mousemove', function(e){
                            var r = btn.getBoundingClientRect();
                            var x = (e.clientX - r.left - r.width / 2) * 0.15;
                            var y = (e.clientY - r.top - r.height / 2) * 0.35;
                            btn.style.transform = 'translate(' + x.toFixed(1) + 'px,' + (y - 1).toFixed(1) + 'px)';
                        });
                        btn.addEventListener('mouseleave', function(){
                            btn.style.transform = '';
                        });
                    });
                }

                // count up the mocked balance on load, same easing pattern as the
                // homepage's stat counters.
                var balanceEl = mock ? mock.querySelector('strong[data-count]') : null;
                if(balanceEl){
                    var target = parseFloat(balanceEl.getAttribute('data-count'));
                    if(reduceMotion){
                        balanceEl.textContent = '$' + target.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
                    } else {
                        var duration = 1100;
                        var start = null;
                        var step = function(ts){
                            if(!start) start = ts;
                            var progress = Math.min((ts - start) / duration, 1);
                            var eased = 1 - Math.pow(1 - progress, 3);
                            balanceEl.textContent = '$' + (target * eased).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
                            if(progress < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                    }
                }
            })();
        </script>

        @fluxScripts
    </body>
</html>
