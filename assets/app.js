/* ═══════════════════════════════════════════
   US Video Walls — site scripts
   ═══════════════════════════════════════════ */

/* ── NAVBAR ── */
(function () {
  const nav = document.getElementById('nav');
  const ham = document.getElementById('ham');
  const mob = document.getElementById('mobnav');
  if (!nav) return;

  const onHero = !!document.getElementById('hero');

  function setNavState() {
    const scrolled = window.scrollY > 60;
    nav.classList.toggle('scrolled', scrolled);
    nav.classList.toggle('hero-nav', onHero && !scrolled);
  }
  setNavState();
  window.addEventListener('scroll', setNavState, { passive: true });

  if (ham && mob) {
    ham.addEventListener('click', function () {
      this.classList.toggle('open');
      mob.classList.toggle('open');
      document.body.style.overflow = mob.classList.contains('open') ? 'hidden' : '';
    });
  }
})();

function closeMob() {
  const h = document.getElementById('ham');
  const m = document.getElementById('mobnav');
  if (h) h.classList.remove('open');
  if (m) m.classList.remove('open');
  document.body.style.overflow = '';
}

/* ── SCROLL REVEAL ── */
(function () {
  const obs = new IntersectionObserver(
    (entries) => entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); obs.unobserve(e.target); } }),
    { threshold: 0.1 }
  );
  document.querySelectorAll('.rv').forEach(el => obs.observe(el));
})();

/* ── SMOOTH SAME-PAGE ANCHORS ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href');
    if (id.length < 2) return;
    const target = document.querySelector(id);
    if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
  });
});

/* ── GALLERY FILTER PILLS ── */
(function () {
  const pills = document.querySelectorAll('.gf-pill');
  const items = document.querySelectorAll('.gf-item[data-cat]');
  if (!pills.length) return;

  pills.forEach(pill => {
    pill.addEventListener('click', () => {
      pills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      const cat = pill.dataset.filter;
      items.forEach(item => {
        const show = cat === 'all' || item.dataset.cat === cat;
        item.style.opacity = show ? '1' : '0.25';
        item.style.pointerEvents = show ? '' : 'none';
      });
    });
  });
})();

/* ── FAQ ACCORDION ── */
document.querySelectorAll('.faq-q').forEach(q => {
  q.addEventListener('click', () => {
    const item = q.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  });
});

/* ── CONTACT FORM ── */
document.querySelectorAll('form[data-contact]').forEach(f => {
  f.addEventListener('submit', e => {
    e.preventDefault();
    const note = f.querySelector('.form-note');
    if (note) { note.textContent = 'Thank you! We\'ll be in touch within 24 hours.'; note.classList.add('ok'); }
    f.reset();
  });
});

/* ── SCREEN WALL HOVER PARALLAX (hero) ── */
(function () {
  const wall = document.querySelector('.screen-wall');
  const hero = document.getElementById('hero');
  if (!wall || !hero) return;
  hero.addEventListener('mousemove', e => {
    const rx = (e.clientX / window.innerWidth - 0.5) * 10;
    const ry = (e.clientY / window.innerHeight - 0.5) * 8;
    wall.style.transform = `perspective(800px) rotateY(${rx * 0.4}deg) rotateX(${-ry * 0.3}deg)`;
  });
  hero.addEventListener('mouseleave', () => {
    wall.style.transform = '';
  });
})();
