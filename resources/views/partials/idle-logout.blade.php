{{--
  Signs a customer or admin out after config('session.idle_timeout') minutes
  with no activity (clicks, taps, typing, scrolling) in ANY of their open tabs
  — the last activity time is shared between tabs through localStorage. A
  minute before, a SweetAlert warning counts down with a "Stay signed in"
  button. Signing out posts to the normal logout route, then opens the login
  page with ?timeout=1 so it can say why. Needs partials.sweetalert first.
--}}
@auth
  @if(config('session.idle_timeout') > 0)
    <script>
      (function () {
        var IDLE_MS = {{ (int) config('session.idle_timeout') }} * 60 * 1000;
        var WARN_MS = Math.min(60 * 1000, IDLE_MS / 2);
        var KEY = 'ledgerLastActivity';
        var logoutUrl = @json(route('logout'));
        var loginUrl = @json(route('login', ['timeout' => 1]));
        var token = @json(csrf_token());
        var lastActivity = Date.now();
        var warning = false;
        var signingOut = false;

        function store(value) {
          try { localStorage.setItem(KEY, String(value)); } catch (e) {}
        }

        function sharedLastActivity() {
          var stored = 0;
          try { stored = parseInt(localStorage.getItem(KEY), 10) || 0; } catch (e) {}
          return Math.max(lastActivity, stored);
        }

        // Activity: remember it (at most every few seconds for mouse moves and
        // scrolling) and close the warning if it's showing.
        function activity() {
          var now = Date.now();
          if (now - lastActivity < 3000 && !warning) return;
          lastActivity = now;
          store(now);
          if (warning) {
            warning = false;
            if (window.Swal) Swal.close();
          }
        }

        // 'wheel' rather than 'scroll': the page can scroll by itself (smooth
        // scrolling, carousels), but only a person turns the wheel.
        ['click', 'keydown', 'touchstart', 'wheel', 'mousemove'].forEach(function (type) {
          window.addEventListener(type, activity, { passive: true });
        });

        function signOut() {
          if (signingOut) return;
          signingOut = true;
          fetch(logoutUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'text/html' }
          }).catch(function () {}).then(function () {
            window.location.href = loginUrl;
          });
        }

        function showWarning(secondsLeft) {
          warning = true;
          if (!window.Swal) return;
          Swal.fire({
            icon: 'warning',
            title: @json(__('authpage.idle_warning_title')),
            html: @json(__('authpage.idle_warning_body')) + ' <strong id="idleCountdown">' + secondsLeft + '</strong>s',
            confirmButtonText: @json(__('authpage.idle_stay_signed_in')),
            confirmButtonColor: '#2F6F62',
            allowOutsideClick: false
          }).then(function (result) {
            if (result.isConfirmed) {
              warning = true; // so activity() resets the clock right away
              activity();
            }
          });
        }

        setInterval(function () {
          var idle = Date.now() - sharedLastActivity();
          var left = Math.ceil((IDLE_MS - idle) / 1000);

          if (idle >= IDLE_MS) {
            signOut();
          } else if (idle >= IDLE_MS - WARN_MS) {
            if (!warning) showWarning(left);
            var counter = document.getElementById('idleCountdown');
            if (counter) counter.textContent = left;
          } else if (warning) {
            // Another tab saw activity: put this one back to normal too.
            warning = false;
            if (window.Swal) Swal.close();
          }
        }, 1000);

        store(lastActivity);
      })();
    </script>
  @endif
@endauth
