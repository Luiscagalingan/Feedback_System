(function () {
  function escapeHtml(value) {
    var node = document.createElement('span'); node.textContent = value == null ? '' : String(value); return node.innerHTML;
  }
  function render(data) {
    var numbers = document.querySelectorAll('#dashboardPage .stat-number');
    if (numbers.length >= 3) { numbers[0].textContent = data.submitted; numbers[1].textContent = data.pending; numbers[2].textContent = data.this_month; }
    var recentPanel = document.querySelector('#dashboardPage .dashboard-grid .panel:first-child');
    if (!recentPanel) return;
    var old = recentPanel.querySelector('.empty, .recent-feedback-list'); if (old) old.remove();
    if (!data.recent.length) {
      recentPanel.insertAdjacentHTML('beforeend', '<div class="empty"><div><div>You have not submitted any feedback yet.</div></div></div>'); return;
    }
    var rows = data.recent.map(function (item) {
      var category = item.category === 'academic' ? 'Academic' : 'Non-Academic';
      var date = new Date(String(item.created_at).replace(' ', 'T'));
      var displayDate = isNaN(date.getTime()) ? item.created_at : date.toLocaleDateString(undefined, {month:'short', day:'numeric', year:'numeric'});
      return '<div style="display:flex;justify-content:space-between;gap:16px;padding:14px 0;border-bottom:1px solid #e5e7eb"><div><strong>' + escapeHtml(item.office_name) + '</strong><div style="color:#7b8497;font-size:13px;margin-top:4px">' + category + ' &middot; ' + escapeHtml(displayDate) + '</div></div><span class="tag" style="height:max-content">' + Number(item.rating_average).toFixed(2) + '/5</span></div>';
    }).join('');
    recentPanel.insertAdjacentHTML('beforeend', '<div class="recent-feedback-list">' + rows + '</div>');
  }
  fetch('../student_feedback_summary.php', {credentials:'same-origin'}).then(function (response) { return response.json(); }).then(function (result) { if (result.success) render(result.data); }).catch(function () {});
})();
