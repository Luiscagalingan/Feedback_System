function showLogoutConfirmation() {
  if (document.getElementById('logoutConfirm')) return;
  const modal = document.createElement('div');
  modal.id = 'logoutConfirm';
  modal.style.cssText = 'position:fixed;inset:0;z-index:9999;display:grid;place-items:center;padding:20px;background:rgba(7,21,39,.45);font-family:Arial,Helvetica,sans-serif;';
  modal.innerHTML = '<div role="dialog" aria-modal="true" aria-labelledby="logoutTitle" style="width:min(390px,100%);padding:28px;border-radius:14px;color:#172033;background:#fff;box-shadow:0 20px 50px rgba(0,0,0,.24)"><div style="width:46px;height:46px;display:grid;place-items:center;margin-bottom:16px;border-radius:50%;color:#9b6a00;background:#fff4d7;font-size:24px;font-weight:900">!</div><h2 id="logoutTitle" style="margin:0 0 10px;font-size:20px">Log out?</h2><p style="margin:0;color:#5f6b7e;line-height:1.5">Are you sure you want to log out of your account?</p><div style="display:flex;justify-content:flex-end;gap:10px;margin-top:25px"><button type="button" data-cancel style="min-height:38px;padding:0 15px;border:1px solid #d7dce5;border-radius:7px;color:#40506a;background:#fff;font-weight:800;cursor:pointer">Cancel</button><button type="button" data-confirm style="min-height:38px;padding:0 15px;border:0;border-radius:7px;color:#fff;background:#c23b3b;font-weight:800;cursor:pointer">Yes, log out</button></div></div>';
  const close = () => modal.remove();
  modal.addEventListener('click', event => { if (event.target === modal) close(); });
  modal.querySelector('[data-cancel]').addEventListener('click', close);
  modal.querySelector('[data-confirm]').addEventListener('click', () => { window.location.href = '../index.html'; });
  document.body.appendChild(modal);
  modal.querySelector('[data-cancel]').focus();
}
