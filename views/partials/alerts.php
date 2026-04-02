<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>" id="flashAlert">
  <div class="alert-content">
    <?php if($flash['type'] === 'success'): ?>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    <?php elseif($flash['type'] === 'error'): ?>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <?php endif; ?>
    <span><?= clean($flash['message']) ?></span>
  </div>
  <button class="alert-close" onclick="this.parentElement.remove()">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
  </button>
</div>
<script>setTimeout(()=>{ const a=document.getElementById('flashAlert'); if(a) a.remove(); }, 4000);</script>
<?php endif; ?>
