// Update Offer View JavaScript

// Smooth scroll for better UX
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// Add animation on scroll
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, observerOptions);

document.querySelectorAll('.facebook-post, .offer-header-card').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
  observer.observe(el);
});

// Local Storage Functions for Likes and Counts
function getLikedReplies() {
  const liked = localStorage.getItem('likedReplies');
  return liked ? JSON.parse(liked) : [];
}

function saveLikedReplies(likedArray) {
  localStorage.setItem('likedReplies', JSON.stringify(likedArray));
}

function getReplyLikeCounts() {
  const counts = localStorage.getItem('replyLikeCounts');
  return counts ? JSON.parse(counts) : {};
}

function saveReplyLikeCounts(countsObject) {
  localStorage.setItem('replyLikeCounts', JSON.stringify(countsObject));
}

function getReplyLikeCount(id) {
  const counts = getReplyLikeCounts();
  return counts[id] || 0;
}

function updateReplyLikeCount(id, increment) {
  const counts = getReplyLikeCounts();
  if (!counts[id]) {
    counts[id] = 0;
  }
  if (increment) {
    counts[id]++;
  } else {
    counts[id] = Math.max(0, counts[id] - 1);
  }
  saveReplyLikeCounts(counts);
  return counts[id];
}

function isReplyLiked(id) {
  const liked = getLikedReplies();
  return liked.includes(id);
}

function toggleReplyLike(id) {
  const liked = getLikedReplies();
  const index = liked.indexOf(id);
  if (index > -1) {
    liked.splice(index, 1); // Unlike
    updateReplyLikeCount(id, false);
    saveLikedReplies(liked);
    return false;
  } else {
    liked.push(id); // Like
    updateReplyLikeCount(id, true);
    saveLikedReplies(liked);
    return true;
  }
}

function likeReply(id) {
  const btn = event.target.closest('button');
  const icon = btn.querySelector('i');
  const countSpan = btn.querySelector('.like-count');
  const isLiked = toggleReplyLike(id);
  
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
    icon.style.color = '#6c757d';
    btn.style.color = '#6c757d';
  }
  
  // Update count display
  if (countSpan) {
    const newCount = getReplyLikeCount(id);
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
  const liked = getLikedReplies();
  const counts = getReplyLikeCounts();
  
  // Restore liked state
  liked.forEach(id => {
    const btn = document.querySelector(`.like-btn[data-reply-id="${id}"]`);
    if (btn) {
      const icon = btn.querySelector('i');
      icon.classList.remove('ri-heart-line');
      icon.classList.add('ri-heart-fill');
      icon.style.color = '#e74c3c';
      btn.style.color = '#e74c3c';
    }
  });
  
  // Restore and display counts
  document.querySelectorAll('.like-count[data-reply-id]').forEach(countSpan => {
    const id = parseInt(countSpan.getAttribute('data-reply-id'));
    const count = counts[id] || 0;
    countSpan.textContent = count;
  });
}

// Share reply functionality
function shareReply(id) {
  const urlParams = new URLSearchParams(window.location.search);
  const offerId = urlParams.get('id_offer') || '';
  const url = window.location.href.split('?')[0] + (offerId ? '?id_offer=' + offerId : '') + '#reply-' + id;
  if (navigator.share) {
    navigator.share({
      title: 'Check out this reply!',
      text: 'View this reply on AidConnect',
      url: url
    }).catch(err => console.log('Error sharing', err));
  } else {
    // Fallback: copy URL to clipboard
    copyToClipboard(url);
    alert('Reply link copied to clipboard!');
  }
}

// Copy reply text functionality
function copyReplyText(id) {
  const replyCard = event.target.closest('.facebook-post');
  const replyText = replyCard.querySelector('.reply-text p');
  if (replyText) {
    const text = replyText.textContent || replyText.innerText;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text).then(() => {
        showToast('Reply text copied to clipboard!');
      });
    } else {
      // Fallback
      const textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.position = 'fixed';
      textArea.style.opacity = '0';
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);
      showToast('Reply text copied to clipboard!');
    }
  }
}

// Copy to clipboard helper
function copyToClipboard(text) {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(text).then(() => {
      showToast('Copied to clipboard!');
    });
  } else {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.opacity = '0';
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    showToast('Copied to clipboard!');
  }
}

// Toast notification
function showToast(message) {
  const toast = document.createElement('div');
  toast.style.cssText = 'position: fixed; bottom: 20px; right: 20px; background: #28a745; color: white; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 9999; animation: slideIn 0.3s ease;';
  toast.innerHTML = '<i class="ri-checkbox-circle-line me-2"></i>' + message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  loadSavedLikes();
});

