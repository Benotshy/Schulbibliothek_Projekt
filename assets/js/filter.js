document.addEventListener('DOMContentLoaded', function() {
  let filterCheckbox = document.getElementById('availableFilter');
  let url = new URL(window.location.href);

  // Ensure checkbox remains checked when reloading the page
  if (url.searchParams.get('available') === 'true') {
      filterCheckbox.checked = true;
  }

  filterCheckbox.addEventListener('change', function() {
      if (this.checked) {
          url.searchParams.set('available', 'true'); // Add filter
      } else {
          url.searchParams.delete('available'); // Remove filter
      }
      window.location.href = url.href; // Reload page with filter
  });
});
