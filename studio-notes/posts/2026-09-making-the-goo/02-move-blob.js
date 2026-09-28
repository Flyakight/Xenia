function moveBlob(instant) {
  var p = layout.place[state.sel];
  var t = 'translate(' + p.x + 'px,' + p.y + 'px)';

  layout.blob.style.transform = t;
  layout.trail.style.transform = t;
}
