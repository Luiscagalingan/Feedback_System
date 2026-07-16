(function () {
  const script = document.currentScript;
  const logoutUrl = script?.dataset.logout || 'auth/logout.php';

  function init() {
    const trigger = document.querySelector('[data-account-menu], #profileInitial, .avatar-dot');
    if (!trigger || trigger.dataset.accountMenuReady) return;
    trigger.dataset.accountMenuReady = 'true';
    trigger.setAttribute('role', 'button');
    trigger.setAttribute('tabindex', '0');
    trigger.setAttribute('aria-label', 'Open account menu');
    trigger.setAttribute('aria-expanded', 'false');

    const style = document.createElement('style');
    style.textContent = '.account-menu-trigger{cursor:pointer}.shared-account-menu{position:fixed;z-index:1000;display:none;width:180px;padding:7px;background:#fff;border:1px solid #e2e5ea;border-radius:10px;box-shadow:0 12px 32px rgba(7,21,39,.18)}.shared-account-menu.open{display:grid}.shared-account-menu button{display:flex;align-items:center;gap:9px;width:100%;padding:10px 12px;border:0;border-radius:7px;color:#172033;background:transparent;text-align:left;font:600 13px Arial,sans-serif;cursor:pointer}.shared-account-menu button:hover{background:#f2f4f7}.shared-account-menu .logout-action{color:#c93636}';
    document.head.appendChild(style);
    trigger.classList.add('account-menu-trigger');

    const menu = document.createElement('div');
    menu.className = 'shared-account-menu';
    menu.innerHTML = '<button type="button" data-account-profile>Profile</button><button type="button" class="logout-action" data-account-logout>Logout</button>';
    document.body.appendChild(menu);

    function position() {
      const rect = trigger.getBoundingClientRect();
      menu.style.top = (rect.bottom + 8) + 'px';
      menu.style.left = Math.max(8, Math.min(window.innerWidth - 188, rect.right - 180)) + 'px';
    }
    function close() { menu.classList.remove('open'); trigger.setAttribute('aria-expanded', 'false'); }
    function toggle(event) { event.stopPropagation(); position(); menu.classList.toggle('open'); trigger.setAttribute('aria-expanded', menu.classList.contains('open') ? 'true' : 'false'); }

    trigger.addEventListener('click', toggle);
    trigger.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggle(event); } });
    menu.querySelector('[data-account-profile]').addEventListener('click', () => {
      close();
      const profileNav = document.querySelector('.nav [data-page="profile"]');
      if (profileNav) profileNav.click();
      else if (typeof window.switchPage === 'function') window.switchPage('profile');
    });
    menu.querySelector('[data-account-logout]').addEventListener('click', () => { close(); AppAlert.logout(logoutUrl); });
    document.addEventListener('click', event => { if (!menu.contains(event.target) && !trigger.contains(event.target)) close(); });
    window.addEventListener('resize', close);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
