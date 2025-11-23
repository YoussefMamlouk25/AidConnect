
(function () {
  function bindUpdateReplyForm() {
    var form = document.querySelector('.fragment-updateoffer-edit form');
    if (!form || form.dataset.updateOfferBound === '1') return;
    if (typeof loadContent !== 'function') return;
    form.dataset.updateOfferBound = '1';

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var fd = new FormData(form);
      fetch(form.action, { method: 'POST', body: fd })
        .then(function (resp) { return resp.text(); })
        .then(function (html) {
          var container = document.getElementById('content-area');
          if (container) {
            container.innerHTML = html;
            if (typeof initUpdateOfferFragments === 'function') {
              initUpdateOfferFragments();
            }
          }
          loadContent('showupdateoffer.php?embed=1');
        })
        .catch(function (err) {
          console.error('Erreur mise à jour updateOffer', err);
          alert('Erreur lors de la mise à jour.');
        });
    });
  }

  function bindDeleteReplyForm() {
    var form = document.querySelector('.fragment-updateoffer-delete form');
    if (!form || form.dataset.updateOfferBound === '1') return;
    if (typeof loadContent !== 'function') return;
    form.dataset.updateOfferBound = '1';

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var fd = new FormData(form);
      fetch(form.action, { method: 'POST', body: fd })
        .then(function (resp) { return resp.text(); })
        .then(function (html) {
          var container = document.getElementById('content-area');
          if (container) {
            container.innerHTML = html;
            if (typeof initUpdateOfferFragments === 'function') {
              initUpdateOfferFragments();
            }
          }
          loadContent('showupdateoffer.php?embed=1');
        })
        .catch(function (err) {
          console.error('Erreur suppression updateOffer', err);
          alert('Erreur lors de la suppression.');
        });
    });
  }

  window.initUpdateOfferFragments = function () {
    bindUpdateReplyForm();
    bindDeleteReplyForm();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initUpdateOfferFragments);
  } else {
    window.initUpdateOfferFragments();
  }
})();

