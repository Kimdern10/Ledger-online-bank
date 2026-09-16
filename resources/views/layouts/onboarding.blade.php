<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')

        {{-- Ledger onboarding theme: fonts + palette, scoped to this layout only. --}}
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
                --ledger-mist:#DDD9CC;
                --ledger-text-1:#10202F;
                --ledger-text-2:#5C6B72;
                --ledger-text-3:#8C9298;

                /* same Flux accent re-theme as the login/register layout — move into
                   resources/css/app.css instead if a Tailwind @layer is beating this. */
                --color-accent: var(--ledger-sage);
                --color-accent-content: var(--ledger-sage);
                --color-accent-foreground: var(--ledger-paper);
            }

            .ledger-onboarding-shell{
                font-family:'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                background: var(--ledger-paper);
            }
            html.dark .ledger-onboarding-shell{ background: var(--ledger-ink); }

            .ledger-onboarding-page{
                min-height:100svh; display:flex; align-items:center; justify-content:center;
                padding:40px 20px;
                background:
                    radial-gradient(640px circle at 12% 10%, var(--ledger-sage-light), transparent 55%),
                    radial-gradient(520px circle at 90% 90%, var(--ledger-wheat-light), transparent 50%);
            }
            html.dark .ledger-onboarding-page{
                background:
                    radial-gradient(640px circle at 12% 10%, rgba(47,111,98,0.16), transparent 55%),
                    radial-gradient(520px circle at 90% 90%, rgba(201,162,75,0.10), transparent 50%);
            }

            /* ---------- card shell: dark story panel + light form panel ---------- */
            .ledger-wizard-card{
                width:100%; max-width:660px; position:relative;
                display:flex; flex-direction:column;
                background: var(--ledger-paper-2);
                border-radius:22px;
                overflow:hidden;
                box-shadow:0 28px 70px rgba(16,32,47,0.16), 0 2px 0 rgba(255,255,255,0.5) inset;
            }
            html.dark .ledger-wizard-card{
                background: var(--ledger-ink-2);
                box-shadow:0 28px 70px rgba(0,0,0,0.45);
            }
            @media (min-width:1024px){
                .ledger-wizard-card{ max-width:960px; flex-direction:row; align-items:stretch; }
            }

            .ledger-wizard-side{
                position:relative;
                flex-shrink:0;
                display:flex; flex-direction:column; gap:22px;
                padding:34px 30px 26px;
                color: var(--ledger-paper);
                background:
                    radial-gradient(circle at 18% 12%, rgba(47,111,98,0.55), transparent 55%),
                    radial-gradient(circle at 92% 88%, rgba(201,162,75,0.28), transparent 55%),
                    linear-gradient(165deg, #0b1821 0%, #10202F 55%, #17293a 100%);
            }
            @media (min-width:1024px){
                .ledger-wizard-side{
                    width:300px; padding:44px 32px; justify-content:space-between;
                }
            }
            .ledger-wizard-side::after{
                content:'';
                position:absolute; left:24px; right:24px; bottom:0; height:1px;
                background-image: radial-gradient(circle, rgba(246,244,238,0.32) 2px, transparent 2px);
                background-size:14px 1px; background-repeat:repeat-x; background-position:bottom center;
            }
            @media (min-width:1024px){
                .ledger-wizard-side::after{
                    left:auto; right:0; top:28px; bottom:28px; width:1px; height:auto;
                    background-image: radial-gradient(circle, rgba(246,244,238,0.32) 2px, transparent 2px);
                    background-size:1px 14px; background-repeat:repeat-y; background-position:center right;
                }
            }

            .ledger-wizard-side-body{ display:flex; flex-direction:column; gap:22px; }

            .ledger-wizard-brand{
                display:inline-flex; align-items:center; gap:10px; text-decoration:none; width:fit-content;
            }
            .ledger-wizard-mark{
                width:36px; height:36px; border-radius:10px; flex-shrink:0;
                background: linear-gradient(135deg, #3d8c7c 0%, var(--ledger-sage) 55%, #1f4d43 100%);
                color: var(--ledger-paper);
                box-shadow:0 8px 20px rgba(47,111,98,0.4);
                display:flex; align-items:center; justify-content:center;
                font-family:'Newsreader', serif; font-weight:600; font-size:17px;
            }
            .ledger-wizard-word{
                font-family:'Newsreader', serif; font-size:19px; font-weight:600; color: var(--ledger-paper);
            }

            .ledger-wizard-eyebrow{
                font-family:'IBM Plex Mono', monospace; font-size:11px; letter-spacing:0.08em;
                text-transform:uppercase; color: var(--ledger-wheat); margin:0;
            }
            .ledger-wizard-title{
                font-family:'Newsreader', serif; font-size:23px; font-weight:600;
                color: var(--ledger-paper); margin:6px 0 6px; line-height:1.2;
            }
            .ledger-wizard-sub{ font-size:13.5px; line-height:1.55; color: rgba(246,244,238,0.68); margin:0 0 6px; }

            .ledger-wizard-side-note{
                font-size:12px; color: rgba(246,244,238,0.5); margin:0; display:flex; align-items:center; gap:6px;
            }

            /* ---------- vertical step list, in the side panel ---------- */
            .ledger-wizard-vsteps{
                list-style:none; margin:0; padding:0;
                display:flex; flex-direction:row; flex-wrap:wrap; gap:14px 22px;
            }
            @media (min-width:1024px){
                .ledger-wizard-vsteps{ flex-direction:column; gap:18px; }
            }
            .ledger-wizard-vstep{
                display:flex; align-items:center; gap:11px;
            }
            .ledger-wizard-vstep-dot{
                width:28px; height:28px; border-radius:50%; flex-shrink:0;
                border:1.5px solid rgba(246,244,238,0.28);
                background: rgba(246,244,238,0.06); color: rgba(246,244,238,0.65);
                display:flex; align-items:center; justify-content:center;
                font-family:'IBM Plex Mono', monospace; font-size:12px; font-weight:600;
                cursor:default;
                transition:background .2s ease, border-color .2s ease, color .2s ease, transform .15s ease, box-shadow .2s ease;
            }
            .ledger-wizard-vstep-dot svg{ width:13px; height:13px; }
            .ledger-wizard-vstep.is-current .ledger-wizard-vstep-dot{
                border-color: var(--ledger-wheat); color: var(--ledger-wheat);
                background: rgba(201,162,75,0.14);
                box-shadow:0 0 0 4px rgba(201,162,75,0.14);
            }
            .ledger-wizard-vstep.is-done .ledger-wizard-vstep-dot{
                background: var(--ledger-sage); border-color: var(--ledger-sage); color: var(--ledger-paper);
                cursor:pointer;
            }
            .ledger-wizard-vstep.is-done .ledger-wizard-vstep-dot:hover{ transform:scale(1.08); }
            .ledger-wizard-vstep-label{
                font-size:12.5px; font-weight:500; color: rgba(246,244,238,0.55);
                display:none;
            }
            @media (min-width:1024px){
                .ledger-wizard-vstep-label{ display:inline; }
            }
            .ledger-wizard-vstep.is-current .ledger-wizard-vstep-label{ color: var(--ledger-paper); font-weight:600; }
            .ledger-wizard-vstep.is-done .ledger-wizard-vstep-label{ color: rgba(246,244,238,0.85); }

            /* ---------- right: the actual form ---------- */
            .ledger-wizard-main{
                position:relative;
                flex:1; min-width:0;
                padding:30px 26px 30px;
                background:
                    radial-gradient(560px circle at 100% 0%, var(--ledger-sage-light), transparent 55%),
                    var(--ledger-paper-2);
            }
            html.dark .ledger-wizard-main{
                background:
                    radial-gradient(560px circle at 100% 0%, rgba(47,111,98,0.16), transparent 55%),
                    var(--ledger-ink-2);
            }
            @media (min-width:1024px){
                .ledger-wizard-main{ padding:46px 44px 42px; }
            }

            /* ---------- step content + form ---------- */
            .ledger-wizard-step{
                animation: ledgerStepIn .45s cubic-bezier(.16,.84,.44,1) both;
            }
            @keyframes ledgerStepIn{
                from{ opacity:0; transform:translateX(14px); }
                to{ opacity:1; transform:translateX(0); }
            }
            @media (prefers-reduced-motion:reduce){
                .ledger-wizard-step{ animation:none; }
            }

            .ledger-wizard-step-head{ margin-bottom:22px; }
            .ledger-wizard-step-head h2{
                font-family:'Newsreader', serif; font-size:21px; font-weight:600;
                color: var(--ledger-text-1); margin:0 0 4px;
            }
            html.dark .ledger-wizard-step-head h2{ color: var(--ledger-paper); }
            .ledger-wizard-step-head p{
                font-size:13.5px; color: var(--ledger-text-2); margin:0;
            }
            html.dark .ledger-wizard-step-head p{ color:#9FB0BC; }

            .ledger-wizard-grid{
                display:grid; grid-template-columns:1fr 1fr; gap:22px 18px; margin-top:6px;
            }
            @media (max-width:560px){ .ledger-wizard-grid{ grid-template-columns:1fr; } }
            .ledger-wizard-grid .span-2{ grid-column:1 / -1; }

            /* ---------- country / state / city cascade (step 3) ---------- */
            .ledger-location-grid{
                grid-column:1 / -1;
                display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px 16px;
            }
            @media (max-width:640px){ .ledger-location-grid{ grid-template-columns:1fr; } }

            .ledger-field{ display:flex; flex-direction:column; gap:6px; }
            .ledger-field label{
                font-size:13px; font-weight:600; color: var(--ledger-text-2);
            }
            html.dark .ledger-field label{ color:#9FB0BC; }

            .ledger-native-select{
                appearance:none; -webkit-appearance:none;
                width:100%; padding:9px 36px 9px 12px; font-size:14.5px; font-family:inherit;
                color: var(--ledger-text-1); background-color: var(--ledger-paper-2);
                border:1px solid var(--ledger-mist); border-radius:9px;
                background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%235C6B72'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E");
                background-repeat:no-repeat; background-position:right 10px center; background-size:16px;
                transition:border-color .15s ease, box-shadow .15s ease;
            }
            .ledger-native-select:focus{
                outline:none; border-color: var(--ledger-sage);
                box-shadow:0 0 0 3px var(--ledger-sage-light);
            }
            .ledger-native-select:disabled{ opacity:.55; cursor:not-allowed; }
            html.dark .ledger-native-select{
                background-color: var(--ledger-ink-2); border-color: rgba(246,244,238,0.14); color: var(--ledger-paper);
            }

            /* ---------- step 1: read-only recap of what was submitted at registration ---------- */
            .ledger-wizard-profile-card{
                display:flex; align-items:center; gap:16px;
                background: var(--ledger-sage-light);
                border-radius:16px; padding:20px 22px;
            }
            html.dark .ledger-wizard-profile-card{ background: rgba(47,111,98,0.16); }
            .ledger-wizard-profile-avatar{
                width:52px; height:52px; border-radius:50%; flex-shrink:0;
                background: linear-gradient(135deg, #3d8c7c 0%, var(--ledger-sage) 55%, #1f4d43 100%);
                color: var(--ledger-paper);
                box-shadow:0 8px 20px rgba(47,111,98,0.35);
                display:flex; align-items:center; justify-content:center;
                font-family:'Newsreader', serif; font-weight:600; font-size:19px;
            }
            .ledger-wizard-profile-info h3{
                font-family:'Newsreader', serif; font-size:17px; font-weight:600;
                color: var(--ledger-text-1); margin:0 0 3px;
            }
            html.dark .ledger-wizard-profile-info h3{ color: var(--ledger-paper); }
            .ledger-wizard-profile-info p{
                font-size:13px; color: var(--ledger-text-2); margin:0;
            }
            html.dark .ledger-wizard-profile-info p{ color:#9FB0BC; }
            .ledger-wizard-profile-info .dot{ margin:0 6px; opacity:.6; }

            .ledger-wizard-readonly-note{
                font-size:12.5px; color: var(--ledger-text-3); margin:14px 2px 0; line-height:1.5;
            }
            html.dark .ledger-wizard-readonly-note{ color:#7C8B95; }

            .ledger-wizard-actions{
                display:flex; align-items:center; justify-content:space-between;
                margin-top:32px; padding-top:22px; gap:12px;
                border-top:1px solid var(--ledger-mist);
            }
            html.dark .ledger-wizard-actions{ border-color: rgba(246,244,238,0.12); }
            /* same lift-on-hover treatment used for the auth pages' submit buttons */
            .ledger-wizard-actions button[type="submit"]{
                transition: transform .18s ease, box-shadow .18s ease;
            }
            .ledger-wizard-actions button[type="submit"]:hover{
                transform: translateY(-1px);
                box-shadow: 0 10px 24px rgba(47,111,98,0.25);
            }

            .ledger-wizard-review{
                position:relative;
                background: var(--ledger-sage-light);
                border-radius:14px; padding:18px 20px 16px; margin-top:10px;
                font-size:13.5px; color: var(--ledger-text-1);
            }
            html.dark .ledger-wizard-review{ background:rgba(47,111,98,0.16); color: var(--ledger-paper); }
            .ledger-wizard-review::before{
                content:'Review your details';
                display:block;
                font-family:'IBM Plex Mono', monospace; font-size:11px; letter-spacing:0.06em;
                text-transform:uppercase; color: var(--ledger-sage);
                margin-bottom:12px;
            }
            html.dark .ledger-wizard-review::before{ color: var(--ledger-wheat); }
            .ledger-wizard-review dl{
                display:grid; grid-template-columns:auto 1fr; gap:9px 14px; margin:0;
            }
            .ledger-wizard-review dt{ font-weight:600; color: var(--ledger-text-2); }
            html.dark .ledger-wizard-review dt{ color:#9FB0BC; }
            .ledger-wizard-review dd{ margin:0; }
        </style>
    </head>
    <body class="min-h-screen antialiased ledger-onboarding-shell">
        <div class="ledger-onboarding-page">
            {!! $slot !!}
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
