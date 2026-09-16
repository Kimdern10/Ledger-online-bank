<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="description" content="{{ $description ?? 'Ledger is a modern bank built on an old idea: every dollar should be accounted for. Open an account, move money, and grow your balance in one calm, honest ledger.' }}">
<title>{{ $title ?? 'Ledger Banking, kept in order' }}</title>
<link rel="icon" href="{!! asset('img/favicon.svg') !!}" type="image/svg+xml" sizes="18x18">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,72,400;0,72,500;0,72,600;0,72,700;1,72,400&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
@include('partials.sweetalert')
<link rel="stylesheet" href="{!! asset('assets/css/welcome.css') !!}">
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

<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = 'bdb3318fe1ac0c8a22433a5d16c75715f9253052';
// The "back to top" button (#backToTop) sits at bottom:24px/right:24px on
// every screen size, moving to bottom:78px/right:16px only below 480px --
// either way it's 46px tall and close enough to Smartsupp's default ~20px
// offset to crowd/overlap once someone has scrolled and the button shows.
// A width check here would only fix one of those two positions (this is
// exactly what happened last time -- the <=899px check left the desktop
// case, i.e. any "laptop" width above 899px, completely unfixed), so this
// applies unconditionally, on every screen size.
_smartsupp.offsetY = 150;
window.smartsupp||(function(d) {
	var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
	s=d.getElementsByTagName('script')[0];c=d.createElement('script');
	c.type='text/javascript';c.charset='utf-8';c.async=true;
	c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
@auth
smartsupp('name', @json(auth()->user()->name));
smartsupp('email', @json(auth()->user()->email));
@endauth
@if(session('smartsupp_logout'))
smartsupp('logout');
@endif
</script>
<!-- End Smartsupp Live Chat script -->

<script src="{!! asset('assets/js/welcome.js') !!}"></script>

@stack('scripts')
</body>
</html>
