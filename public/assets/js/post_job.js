(function () {
  // Description character counter
  var desc = document.getElementById('description');
  var counter = document.querySelector('[data-count]');

  if (desc && counter) {
    var max = desc.getAttribute('maxlength') ? parseInt(desc.getAttribute('maxlength'), 10) : 0;

    function updateCount() {
      var len = desc.value.length;
      if (max) {
        counter.textContent = len + ' / ' + max;
      } else {
        counter.textContent = String(len);
      }
    }

    desc.addEventListener('input', updateCount);
    updateCount();
  }
})();


