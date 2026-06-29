/* ════════════════════════════════════════════
   US Water Walls — shared site scripts
   Every block is guarded so it can run on any page.
   ════════════════════════════════════════════ */

/* ── rising water drop spawner (hero) ── */
(function(){
  const layer=document.getElementById('heroDrops');
  if(!layer) return;
  const dropSVG=(size,color,opacity)=>
    `<svg width="${size}" height="${Math.round(size*1.32)}" viewBox="0 0 60 80"><path d="M30 0 C30 0 4 36 4 53 A26 26 0 0 0 56 53 C56 36 30 0 30 0Z" fill="${color}" stroke="rgba(62,207,190,${opacity*.7})" stroke-width="1"/></svg>`;
  function spawnDrop(){
    const el=document.createElement('div');
    el.className='wdrop';
    const size=Math.random()*14+6;
    const drift=(Math.random()-.5)*80;
    const dur=Math.random()*8+6;
    const delay=Math.random()*2;
    const x=Math.random()*55;
    const isGold=Math.random()>.8;
    const color=isGold?`rgba(212,168,83,${Math.random()*.15+.08})`:`rgba(62,207,190,${Math.random()*.18+.07})`;
    const opacity=Math.random()*.4+.15;
    el.style.cssText=`left:${x}%;bottom:-5%;--drift:${drift}px;animation-duration:${dur}s;animation-delay:${delay}s`;
    el.innerHTML=dropSVG(size,color,opacity);
    layer.appendChild(el);
    setTimeout(()=>el.remove(),(dur+delay)*1000+200);
  }
  setInterval(spawnDrop,700);
  for(let i=0;i<6;i++) setTimeout(spawnDrop,i*300);
})();

/* ── cursor glow ── */
(function(){
  const cg=document.getElementById('cg');
  if(!cg) return;
  document.addEventListener('mousemove',e=>{cg.style.left=e.clientX+'px';cg.style.top=e.clientY+'px'});
})();

/* ── navbar scroll state ── */
(function(){
  const nav=document.getElementById('nav');
  if(!nav) return;
  window.addEventListener('scroll',()=>nav.classList.toggle('scrolled',scrollY>55));
})();

/* ── mobile nav ── */
function closeMob(){
  const h=document.getElementById('ham'),m=document.getElementById('mobnav');
  if(h)h.classList.remove('open'); if(m)m.classList.remove('open');
}
(function(){
  const ham=document.getElementById('ham'),mob=document.getElementById('mobnav');
  if(!ham||!mob) return;
  ham.addEventListener('click',function(){this.classList.toggle('open');mob.classList.toggle('open')});
})();

/* ── typewriter (hero) ── */
(function(){
  const twEl=document.getElementById('tw');
  if(!twEl) return;
  const words=(twEl.dataset.words||'Space,Home,Hotel,Office,Restaurant,Vision,Lobby').split(',');
  let wi=0,ci=0,del=false;
  function type(){
    const w=words[wi];
    twEl.textContent=del?w.slice(0,--ci):w.slice(0,++ci);
    if(!del&&ci===w.length){del=true;setTimeout(type,2000);return}
    if(del&&ci===0){del=false;wi=(wi+1)%words.length}
    setTimeout(type,del?55:105);
  }
  setTimeout(type,900);
})();

/* ── background particles canvas ── */
(function(){
  const cv=document.getElementById('pc');
  if(!cv) return;
  const cx=cv.getContext('2d');
  function rs(){cv.width=window.innerWidth;cv.height=window.innerHeight}
  rs();window.addEventListener('resize',rs);
  const pts=Array.from({length:90},()=>({
    x:Math.random()*cv.width,y:Math.random()*cv.height,
    r:Math.random()*1.4+.3,vx:(Math.random()-.5)*.18,vy:-(Math.random()*.3+.07),
    a:Math.random()*.32+.05,c:Math.random()>.72?'212,168,83':'62,207,190'
  }));
  function draw(){
    cx.clearRect(0,0,cv.width,cv.height);
    pts.forEach(p=>{p.x+=p.vx;p.y+=p.vy;if(p.y<-4){p.y=cv.height+4;p.x=Math.random()*cv.width}
      cx.beginPath();cx.arc(p.x,p.y,p.r,0,Math.PI*2);cx.fillStyle=`rgba(${p.c},${p.a})`;cx.fill()});
    requestAnimationFrame(draw);
  }
  draw();
})();

