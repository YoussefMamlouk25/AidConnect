// Show Post JavaScript - Share and Print functionality

// Copy to clipboard function
function copyToClipboard(text) {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(text).then(function() {
      showToast('Lien copié dans le presse-papiers !');
    }, function(err) {
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.position = 'fixed';
      textArea.style.opacity = '0';
      document.body.appendChild(textArea);
      textArea.select();
      try {
        document.execCommand('copy');
        showToast('Lien copié dans le presse-papiers !');
      } catch (err) {
        showToast('Erreur lors de la copie');
      }
      document.body.removeChild(textArea);
    });
  } else {
    // Fallback for older browsers
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.opacity = '0';
    document.body.appendChild(textArea);
    textArea.select();
    try {
      document.execCommand('copy');
      showToast('Lien copié dans le presse-papiers !');
    } catch (err) {
      showToast('Erreur lors de la copie');
    }
    document.body.removeChild(textArea);
  }
}

// Share post functionality
function sharePost(id) {
  const url = window.location.href.split('?')[0] + '?id_post=' + id;
  if (navigator.share) {
    navigator.share({
      title: 'Découvrez ce post sur AidConnect !',
      text: 'Consultez ce post sur AidConnect',
      url: url
    }).catch(err => console.log('Erreur lors du partage', err));
  } else {
    // Fallback: copy URL to clipboard
    copyToClipboard(url);
  }
}

