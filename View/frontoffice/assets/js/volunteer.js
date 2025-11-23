// Delete Form Confirmation Handler
document.addEventListener('DOMContentLoaded', function() {
  const deleteForm = document.getElementById('deleteForm');
  if (deleteForm) {
    deleteForm.addEventListener('submit', function(e) {
      if (!confirm('Are you absolutely sure you want to delete this volunteer? This action cannot be undone!')) {
        e.preventDefault();
        return false;
      }
      const btn = document.getElementById('deleteBtn');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
      }
    });
  }
});