/* ── water wall canvas (hero visual) ── */
(function(){
  const cv=document.getElementById('wwc');
  if(!cv) return;
  const cx=cv.getContext('2d');
  function rs(){cv.width=cv.offsetWidth;cv.height=cv.offsetHeight}
  rs();window.addEventListener('resize',rs);
  const COLS=20;
  const bubbles=[];
  for(let c=0;c<COLS;c++){
    for(let i=0;i<Math.floor(Math.random()*5)+3;i++) bubbles.push({
      col:c,y:Math.random(),r:Math.random()*4+1.5,
      spd:Math.random()*.55+.2,a:Math.random()*.45+.15,
      ph:Math.random()*Math.PI*2,wb:Math.random()*.35+.08
    });
  }
  const rays=[{x:.18,w:35,a:.04},{x:.45,w:22,a:.028},{x:.72,w:40,a:.035},{x:.9,w:18,a:.022}];
  let t=0;
  function draw(){
    const W=cv.width,H=cv.height;
    cx.clearRect(0,0,W,H);
    const bg=cx.createLinearGradient(0,0,0,H);
    bg.addColorStop(0,'#05101e');bg.addColorStop(.5,'#07162a');bg.addColorStop(1,'#050d1a');
    cx.fillStyle=bg;cx.fillRect(0,0,W,H);
    rays.forEach(r=>{
      const rg=cx.createLinearGradient(0,0,0,H);
      rg.addColorStop(0,`rgba(62,207,190,0)`);
      rg.addColorStop(.45,`rgba(62,207,190,${r.a})`);
      rg.addColorStop(1,`rgba(62,207,190,0)`);
      cx.fillStyle=rg;cx.fillRect(r.x*W-r.w/2,0,r.w,H);
    });
    for(let i=0;i<3;i++){
      const y=((t*.0018+i*.33)%1)*H;
      const sg=cx.createLinearGradient(0,0,W,0);
      sg.addColorStop(0,'rgba(62,207,190,0)');sg.addColorStop(.5,`rgba(62,207,190,.038)`);sg.addColorStop(1,'rgba(62,207,190,0)');
      cx.fillStyle=sg;cx.fillRect(0,y,W,1.5);
    }
    const cw=W/COLS;
    bubbles.forEach(b=>{
      b.y-=b.spd*.003;if(b.y<-b.r/H)b.y=1+b.r/H;
      const px=(b.col+.5)*cw+Math.sin(t*.022+b.ph)*b.wb*cw*.7;
      const py=b.y*H;
      const gl=cx.createRadialGradient(px,py,0,px,py,b.r*3.2);
      gl.addColorStop(0,`rgba(62,207,190,${b.a*.45})`);gl.addColorStop(1,'rgba(62,207,190,0)');
      cx.fillStyle=gl;cx.beginPath();cx.arc(px,py,b.r*3.2,0,Math.PI*2);cx.fill();
      cx.beginPath();cx.arc(px,py,b.r,0,Math.PI*2);
      cx.strokeStyle=`rgba(62,207,190,${b.a})`;cx.lineWidth=.9;cx.stroke();
      cx.beginPath();cx.arc(px-b.r*.32,py-b.r*.32,b.r*.28,0,Math.PI*2);
      cx.fillStyle=`rgba(255,255,255,${b.a*.55})`;cx.fill();
    });
    const pg=cx.createLinearGradient(0,H*.82,0,H);
    pg.addColorStop(0,'rgba(62,207,190,0)');pg.addColorStop(1,'rgba(62,207,190,.07)');
    cx.fillStyle=pg;cx.fillRect(0,H*.82,W,H*.18);
    for(let i=0;i<3;i++){
      const wx=((t*.012+i*.33)%1)*W*1.4-W*.2;
      cx.beginPath();cx.ellipse(wx,H*.92,W*.22,3.5,0,0,Math.PI*2);
      cx.strokeStyle=`rgba(62,207,190,${.05+i*.015})`;cx.lineWidth=1;cx.stroke();
    }
    t++;requestAnimationFrame(draw);
  }
  draw();
})();

/* ── scroll reveal ── */
const rvObs=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting)e.target.classList.add('in')}),{threshold:.1});
document.querySelectorAll('.rv').forEach(el=>rvObs.observe(el));

/* ── count-up helper ── */
function countUp(el,to,dur=1700){
  let s=null;
  const step=ts=>{if(!s)s=ts;const p=Math.min((ts-s)/dur,1);const e=1-Math.pow(1-p,4);el.textContent=Math.floor(e*to);if(p<1)requestAnimationFrame(step);else el.textContent=to};
  requestAnimationFrame(step);
}
/* hero card counter */
(function(){
  const hero=document.getElementById('hero'),hc=document.getElementById('hcnt');
  if(!hero||!hc) return;
  let done=false;
  new IntersectionObserver(e=>{if(e[0].isIntersecting&&!done){done=true;countUp(hc,500,2200)}}).observe(hero);
})();
/* section counters */
document.querySelectorAll('.cnum').forEach(el=>{
  new IntersectionObserver(e=>{if(e[0].isIntersecting){countUp(el,+el.dataset.to);rvObs.unobserve(el)}},{threshold:.4}).observe(el);
});

/* ── smooth scroll for same-page anchors ── */
document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click',e=>{
    const href=a.getAttribute('href');
    if(href.length<2) return;
    const t=document.querySelector(href);
    if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth'})}
  });
});

/* ── hero parallax ── */
(function(){
  const hero=document.getElementById('hero'),grid=document.querySelector('.hero-grid-lines');
  if(!hero||!grid) return;
  hero.addEventListener('mousemove',e=>{
    const dx=(e.clientX/window.innerWidth-.5)*16;
    const dy=(e.clientY/window.innerHeight-.5)*16;
    grid.style.transform=`translate(${dx*.35}px,${dy*.35}px)`;
  });
})();

/* form submits directly to send_mail.php via HTML action attribute */

/* ── FAQ accordion ── */
document.querySelectorAll('.faq-q').forEach(q=>{
  q.addEventListener('click',()=>{
    const item=q.closest('.faq-item');
    const open=item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i=>i.classList.remove('open'));
    if(!open) item.classList.add('open');
  });
});
