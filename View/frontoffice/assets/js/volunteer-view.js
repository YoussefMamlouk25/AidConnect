// Volunteer View JavaScript

// Copy to clipboard function
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(function() {
    alert('Copied to clipboard: ' + text);
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
      alert('Copied to clipboard: ' + text);
    } catch (err) {
      alert('Failed to copy');
    }
    document.body.removeChild(textArea);
  });
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

// Like offer functionality with database
function likeOffer(offerId) {
  console.log('=== likeOffer called with offerId:', offerId, '===');
  
  if (!offerId || offerId <= 0) {
    console.error('Invalid offerId:', offerId);
    showToast('ID offre invalide');
    return false;
  }
  
  const btn = document.querySelector(`.like-offer-btn[data-offer-id="${offerId}"]`);
  if (!btn) {
    console.error('Button not found for offerId:', offerId);
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
  
  // Make AJAX request
  const apiUrl = '../../Model/offer_like.php';
  
  console.log('Fetching from:', apiUrl);
  
  fetch(apiUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      action: 'toggle',
      id_offer: offerId
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
      btn.style.opacity = '1';
      
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
        showToast('Veuillez vous connecter pour aimer une offre');
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
      btn.style.opacity = '1';
      console.error('Erreur:', error);
      showToast('Erreur de connexion: ' + error.message);
    });
  
  return false; // Prevent default behavior
}

// Setup like buttons event listeners
function setupLikeButtons() {
  console.log('Setting up like offer buttons...');
  const likeButtons = document.querySelectorAll('.like-offer-btn[data-offer-id]');
  console.log('Found', likeButtons.length, 'like buttons');
  
  likeButtons.forEach(btn => {
    const offerId = btn.getAttribute('data-offer-id');
    
    // Remove any existing listeners by cloning
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
    
    // Add click event listener
    newBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Like button clicked for offer:', offerId);
      if (typeof likeOffer === 'function') {
        likeOffer(parseInt(offerId));
      } else {
        console.error('likeOffer function not defined!');
        alert('Erreur: fonction likeOffer non trouvée. Vérifiez que volunteer-view.js est chargé.');
      }
      return false;
    });
  });
}

// Load likes on page load
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM loaded, initializing offer likes...');
  
  // Setup event listeners first
  setupLikeButtons();
  
  // Wait a bit to ensure buttons are set up
  setTimeout(() => {
    // Load initial like states for all offers (only if like buttons exist = user is logged in)
    const likeButtons = document.querySelectorAll('.like-offer-btn[data-offer-id]');
    console.log('Found', likeButtons.length, 'like buttons to load state for');
    
    if (likeButtons.length === 0) {
      console.log('No like buttons found - user not logged in');
      return; // User not logged in
    }
    
    likeButtons.forEach(btn => {
      const offerId = btn.getAttribute('data-offer-id');
      
    const apiUrl = '../../Model/offer_like.php';
    
    fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          action: 'get',
          id_offer: offerId
        })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
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
  }, 100);
});

// Local Storage Functions for Likes and Counts (kept for backward compatibility)
function getLikedOffers() {
  const liked = localStorage.getItem('likedOffers');
  return liked ? JSON.parse(liked) : [];
}

function saveLikedOffers(likedArray) {
  localStorage.setItem('likedOffers', JSON.stringify(likedArray));
}

function getOfferLikeCounts() {
  const counts = localStorage.getItem('offerLikeCounts');
  return counts ? JSON.parse(counts) : {};
}

function saveOfferLikeCounts(countsObject) {
  localStorage.setItem('offerLikeCounts', JSON.stringify(countsObject));
}

function getOfferLikeCount(id) {
  const counts = getOfferLikeCounts();
  return counts[id] || 0;
}

function updateOfferLikeCount(id, increment) {
  const counts = getOfferLikeCounts();
  if (!counts[id]) {
    counts[id] = 0;
  }
  if (increment) {
    counts[id]++;
  } else {
    counts[id] = Math.max(0, counts[id] - 1);
  }
  saveOfferLikeCounts(counts);
  return counts[id];
}

function isOfferLiked(id) {
  const liked = getLikedOffers();
  return liked.includes(id);
}

function toggleOfferLike(id) {
  const liked = getLikedOffers();
  const index = liked.indexOf(id);
  if (index > -1) {
    liked.splice(index, 1); // Unlike
    updateOfferLikeCount(id, false);
    saveLikedOffers(liked);
    return false;
  } else {
    liked.push(id); // Like
    updateOfferLikeCount(id, true);
    saveLikedOffers(liked);
    return true;
  }
}

function likePost(id) {
  const btn = event.target.closest('button');
  const icon = btn.querySelector('i');
  const countSpan = btn.querySelector('.like-count');
  const isLiked = toggleOfferLike(id);
  
  // Update visual state
  if (isLiked) {
    icon.classList.remove('ri-heart-line');
    icon.classList.add('ri-heart-fill');
    icon.style.color = '#e74c3c';
    btn.style.color = '#e74c3c';
    // Add visual feedback
    btn.style.transform = 'scale(1.1)';
    setTimeout(() => {
      btn.style.transform = 'scale(1)';
    }, 200);
  } else {
    icon.classList.remove('ri-heart-fill');
    icon.classList.add('ri-heart-line');
    icon.style.color = '';
    btn.style.color = '';
  }
  
  // Update count display
  if (countSpan) {
    const newCount = getOfferLikeCount(id);
    countSpan.textContent = newCount;
    // Add animation
    countSpan.style.transform = 'scale(1.2)';
    setTimeout(() => {
      countSpan.style.transform = 'scale(1)';
    }, 200);
  }
}

// Load saved likes and counts on page load
function loadSavedLikes() {
  const liked = getLikedOffers();
  const counts = getOfferLikeCounts();
  
  // Restore liked state
  liked.forEach(id => {
    const btn = document.querySelector(`.like-post-btn[data-offer-id="${id}"]`);
    if (btn) {
      const icon = btn.querySelector('i');
      icon.classList.remove('ri-heart-line');
      icon.classList.add('ri-heart-fill');
      icon.style.color = '#e74c3c';
      btn.style.color = '#e74c3c';
    }
  });
  
  // Restore and display counts
  document.querySelectorAll('.like-count[data-offer-id]').forEach(countSpan => {
    const id = parseInt(countSpan.getAttribute('data-offer-id'));
    const count = counts[id] || 0;
    countSpan.textContent = count;
  });
}

// Share post functionality
function sharePost(id) {
  const url = window.location.href.split('?')[0] + '?id_offer=' + id;
  if (navigator.share) {
    navigator.share({
      title: 'Check out this volunteer offer!',
      text: 'View this offer on AidConnect',
      url: url
    }).catch(err => console.log('Error sharing', err));
  } else {
    // Fallback: copy URL to clipboard
    copyToClipboard(url);
  }
}

// Note: L'ancien code avec localStorage a été remplacé par le système de base de données
// Les fonctions loadSavedLikes() et likePost() sont conservées pour compatibilité mais ne sont plus utilisées

