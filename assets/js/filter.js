document.addEventListener('DOMContentLoaded', function() {
  let filterCheckbox = document.getElementById('availableFilter');
  let url = new URL(window.location.href);

  // ensure checkbox remains checked when reloading the page
  if (url.searchParams.get('available') === 'true') {
      filterCheckbox.checked = true;
  }

  filterCheckbox.addEventListener('change', function() {
      if (this.checked) {
          url.searchParams.set('available', 'true');
      } else {
          url.searchParams.delete('available');
      }
      window.location.href = url.href;
  });
});
