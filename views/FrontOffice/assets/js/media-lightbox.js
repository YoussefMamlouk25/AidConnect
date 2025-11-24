(function(){
  function delegateImageClicks(e){
    var img = e.target.closest('.fb-media-grid img, .fb-media-grid video');
    if(!img) return;
    e.preventDefault();
    var grid = img.closest('.fb-media-grid');
    var items = Array.prototype.slice.call(grid.querySelectorAll('img,video'));
    var index = items.indexOf(img);
    openModal(items.map(function(it){return {type: it.tagName.toLowerCase(), src: it.src};}), index);
  }

  function openModal(items, startIndex){
    var modal = document.getElementById('image-modal');
    if(!modal) return;
    var wrap = modal.querySelector('.media-wrap');
    var closeBtn = modal.querySelector('.close-btn');
    var prevBtn = modal.querySelector('.nav.prev');
    var nextBtn = modal.querySelector('.nav.next');
    var current = startIndex || 0;
    function render(){
      wrap.innerHTML = '';
      var it = items[current];
      if(!it) return;
      if(it.type === 'video'){
        var v = document.createElement('video'); v.src = it.src; v.controls = true; v.autoplay = true; v.preload = 'none'; v.style.maxWidth='100%'; v.style.maxHeight='100%'; wrap.appendChild(v);
      } else {
        var im = document.createElement('img'); im.src = it.src; im.alt = ''; wrap.appendChild(im);
      }
    }
    function keyHandler(ev){ if(ev.key === 'Escape') close(); if(ev.key === 'ArrowLeft') prev(); if(ev.key === 'ArrowRight') next(); }
    function prev(){ current = (current - 1 + items.length) % items.length; render(); }
    function next(){ current = (current + 1) % items.length; render(); }
    function close(){ modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); wrap.innerHTML=''; document.removeEventListener('keydown', keyHandler); }

    modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); render();
    closeBtn.onclick = close; prevBtn.onclick = prev; nextBtn.onclick = next;
    modal.onclick = function(ev){ if(ev.target === modal) close(); };
    document.addEventListener('keydown', keyHandler);
  }

  document.addEventListener('click', delegateImageClicks, false);
})();
