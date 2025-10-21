<?php

include 'chaos_header.php';
?>

<style>
:root{
  --bg:#0c0f14; --surface:#121722; --surface2:#1a2133; --muted:#9aa3b2; --text:#e7ebf3;
  --accent:#7aa2ff; --accent2:#3dd9b6; --gold:#f6c25a; --ok:#58d68d; --danger:#ff6b6b;
  --radius:14px; --shadow:0 10px 28px rgba(0,0,0,.4);
}
.bp2{background:var(--bg); color:var(--text); padding:18px; border-radius:var(--radius); box-shadow:var(--shadow);}

.bp2-spotlight{display:grid; grid-template-columns: 380px 1fr; gap:24px; align-items:stretch;}
.bp2-art{background:linear-gradient(180deg,#0b1220,#0d1422); border-radius:16px; overflow:hidden; box-shadow:var(--shadow)}
.bp2-art img{display:block; width:100%; height:100%; object-fit:cover; aspect-ratio:16/10}

.bp2-info{display:flex; flex-direction:column; justify-content:center; gap:10px}
.bp2-path{display:flex; gap:14px; align-items:baseline}
.bp2-level{font-weight:800; font-size:1.25rem}
.bp2-xp{color:var(--muted)}
.bp2-desc{color:#c7cedd; max-width:60ch}
.bp2-actions{display:flex; gap:12px; margin-top:6px}
.bp2-claim{background:var(--accent); color:#fff; border:0; padding:.8rem 1.2rem; border-radius:12px; font-weight:800; letter-spacing:.5px; cursor:pointer}
.bp2-claim[disabled]{background:#2c3343; color:#97a0b3; cursor:not-allowed}
.bp2-upgrade{background:transparent; color:#c8a96b; border:1px solid #59472b; padding:.75rem 1rem; border-radius:12px; font-weight:700; cursor:pointer}
.bp2-flash{min-height:1.2em; color:var(--ok); font-weight:600}

.bp2-track-wrap{display:grid; grid-template-columns: 44px 1fr 44px; align-items:center; gap:10px; margin-top:18px}
.bp2-nav{width:44px; height:44px; border-radius:10px; border:0; background:var(--surface2); color:var(--text); cursor:pointer}
.bp2-track-viewport{overflow:hidden}
.bp2-track{display:flex; gap:18px; align-items:flex-start; overflow-x:auto; padding:8px; scroll-behavior:smooth}
.bp2-track::-webkit-scrollbar{height:10px}
.bp2-track::-webkit-scrollbar-thumb{background:var(--surface2); border-radius:999px}

.bp2-tile{width:140px; min-width:140px; display:flex; flex-direction:column; gap:8px; user-select:none}
.bp2-thumb{position:relative; border-radius:12px; overflow:hidden; background:var(--surface); box-shadow:var(--shadow); outline:2px solid transparent; transition:outline-color .18s}
.bp2-thumb img{display:block; width:100%; height:100%; object-fit:cover; aspect-ratio:1/1}
.bp2-badge{position:absolute; top:8px; left:8px; font-size:.7rem; padding:.2rem .45rem; border-radius:999px; background:rgba(0,0,0,.45); backdrop-filter: blur(2px)}
.bp2-lv{position:absolute; bottom:8px; left:8px; font-weight:800; background:#0b101a; color:#cad3e6; padding:.15rem .4rem; border-radius:8px; font-size:.75rem}
.bp2-state{position:absolute; top:8px; right:8px; font-size:.7rem; padding:.2rem .45rem; border-radius:999px; font-weight:800}
.bp2-state.claimed{background:linear-gradient(90deg,var(--ok),#86e6bd); color:#042613}
.bp2-state.locked{background:#1f2636; color:#8fa0bf}
.bp2-state.ready{background:linear-gradient(90deg,var(--accent),var(--accent2)); color:#07141a}

.bp2-progress{height:8px; border-radius:999px; background:#202636; overflow:hidden}
.bp2-progress > i{display:block; height:100%; width:0%; background:linear-gradient(90deg,var(--accent),var(--accent2)); transition:width .3s ease}
.bp2-tile.selected .bp2-thumb{outline-color:var(--gold)}
.bp2-tile.claimed .bp2-thumb{outline-color:rgba(88,214,141,.5)}

@media (max-width: 980px){
  .bp2-spotlight{grid-template-columns:1fr}
  .bp2-art img{aspect-ratio:16/9}
}
@media (max-width: 460px){
  .bp2-tile{width:120px; min-width:120px}
}
</style>


<br />

<section class="bp2" aria-label="Battle Pass">
  <!-- Spotlight -->
  <div class="bp2-spotlight">
    <div class="bp2-art">
      <img id="bp2-art-img" src="" alt="">
    </div>
    <div class="bp2-info">
      <div class="bp2-path">
        <span id="bp2-path-level" class="bp2-level">Level 1</span>
        <span id="bp2-path-xp" class="bp2-xp">0 / 0 XP</span>
      </div>
      <h2 id="bp2-title"></h2>
      <p id="bp2-desc" class="bp2-desc"></p>
      <div class="bp2-actions">
        <button id="bp2-claim" class="bp2-claim" disabled>CLAIM</button>
        <button id="bp2-upgrade" class="bp2-upgrade" type="button">UPGRADE PASS</button>
      </div>
      <div id="bp2-flash" class="bp2-flash" aria-live="polite"></div>
    </div>
  </div>

  <!-- Track -->
  <div class="bp2-track-wrap">
    <button class="bp2-nav" id="bp2-prev" aria-label="Scroll left">❮</button>

    <div class="bp2-track-viewport">
      <div id="bp2-track" class="bp2-track" role="list" aria-label="Rewards track">
        <!-- tiles injected -->
      </div>
    </div>

    <button class="bp2-nav" id="bp2-next" aria-label="Scroll right">❯</button>
  </div>
</section>


<script>
/* ===== Replace with PHP-rendered JSON ===== */
const player = {
  level: 17,           // current level
  xpIntoLevel: 750,    // XP inside this level
  xpForLevel: 2000     // XP needed to finish this level
};
const rewards = [
  { id: 101, level:16, title:"Blue Shards", desc:"100",   img:"https://picsum.photos/seed/a/720", claimed:true  },
  { id: 102, level:17, title:"Lantern Ember", desc:"+2 sp/h", img:"https://picsum.photos/seed/b/720", claimed:false },
  { id: 103, level:18, title:"Token Bundle", desc:"125", img:"https://picsum.photos/seed/c/720", claimed:false },
  { id: 104, level:19, title:"Icon – Rogue", desc:"Free", img:"https://picsum.photos/seed/d/720", claimed:false },
  { id: 105, level:20, title:"Arcane Singed", desc:"Champion skin", img:"https://picsum.photos/seed/e/720", claimed:false }
];
/* ========================================== */

const els = {
  track: document.getElementById('bp2-track'),
  artImg: document.getElementById('bp2-art-img'),
  title: document.getElementById('bp2-title'),
  desc: document.getElementById('bp2-desc'),
  lvl: document.getElementById('bp2-path-level'),
  xp: document.getElementById('bp2-path-xp'),
  claim: document.getElementById('bp2-claim'),
  flash: document.getElementById('bp2-flash'),
  prev: document.getElementById('bp2-prev'),
  next: document.getElementById('bp2-next'),
};

function fmt(n){ return n.toLocaleString(); }
function eligible(r){ return player.level >= r.level && !r.claimed; }
function fillForTile(index){
  const r = rewards[index];
  if (player.level > r.level) return 100;
  if (player.level < r.level) return 0;
  // player is on this tile's level → partial by current XP
  return Math.max(0, Math.min(100, Math.round((player.xpIntoLevel / player.xpForLevel) * 100)));
}

function tileTemplate(r, idx){
  const state = r.claimed ? 'claimed' : eligible(r) ? 'ready' : 'locked';
  const fill = fillForTile(idx);
  return `
    <div class="bp2-tile ${state}" role="listitem" data-id="${r.id}" data-index="${idx}">
      <div class="bp2-thumb">
        <img src="${r.img}" alt="${r.title}">
        <span class="bp2-badge">FREE</span>
        <span class="bp2-lv">Lv ${r.level}</span>
        <span class="bp2-state ${state}">${r.claimed ? 'Claimed' : eligible(r) ? 'Ready' : 'Locked'}</span>
      </div>
      <div class="bp2-progress"><i style="width:${fill}%"></i></div>
    </div>
  `;
}

function renderTrack(){
  els.track.innerHTML = rewards.map((r,i)=>tileTemplate(r,i)).join('');
}
renderTrack();

function updateSpotlight(selectedIndex){
  const r = rewards[selectedIndex];
  document.querySelectorAll('.bp2-tile').forEach(t=>t.classList.remove('selected'));
  const node = els.track.querySelector(`.bp2-tile[data-index="${selectedIndex}"]`);
  if (node) node.classList.add('selected');

  els.artImg.src = r.img;
  els.artImg.alt = r.title;
  els.title.textContent = r.title;
  els.desc.textContent = r.desc;
  els.lvl.textContent = `Level ${player.level}`;
  els.xp.textContent = `${fmt(player.xpIntoLevel)} / ${fmt(player.xpForLevel)} XP`;

  els.claim.disabled = !eligible(r);
  els.claim.dataset.rewardId = r.id;
}

let selected = Math.max(0, rewards.findIndex(r=>r.level >= player.level));
if (selected === -1) selected = rewards.length - 1;
updateSpotlight(selected);

/* Click to select */
els.track.addEventListener('click', e=>{
  const tile = e.target.closest('.bp2-tile');
  if (!tile) return;
  selected = parseInt(tile.dataset.index,10);
  updateSpotlight(selected);
});

/* Claim from spotlight */
els.claim.addEventListener('click', async ()=>{
  const r = rewards[selected];
  if (!eligible(r)) return;

  els.claim.disabled = true;
  els.flash.textContent = 'Claiming…';

  try{
    const res = await fetch('/claim_reward.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ reward_id: r.id })
    });
    if (!res.ok) throw new Error('Server rejected');

    // success → mark claimed, re-render tile + spotlight
    r.claimed = true;
    const tile = els.track.querySelector(`.bp2-tile[data-index="${selected}"]`);
    if (tile){
      tile.classList.remove('ready','locked'); tile.classList.add('claimed');
      tile.querySelector('.bp2-state').textContent = 'Claimed';
      tile.querySelector('.bp2-state').className = 'bp2-state claimed';
      tile.querySelector('.bp2-progress > i').style.width = '100%';
    }
    updateSpotlight(selected);
    els.flash.textContent = `Claimed: ${r.title}`;
  }catch(err){
    els.flash.style.color = 'var(--danger)';
    els.flash.textContent = 'Could not claim. Try again.';
    els.claim.disabled = false;
    setTimeout(()=>{ els.flash.style.color = 'var(--ok)'; els.flash.textContent=''; }, 1600);
  }
});

/* Arrows scroll one tile at a time */
function scrollByTile(dir){
  const tiles = Array.from(els.track.querySelectorAll('.bp2-tile'));
  if (!tiles.length) return;
  let idx = selected + dir;
  idx = Math.max(0, Math.min(tiles.length-1, idx));
  tiles[idx].scrollIntoView({behavior:'smooth', inline:'center', block:'nearest'});
  selected = idx;
  updateSpotlight(selected);
}
els.prev.addEventListener('click', ()=>scrollByTile(-1));
els.next.addEventListener('click', ()=>scrollByTile(1));
</script>


<?php
include 'footer.php';
?>