<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Online Banking') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,72,400;0,72,500;0,72,600;1,72,400&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('assets/css/dashboard.css') ?>">
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

<?php /* See the .dash-lang-float rules above for why this lives here, floating,
     rather than inside header.blade.php/footer.blade.php themselves — those
     two partials swap for each other at the 900px breakpoint (sidebar vs.
     bottom tab bar) and neither has room to spare for a language picker
     alongside its existing items. Posts to the authenticated
     setting.language.update endpoint — unlike the marketing header's
     guest-facing version, everyone who ever sees this layout is already
     signed in. */ ?>
<div class="dash-lang-float">
  <form method="POST" action="<?= route('setting.language.update') ?>">
    <?= csrf_field() ?>
    <label class="dash-lang-select-wrap">
      <span class="sr-only"><?= e(__('welcome.language_label')) ?></span>
      <svg class="dash-lang-globe" viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20Z"/></svg>
      <select name="language" class="dash-lang-select" onchange="this.form.submit()">
        <?php foreach (\App\Http\Controllers\LanguageSettingController::LANGUAGES as $code => $label): ?>
          <option value="<?= e($code) ?>" <?= (auth()->user() && auth()->user()->language === $code) ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
      <svg class="dash-lang-chev" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </label>
    <noscript><button type="submit" class="btn btn-primary" style="margin-top:8px;"><?= e(__('welcome.language_label')) ?></button></noscript>
  </form>
</div>

<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = 'bdb3318fe1ac0c8a22433a5d16c75715f9253052';
// The floating language picker (.dash-lang-float) sits at bottom:20px on
// EVERY screen size, not just on phones -- it only moves up to bottom:92px
// (to clear the ~90px-tall .bottom-nav tab bar) below the 900px breakpoint.
// Smartsupp's default ~20px offset overlaps the bottom:20px desktop/laptop
// position directly, so a width check here would leave that case (any
// width above 899px) unfixed -- which is exactly what happened last time.
// Applies unconditionally, on every screen size, clearing both positions.
_smartsupp.offsetY = 150;
window.smartsupp||(function(d) {
	var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
	s=d.getElementsByTagName('script')[0];c=d.createElement('script');
	c.type='text/javascript';c.charset='utf-8';c.async=true;
	c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
smartsupp('name', <?= json_encode(auth()->user()->name) ?>);
smartsupp('email', <?= json_encode(auth()->user()->email) ?>);
</script>
<!-- End Smartsupp Live Chat script -->

<script src="<?= asset('assets/js/dashboard.js') ?>"></script>
</body>
</html>
