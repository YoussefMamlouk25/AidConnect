(function () {
  function bindUpdateForm() {
    var form = document.querySelector('.fragment-offer-edit form');
    if (!form || form.dataset.offerBound === '1') return;
    if (typeof loadContent !== 'function') return;
    form.dataset.offerBound = '1';

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var action = form.getAttribute('action') || window.location.href;
      var fd = new FormData(form);
      fetch(action, { method: 'POST', body: fd })
        .then(function (resp) { return resp.text(); })
        .then(function (html) {
          var container = document.getElementById('content-area');
          if (container) {
            container.innerHTML = html;
            if (typeof initOfferFragments === 'function') {
              initOfferFragments();
            }
          } else {
            document.open(); document.write(html); document.close();
          }
          loadContent('showoffer.php');
        })
        .catch(function (err) {
          console.error('AJAX submit error', err);
          alert('Erreur lors de la requête.');
        });
    });
  }

  function bindDeleteForm() {
    var form = document.querySelector('.fragment-delete-offer form');
    if (!form || form.dataset.offerBound === '1') return;
    if (typeof loadContent !== 'function') return;
    form.dataset.offerBound = '1';

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var action = form.getAttribute('action') || window.location.href;
      var fd = new FormData(form);
      fetch(action, { method: 'POST', body: fd })
        .then(function () {
          loadContent('showoffer.php');
        })
        .catch(function (err) {
          console.error('Delete AJAX error', err);
          alert('Erreur lors de la suppression.');
        });
    });
  }

  function bindDeleteEventForm() {
    var form = document.querySelector('.fragment-delete-event form');
    if (!form || form.dataset.eventBound === '1') return;
    if (typeof loadContent !== 'function') return;
    form.dataset.eventBound = '1';

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var action = form.getAttribute('action') || window.location.href;
      var fd = new FormData(form);
      fetch(action, { method: 'POST', body: fd })
        .then(function () {
          loadContent('showevent.php');
        })
        .catch(function (err) {
          console.error('Delete AJAX error', err);
          alert('Erreur lors de la suppression.');
        });
    });
  }

  window.initOfferFragments = function () {
    bindUpdateForm();
    bindDeleteForm();
    bindDeleteEventForm();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initOfferFragments);
  } else {
    window.initOfferFragments();
  }
})();
