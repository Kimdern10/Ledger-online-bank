<?php /*
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
*/ ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
