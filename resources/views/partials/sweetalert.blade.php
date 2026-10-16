{{--
  Loaded once from layouts/app.blade.php and layouts/admin.blade.php, so
  every page that extends either one gets SweetAlert2 automatically.

  Two ways any page can use it:

  1. A plain form that used to have onsubmit="return confirm('...')" —
     just add data-confirm="..." to the <form> instead (no onsubmit at
     all). Optional data-confirm-title, data-confirm-button, and
     data-confirm-danger="1" (for a destructive/red action) let a page
     customize the popup without any extra JS. The delegated listener
     below intercepts the submit, shows the popup, and re-submits the
     form for real once confirmed.

  2. A JS confirm() inside a function (e.g. cards.blade.php's
     reportLostOrStolen()) — call the global ledgerConfirm(text, onConfirm,
     options) helper instead, and put whatever used to run after the old
     `if (confirm(...))` check inside the onConfirm callback.

  3. A short success message that disappears on its own — call
     ledgerToast(text, icon). It slides in at the top with a line along its
     bottom that shrinks as it counts down, then closes itself (e.g. the
     "Your transfer has been sent." on send-receipt.blade.php).
--}}
{{-- Served from this site (copied from node_modules by `npm run copy:vendor`)
     rather than a CDN, which took up to 3 s on a first visit. --}}
<script src="{{ asset('assets/vendor/sweetalert2.all.min.js') }}?v=11.26.25"></script>
<style>
  /* The shrinking countdown line under ledgerToast() — in the app's green. */
  .swal2-timer-progress-bar { background: #2F6F62; }
</style>
<script>
  window.ledgerToast = function(text, icon){
    Swal.fire({
      toast: true,
      position: 'top',
      icon: icon || 'success',
      title: text,
      showConfirmButton: false,
      timer: 3500,
      timerProgressBar: true,
      // Hovering pauses the countdown so it can be read.
      didOpen: function(toast){
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });
  };

  window.ledgerConfirm = function(text, onConfirm, options){
    options = options || {};

    Swal.fire({
      title: options.title || 'Are you sure?',
      text: text,
      icon: options.icon || 'warning',
      showCancelButton: true,
      confirmButtonText: options.confirmButtonText || 'Yes, continue',
      cancelButtonText: 'Cancel',
      confirmButtonColor: options.danger ? '#C1503C' : '#2F6F62',
      cancelButtonColor: '#8C9298',
      reverseButtons: true,
      focusCancel: !!options.danger
    }).then(function(result){
      if (result.isConfirmed) onConfirm();
    });
  };

  document.addEventListener('submit', function(e){
    var form = e.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) return;
    if (form.dataset.confirmed === '1') return; // already confirmed once — let this one through

    e.preventDefault();

    window.ledgerConfirm(form.dataset.confirm, function(){
      form.dataset.confirmed = '1';
      form.submit();
    }, {
      title: form.dataset.confirmTitle,
      confirmButtonText: form.dataset.confirmButton,
      danger: form.dataset.confirmDanger === '1'
    });
  });
</script>
