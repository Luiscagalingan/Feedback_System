(function (global) {
  let loader;
  function ready() {
    if (global.Swal) return Promise.resolve(global.Swal);
    if (!loader) loader = new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
      script.onload = () => resolve(global.Swal);
      script.onerror = () => reject(new Error('SweetAlert2 could not be loaded.'));
      document.head.appendChild(script);
    });
    return loader;
  }
  const style = document.createElement('style');
  style.textContent = '.plp-alert{font-family:Arial,Helvetica,sans-serif!important;border-radius:14px!important;padding:24px!important}.plp-alert .swal2-actions{gap:10px}.plp-alert .swal2-confirm,.plp-alert .swal2-cancel{min-height:40px;padding:0 18px;border-radius:8px!important;font-weight:800}.plp-alert .swal2-cancel{color:#40506a!important}';
  document.head.appendChild(style);
  const base = {customClass:{popup:'plp-alert'},confirmButtonColor:'#878786',cancelButtonColor:'#e7e9ed',buttonsStyling:true,heightAuto:false,reverseButtons:true,focusCancel:true};
  async function fire(options) { const Swal = await ready(); return Swal.fire(Object.assign({}, base, options)); }
  function escapeHtml(value) { return String(value??'').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char])); }
  global.AppAlert = {
    success:(title,text)=>fire({icon:'success',title:title||'Success',text:text||'',confirmButtonText:'Done',confirmButtonColor:'#16a36a'}),
    error:(title,text)=>fire({icon:'error',title:title||'Something went wrong',text:text||'',confirmButtonText:'OK',confirmButtonColor:'#d33'}),
    warning:(title,text)=>fire({icon:'warning',title:title||'Warning',text:text||'',confirmButtonText:'OK'}),
    info:(title,text)=>fire({icon:'info',title:title||'Information',text:text||'',confirmButtonText:'OK',confirmButtonColor:'#3085d6'}),
    confirm:(options={})=>fire({icon:'warning',title:options.title||'Please confirm',text:options.text||'',showCancelButton:true,confirmButtonText:options.confirmText||'Confirm',cancelButtonText:options.cancelText||'Cancel',confirmButtonColor:options.danger===false?'#3085d6':'#d33',allowOutsideClick:false,allowEscapeKey:true}).then(result=>result.isConfirmed),
    logout:(url)=>global.AppAlert.confirm({title:'Log out?',text:'Are you sure you want to log out of your account?',confirmText:'Logout'}).then(ok=>{if(ok)global.location.href=url;return ok;}),
    result:(result,successTitle)=>result&&result.success?global.AppAlert.success(successTitle||'Success',result.message||''):global.AppAlert.error('Unable to complete request',result?.message||'Please try again.')
  };
  global.AppAlert.importResult = result => {
    const errors=(result?.errors||[]).map(error=>'<li><strong>Row '+escapeHtml(error.row)+'</strong>'+(error.student_id?' ('+escapeHtml(error.student_id)+')':'')+': '+escapeHtml((error.reasons||[]).join('; '))+'</li>').join('');
    return fire({icon:result?.success?'success':'error',title:result?.success?'Import Complete':'Import Failed',html:'<p><strong>'+escapeHtml(result?.inserted||0)+'</strong> added &nbsp; <strong>'+escapeHtml(result?.skipped||0)+'</strong> skipped</p>'+(errors?'<div style="max-height:240px;overflow:auto;text-align:left"><ul>'+errors+'</ul></div>':'<p>'+escapeHtml(result?.message||'')+'</p>'),confirmButtonText:'Done',confirmButtonColor:result?.success?'#16a36a':'#d33'});
  };
})(window);
