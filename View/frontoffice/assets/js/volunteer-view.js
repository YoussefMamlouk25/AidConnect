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

// Local Storage Functions for Likes and Counts
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  loadSavedLikes();
});