// Print post functionality
function printPost(postId) {
  // Find the post card element
  const postCard = document.querySelector(`[data-post-id="${postId}"]`);
  if (!postCard) {
    // If not found, print the entire page
    window.print();
    return;
  }
  
  // Create a new window for printing
  const printWindow = window.open('', '_blank');
  const printContent = postCard.cloneNode(true);
  
  // Remove action buttons from print view
  const actionButtons = printContent.querySelectorAll('.post-actions');
  actionButtons.forEach(btn => {
    btn.remove();
  });
  
  // Get post title for print title
  const postTitle = printContent.querySelector('h5') ? printContent.querySelector('h5').textContent : 'Post';
  
  // Create print HTML
  printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>${postTitle} - AidConnect</title>
      <meta charset="UTF-8">
      <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
        body {
          font-family: Arial, sans-serif;
          padding: 20px;
          max-width: 800px;
          margin: 0 auto;
          color: #1c1e21;
        }
        .card {
          border: 1px solid #ddd;
          border-radius: 8px;
          padding: 20px;
          margin-bottom: 20px;
          background: white;
        }
        .card-body {
          padding: 0;
        }
        img {
          max-width: 100%;
          height: auto;
          border-radius: 8px;
        }
        .bg-light {
          background-color: #f8f9fa;
          padding: 15px;
          border-radius: 5px;
          margin: 15px 0;
        }
        h5 {
          font-size: 18px;
          margin-bottom: 10px;
          color: #1c1e21;
        }
        h6 {
          font-size: 16px;
          margin-bottom: 5px;
        }
        p {
          margin-bottom: 10px;
          line-height: 1.6;
        }
        .fb-media-grid {
          display: grid;
          gap: 8px;
          margin-bottom: 15px;
        }
        .fb-media-grid.count-1 {
          grid-template-columns: 1fr;
        }
        .fb-media-grid.count-2 {
          grid-template-columns: 1fr 1fr;
        }
        .fb-media-grid.count-3 {
          grid-template-columns: 1fr 1fr;
        }
        .fb-media-grid.count-4 {
          grid-template-columns: 1fr 1fr;
        }
        @media print {
          body {
            padding: 10px;
          }
          .card {
            border: none;
            box-shadow: none;
            page-break-inside: avoid;
          }
          @page {
            margin: 1cm;
          }
        }
      </style>
    </head>
    <body>
      <h1 style="text-align: center; margin-bottom: 20px; color: #0f5fcc;">AidConnect</h1>
      ${printContent.innerHTML}
      <div style="text-align: center; margin-top: 20px; color: #65676b; font-size: 12px;">
        <p>Imprimé depuis AidConnect - ${new Date().toLocaleDateString('fr-FR')}</p>
      </div>
    </body>
    </html>
  `);
  
  printWindow.document.close();
  printWindow.focus();
  
  // Wait for images to load, then print
  setTimeout(() => {
    printWindow.print();
    // Close window after printing (user can cancel)
    setTimeout(() => {
      printWindow.close();
    }, 1000);
  }, 500);
}

// Toast notification
function showToast(message) {
  const toast = document.createElement('div');
  toast.style.cssText = `
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #333;
    color: white;
    padding: 12px 24px;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    z-index: 10000;
    font-size: 14px;
  `;
  toast.textContent = message;
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s';
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 300);
  }, 3000);
}

// Like post functionality with database
function likePost(postId) {
  console.log('=== likePost called with postId:', postId, '===');
  
  if (!postId || postId <= 0) {
    console.error('Invalid postId:', postId);
    showToast('ID post invalide');
    return false;
  }
  
  const btn = document.querySelector(`.like-post-btn[data-post-id="${postId}"]`);
  if (!btn) {
    console.error('Button not found for postId:', postId);
    const allButtons = document.querySelectorAll('.like-post-btn');
    console.log('Available buttons:', allButtons.length);
    showToast('Bouton non trouvé');
    return false;
  }
  
  console.log('Button found:', btn);
  
  const icon = btn.querySelector('i');
  const countSpan = btn.querySelector('.like-count');
  
  if (!icon || !countSpan) {
    console.error('Icon or countSpan not found');
    return false;
  }
  
  // Disable button during request
  btn.disabled = true;
  btn.style.pointerEvents = 'none';
  btn.style.opacity = '0.6';
  
  // Make AJAX request - use relative path
  const apiUrl = 'api/post_like.php';
  
  console.log('Fetching from:', apiUrl, 'Current path:', window.location.pathname);
  
  fetch(apiUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      action: 'toggle',
      id_post: postId
    })
  })
  .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      console.log('Response data:', data);
      btn.disabled = false;
      btn.style.pointerEvents = 'auto';
      
      if (data.success) {
      const isLiked = data.isLiked;
      const count = data.count;
      
      // Update visual state
      if (isLiked) {
        icon.classList.remove('ri-heart-line');
        icon.classList.add('ri-heart-fill');
        icon.style.color = '#e74c3c';
        btn.style.color = '#e74c3c';
        btn.classList.add('liked');
        // Add visual feedback
        btn.style.transform = 'scale(1.1)';
        setTimeout(() => {
          btn.style.transform = 'scale(1)';
        }, 200);
      } else {
        icon.classList.remove('ri-heart-fill');
        icon.classList.add('ri-heart-line');
        icon.style.color = '#6c757d';
        btn.style.color = '#6c757d';
        btn.classList.remove('liked');
      }
      
      // Update count display
      if (countSpan) {
        countSpan.textContent = count;
        // Add animation
        countSpan.style.transform = 'scale(1.2)';
        setTimeout(() => {
          countSpan.style.transform = 'scale(1)';
        }, 200);
      }
    } else {
      if (data.error === 'Utilisateur non connecté') {
        showToast('Veuillez vous connecter pour aimer un post');
        // Redirect to login
        setTimeout(() => {
          window.location.href = 'login.php';
        }, 1500);
      } else {
        showToast(data.error || 'Erreur lors du like');
      }
    }
    })
    .catch(error => {
      btn.disabled = false;
      btn.style.pointerEvents = 'auto';
      console.error('Erreur:', error);
      showToast('Erreur de connexion: ' + error.message);
    });
  
  return false; // Prevent default behavior
}

// Setup like buttons event listeners
function setupLikeButtons() {
  console.log('Setting up like buttons...');
  const likeButtons = document.querySelectorAll('.like-post-btn[data-post-id]');
  console.log('Found', likeButtons.length, 'like buttons');
  
  likeButtons.forEach(btn => {
    const postId = btn.getAttribute('data-post-id');
    
    // Remove any existing listeners by cloning
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
    
    // Add click event listener
    newBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Like button clicked for post:', postId);
      if (typeof likePost === 'function') {
        likePost(parseInt(postId));
      } else {
        console.error('likePost function not defined!');
        alert('Erreur: fonction likePost non trouvée. Vérifiez que showpost.js est chargé.');
      }
      return false;
    });
  });
}

// Load likes on page load (only if user is logged in)
document.addEventListener('DOMContentLoaded', function() {
  // Setup event listeners first
  setupLikeButtons();
  
  // Load initial like states for all posts (only if like buttons exist = user is logged in)
  const likeButtons = document.querySelectorAll('.like-post-btn[data-post-id]');
  if (likeButtons.length === 0) return; // User not logged in
  
  likeButtons.forEach(btn => {
    const postId = btn.getAttribute('data-post-id');
    
    let apiUrl = 'api/post_like.php';
    if (!window.location.pathname.includes('/View/frontoffice/')) {
      apiUrl = 'View/frontoffice/api/post_like.php';
    }
    
    fetch(apiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: 'get',
        id_post: postId
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const icon = btn.querySelector('i');
        const countSpan = btn.querySelector('.like-count');
        
        if (data.isLiked) {
          icon.classList.remove('ri-heart-line');
          icon.classList.add('ri-heart-fill');
          icon.style.color = '#e74c3c';
          btn.style.color = '#e74c3c';
          btn.classList.add('liked');
        }
        
        if (countSpan) {
          countSpan.textContent = data.count;
        }
      }
    })
    .catch(error => {
      console.error('Erreur chargement likes:', error);
    });
  });
});

// Comments functionality
function showComments(postId) {
  console.log('Showing comments for post:', postId);
  const commentsSection = document.getElementById(`comments-${postId}`);
  const commentsList = document.getElementById(`comments-list-${postId}`);
  
  if (!commentsSection || !commentsList) {
    console.error('Comments section not found for post:', postId);
    return;
  }
  
  // Toggle visibility
  if (commentsSection.style.display === 'none') {
    commentsSection.style.display = 'block';
    loadComments(postId);
  } else {
    commentsSection.style.display = 'none';
  }
}

function loadComments(postId) {
  const commentsList = document.getElementById(`comments-list-${postId}`);
  if (!commentsList) return;
  
  commentsList.innerHTML = '<div class="text-center py-3"><i class="ri-loader-4-line spin"></i> Chargement...</div>';
  
  fetch('api/post_comment.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      action: 'get',
      id_post: postId
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      displayComments(postId, data.comments);
      updateCommentCount(postId, data.count);
    } else {
      commentsList.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des commentaires</div>';
    }
  })
  .catch(error => {
    console.error('Erreur:', error);
    commentsList.innerHTML = '<div class="alert alert-danger">Erreur de connexion</div>';
  });
}

function displayComments(postId, comments) {
  const commentsList = document.getElementById(`comments-list-${postId}`);
  if (!commentsList) return;
  
  if (comments.length === 0) {
    commentsList.innerHTML = '<div class="text-muted text-center py-3">Aucun commentaire pour le moment. Soyez le premier à commenter !</div>';
    return;
  }
  
  let html = '';
  comments.forEach(comment => {
    const date = new Date(comment.date_comment);
    const dateStr = date.toLocaleDateString('fr-FR', { 
      day: '2-digit', 
      month: '2-digit', 
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
    
    html += `
      <div class="comment-item mb-3 p-2 bg-light rounded">
        <div class="d-flex align-items-start">
          <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px; font-weight: 600;">
            ${(comment.name || 'Utilisateur').charAt(0).toUpperCase()}
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <strong class="comment-author">${escapeHtml(comment.name || 'Utilisateur supprimé')}</strong>
                <small class="text-muted ms-2">${dateStr}</small>
              </div>
            </div>
            <p class="mb-0 mt-1 comment-text">${escapeHtml(comment.comment_text)}</p>
          </div>
        </div>
      </div>
    `;
  });
  
  commentsList.innerHTML = html;
}

function addComment(postId) {
  const input = document.getElementById(`comment-input-${postId}`);
  if (!input) return;
  
  const commentText = input.value.trim();
  if (!commentText) {
    showToast('Le commentaire ne peut pas être vide');
    return;
  }
  
  // Disable input during request
  input.disabled = true;
  
  fetch('api/post_comment.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      action: 'add',
      id_post: postId,
      comment_text: commentText
    })
  })
  .then(response => response.json())
  .then(data => {
    input.disabled = false;
    
    if (data.success) {
      input.value = '';
      loadComments(postId); // Reload comments
      showToast('Commentaire ajouté avec succès');
    } else {
      showToast(data.error || 'Erreur lors de l\'ajout du commentaire');
    }
  })
  .catch(error => {
    input.disabled = false;
    console.error('Erreur:', error);
    showToast('Erreur de connexion');
  });
}

function updateCommentCount(postId, count) {
  const countSpan = document.querySelector(`.comment-count[data-post-id="${postId}"]`);
  if (countSpan) {
    countSpan.textContent = count;
  }
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Add spin animation for loading
if (!document.getElementById('comment-spin-style')) {
  const style = document.createElement('style');
  style.id = 'comment-spin-style';
  style.textContent = `
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .spin {
      animation: spin 1s linear infinite;
      display: inline-block;
    }
  `;
  document.head.appendChild(style);
}

// Search functionality for posts
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('postSearchInput');
  const clearBtn = document.getElementById('clearSearchBtn');
  
  if (!searchInput) return;
  
  // Search on input
  searchInput.addEventListener('input', function() {
    filterPosts(this.value.trim());
    // Show/hide clear button
    if (this.value.trim().length > 0) {
      clearBtn.style.display = 'block';
    } else {
      clearBtn.style.display = 'none';
    }
  });
  
  // Search on Enter key
  searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      filterPosts(this.value.trim());
    }
  });
  
  // Clear search on Escape key
  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      clearSearch();
    }
  });
});

function filterPosts(searchTerm) {
  const posts = document.querySelectorAll('.post-item');
  const searchLower = searchTerm.toLowerCase();
  let visibleCount = 0;
  
  posts.forEach(post => {
    const title = post.getAttribute('data-post-title') || '';
    
    if (searchTerm === '' || title.includes(searchLower)) {
      post.style.display = 'block';
      visibleCount++;
    } else {
      post.style.display = 'none';
    }
  });
  
  // Show message if no results
  let noResultsMsg = document.getElementById('no-search-results');
  if (searchTerm !== '' && visibleCount === 0) {
    if (!noResultsMsg) {
      noResultsMsg = document.createElement('div');
      noResultsMsg.id = 'no-search-results';
      noResultsMsg.className = 'alert alert-info text-center mt-3';
      const searchCard = document.querySelector('.card .card-body');
      if (searchCard && searchCard.parentElement) {
        searchCard.parentElement.insertAdjacentElement('afterend', noResultsMsg);
      }
    }
    noResultsMsg.innerHTML = `
      <i class="ri-search-line me-2"></i>
      Aucun post trouvé pour "<strong>${escapeHtml(searchTerm)}</strong>"
    `;
    noResultsMsg.style.display = 'block';
  } else {
    if (noResultsMsg) {
      noResultsMsg.style.display = 'none';
    }
  }
}

function clearSearch() {
  const searchInput = document.getElementById('postSearchInput');
  const clearBtn = document.getElementById('clearSearchBtn');
  
  if (searchInput) {
    searchInput.value = '';
    filterPosts('');
    clearBtn.style.display = 'none';
    searchInput.focus();
  }
}

