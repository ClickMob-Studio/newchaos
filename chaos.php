<?php

include 'chaos_header.php';
?>

<style>
  :root{
    --bp-bg: #0f1115;
    --bp-surface: #141823;
    --bp-surface-2: #1a2030;
    --bp-text: #e7ebf3;
    --bp-muted: #9aa3b2;
    --bp-accent: #7aa2ff;
    --bp-accent-2: #3dd9b6;
    --bp-danger: #ff6b6b;
    --bp-gold: #f5c542;
    --bp-claimed: #58d68d;
    --bp-locked: #7e8796;
    --bp-shadow: 0 8px 24px rgba(0,0,0,.35);
    --bp-radius: 14px;
  }
  .bp{background:var(--bp-bg); color:var(--bp-text); padding:16px; border-radius:var(--bp-radius); box-shadow:var(--bp-shadow);}
  .bp-header{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px;}
  .bp-level{display:flex; flex-wrap:wrap; gap:10px; font-weight:600}
  .bp-level span:last-child{color:var(--bp-muted); font-weight:500}
  .bp-controls{display:flex; gap:8px}
  .bp-nav{border:0; background:var(--bp-surface); color:var(--bp-text); width:40px; height:40px; border-radius:10px; cursor:pointer}
  .bp-nav:disabled{opacity:.5; cursor:not-allowed}
  .bp-viewport{position:relative; overflow:hidden}
  .bp-track{display:flex; align-items:stretch; gap:0; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; padding-bottom:6px}
  .bp-track::-webkit-scrollbar{height:10px}
  .bp-track::-webkit-scrollbar-thumb{background:var(--bp-surface-2); border-radius:9999px}
  .bp-item{display:flex; align-items:center; gap:0; scroll-snap-align:center; padding:10px 6px}
  .bp-card{background:var(--bp-surface); border:1px solid transparent; border-radius:12px; width:160px; min-width:160px; padding:10px; display:flex; flex-direction:column; gap:8px; box-shadow:var(--bp-shadow)}
  .bp-card .bp-img{width:100%; aspect-ratio:1/1; border-radius:10px; background:var(--bp-surface-2); display:grid; place-items:center; overflow:hidden}
  .bp-card .bp-img img{width:100%; height:100%; object-fit:cover}
  .bp-card .bp-title{font-size:.95rem; font-weight:700; line-height:1.1}
  .bp-card .bp-desc{font-size:.8rem; color:var(--bp-muted)}
  .bp-meta{display:flex; align-items:center; justify-content:space-between; gap:8px}
  .bp-tag{font-size:.75rem; padding:.25rem .5rem; border-radius:999px; background:var(--bp-surface-2); color:var(--bp-muted)}
  .bp-claim{border:0; background:var(--bp-accent); color:#fff; padding:.6rem .8rem; border-radius:10px; font-weight:700; cursor:pointer}
  .bp-claim[disabled]{background:#333a; color:#999; cursor:not-allowed}
  .bp-card.locked{opacity:.75; border-color:transparent}
  .bp-card.available{border-color:var(--bp-accent-2); box-shadow:0 0 0 3px rgba(61,217,182,.25), var(--bp-shadow); animation:bpPulse 1.8s ease-in-out infinite}
  @keyframes bpPulse{0%{box-shadow:0 0 0 0 rgba(61,217,182,.28), var(--bp-shadow)}70%{box-shadow:0 0 0 10px rgba(61,217,182,0), var(--bp-shadow)}100%{box-shadow:0 0 0 0 rgba(61,217,182,0), var(--bp-shadow)}}
  .bp-card.claimed{border-color:rgba(88,214,141,.45); position:relative}
  .bp-card.claimed::after{content:"✓ Claimed"; position:absolute; top:8px; right:8px; font-size:.7rem; background:linear-gradient(90deg,var(--bp-claimed),#86e6bd); color:#042613; padding:.2rem .45rem; border-radius:999px; font-weight:800}
  /* Connector between cards */
  .bp-connector{height:8px; width:72px; min-width:72px; margin:0 6px; background:#202636; border-radius:999px; position:relative; overflow:hidden}
  .bp-connector .bp-fill{position:absolute; left:0; top:0; bottom:0; width:0%; background:linear-gradient(90deg,var(--bp-accent),var(--bp-accent-2)); border-radius:999px; transition:width .4s ease}
  .bp-connector.done{background:#1b2a22}
  .bp-connector.done .bp-fill{width:100%}
  /* Current marker */
  .bp-card.current{outline:2px solid var(--bp-gold); outline-offset:2px}
  /* Tooltips (desktop hover) */
  .bp-card [data-tip]{position:relative}
  .bp-card [data-tip]:hover::after{content:attr(data-tip); position:absolute; left:50%; transform:translateX(-50%); bottom:100%; margin-bottom:8px; background:#0a0d14; color:#eaf3ff; border:1px solid #293047; font-size:.75rem; white-space:nowrap; padding:.3rem .5rem; border-radius:8px; box-shadow:var(--bp-shadow); z-index:5}
  /* Responsive */
  @media (max-width: 480px){
    .bp-card{width:140px; min-width:140px}
    .bp-connector{width:48px; min-width:48px}
    .bp-nav{width:36px; height:36px}
  }
  /* Screen reader utility */
  .sr-only{position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0}
</style>


<!-- Battle Pass -->
<section class="bp" aria-label="Battle Pass">
  <header class="bp-header">
    <div class="bp-level">
      <span id="bp-level-label">Level 7</span>
      <span id="bp-xp-label">1,450 / 2,000 XP</span>
    </div>
    <div class="bp-controls">
      <button class="bp-nav" id="bp-prev" aria-label="Scroll left" title="Prev">&#10094;</button>
      <button class="bp-nav" id="bp-next" aria-label="Scroll right" title="Next">&#10095;</button>
    </div>
  </header>

  <!-- The track (cards + connectors) -->
  <div class="bp-viewport">
    <div class="bp-track" id="bp-track" role="list" aria-label="Rewards track">
      <!-- Cards/connectors are injected by JS -->
    </div>
  </div>

  <!-- Accessible live region for status updates -->
  <div class="sr-only" aria-live="polite" id="bp-status"></div>
</section>


<script>
/** ----- Replace with server values (PHP can echo json_encode([...])) ----- **/
const player = {
  level: 7,            // whole level the player is currently on
  xpIntoLevel: 1450,   // XP earned within current level
  xpForLevel: 2000,    // XP needed to finish current level
};
const rewards = [
  // id, levelRequired = the level you reach to unlock the card
  { id: 1, levelRequired: 1, title: "Starter Cache", desc: "50 Souls", img: "https://picsum.photos/seed/a/300", claimed: true },
  { id: 2, levelRequired: 2, title: "Lantern Ember", desc: "+2 sp/h", img: "https://picsum.photos/seed/b/300", claimed: true },
  { id: 3, levelRequired: 3, title: "Soul Pouch", desc: "100 Souls", img: "https://picsum.photos/seed/c/300", claimed: true },
  { id: 4, levelRequired: 4, title: "Avatar Frame", desc: "Rare cosmetic", img: "https://picsum.photos/seed/d/300", claimed: false },
  { id: 5, levelRequired: 5, title: "Pumpkin Cache", desc: "250 Souls", img: "https://picsum.photos/seed/e/300", claimed: false },
  { id: 6, levelRequired: 6, title: "XP Boost", desc: "2x for 1h", img: "https://picsum.photos/seed/f/300", claimed: false },
  { id: 7, levelRequired: 7, title: "Lantern Upgrade", desc: "+4 sp/h", img: "https://picsum.photos/seed/g/300", claimed: false },
  { id: 8, levelRequired: 8, title: "Epic Cache", desc: "500 Souls", img: "https://picsum.photos/seed/h/300", claimed: false },
  { id: 9, levelRequired: 9, title: "Border - Phantom", desc: "Cosmetic", img: "https://picsum.photos/seed/i/300", claimed: false },
  { id:10, levelRequired:10, title: "Mythic Cache", desc: "1,000 Souls", img: "https://picsum.photos/seed/j/300", claimed: false },
];
/** ---------------------------------------------------------------------- **/

const trackEl = document.getElementById('bp-track');
const statusEl = document.getElementById('bp-status');
const prevBtn = document.getElementById('bp-prev');
const nextBtn = document.getElementById('bp-next');
const levelLabel = document.getElementById('bp-level-label');
const xpLabel = document.getElementById('bp-xp-label');

function fmt(n){ return n.toLocaleString(); }
levelLabel.textContent = `Level ${player.level}`;
xpLabel.textContent = `${fmt(player.xpIntoLevel)} / ${fmt(player.xpForLevel)} XP`;

function canClaim(card){
  return (player.level >= card.levelRequired) && !card.claimed;
}
// connector fill between card[i] and card[i+1]
function connectorFillFor(i){
  const curr = rewards[i];
  const next = rewards[i+1];
  // If player is above the next requirement, connector is fully done
  if (player.level >= next.levelRequired) return 100;
  // If player is below current requirement, connector empty
  if (player.level < curr.levelRequired) return 0;
  // Player is between curr.levelRequired and next.levelRequired
  // Fill by % of current level progress (xpIntoLevel / xpForLevel)
  return Math.max(0, Math.min(100, Math.round((player.xpIntoLevel / player.xpForLevel) * 100)));
}

function makeCard(card){
  const li = document.createElement('div');
  li.className = 'bp-item';
  li.setAttribute('role','listitem');

  const cardDiv = document.createElement('article');
  cardDiv.className = 'bp-card';
  if (card.claimed) cardDiv.classList.add('claimed');
  else if (canClaim(card)) cardDiv.classList.add('available');
  else cardDiv.classList.add('locked');

  // mark current (the highest level not yet claimed, or equal to player level)
  if (card.levelRequired === player.level) cardDiv.classList.add('current');

  const imgWrap = document.createElement('div');
  imgWrap.className = 'bp-img';
  const img = document.createElement('img');
  img.src = card.img;
  img.alt = card.title;
  imgWrap.appendChild(img);

  const title = document.createElement('div');
  title.className = 'bp-title';
  title.textContent = card.title;

  const desc = document.createElement('div');
  desc.className = 'bp-desc';
  desc.textContent = card.desc;

  const meta = document.createElement('div');
  meta.className = 'bp-meta';

  const tag = document.createElement('span');
  tag.className = 'bp-tag';
  tag.textContent = `Lv ${card.levelRequired}`;

  const btn = document.createElement('button');
  btn.className = 'bp-claim';
  btn.type = 'button';
  btn.textContent = card.claimed ? 'Claimed' : 'Claim';
  btn.disabled = !canClaim(card);

  // tooltip on hover: requirement
  btn.setAttribute('data-tip',
    canClaim(card) ? 'Ready to claim' : `Reach Level ${card.levelRequired} to claim`
  );

  btn.addEventListener('click', async () => {
    if (btn.disabled) return;
    // Optimistic UI: update immediately, then POST. Roll back on error if you want.
    btn.disabled = true;
    btn.textContent = 'Claimed';
    cardDiv.classList.remove('available');
    cardDiv.classList.add('claimed');
    statusEl.textContent = `Claimed: ${card.title}`;

    // TODO: wire to your endpoint
    try{
      // Example: replace with your real URL & CSRF handling
      const res = await fetch('/claim_reward.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ reward_id: card.id })
      });
      if(!res.ok){
        // roll back UI if server rejects
        btn.disabled = false;
        btn.textContent = 'Claim';
        cardDiv.classList.remove('claimed');
        cardDiv.classList.add('available');
        statusEl.textContent = `Could not claim ${card.title}`;
      }
    }catch(e){
      btn.disabled = false;
      btn.textContent = 'Claim';
      cardDiv.classList.remove('claimed');
      cardDiv.classList.add('available');
      statusEl.textContent = `Network error while claiming ${card.title}`;
    }
  });

  meta.append(tag, btn);
  cardDiv.append(imgWrap, title, desc, meta);
  li.appendChild(cardDiv);

  return li;
}

function makeConnector(i){
  const c = document.createElement('div');
  c.className = 'bp-connector';
  const fill = document.createElement('div');
  fill.className = 'bp-fill';
  const pct = connectorFillFor(i);
  fill.style.width = pct + '%';
  if (pct >= 100) c.classList.add('done');
  c.appendChild(fill);
  return c;
}

function buildTrack(){
  trackEl.innerHTML = '';
  rewards.forEach((card, i) => {
    const cardEl = makeCard(card);
    trackEl.appendChild(cardEl);
    if (i < rewards.length - 1){
      trackEl.appendChild(makeConnector(i));
    }
  });
}
buildTrack();

/* Scroll helpers */
function scrollByCards(dir = 1){
  const cards = Array.from(trackEl.querySelectorAll('.bp-item'));
  if (!cards.length) return;
  // Find a card that is most centered
  const viewportRect = trackEl.getBoundingClientRect();
  let best = {el:cards[0], score: Infinity};
  cards.forEach(el=>{
    const r = el.getBoundingClientRect();
    const center = r.left + r.width/2;
    const vpCenter = viewportRect.left + viewportRect.width/2;
    const score = Math.abs(center - vpCenter);
    if (score < best.score) best = {el, score};
  });
  let idx = cards.indexOf(best.el) + dir;
  idx = Math.max(0, Math.min(cards.length-1, idx));
  cards[idx].scrollIntoView({behavior:'smooth', inline:'center', block:'nearest'});
}
prevBtn.addEventListener('click',()=>scrollByCards(-1));
nextBtn.addEventListener('click',()=>scrollByCards(1));

/* Keyboard accessibility: arrow keys to pan when focused */
trackEl.setAttribute('tabindex','0');
trackEl.addEventListener('keydown',(e)=>{
  if (e.key === 'ArrowRight'){ e.preventDefault(); scrollByCards(1); }
  if (e.key === 'ArrowLeft'){ e.preventDefault(); scrollByCards(-1); }
});

/* Auto-jump near current reward on load */
(function snapToCurrent(){
  const current = trackEl.querySelector('.bp-card.current');
  if (current){
    current.scrollIntoView({behavior:'auto', inline:'center', block:'nearest'});
  }
})();
</script>


<?php
include 'footer.php';
?>