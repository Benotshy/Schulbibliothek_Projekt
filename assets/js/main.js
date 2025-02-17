document.addEventListener("DOMContentLoaded", function () {
  const borrowedBookElements = document.querySelectorAll(".borrowed-books");

  borrowedBookElements.forEach(element => {
    element.addEventListener("mouseover", function () {
      let books = this.getAttribute("data-books");
      if (!books) return;

      // Create tooltip div
      let tooltip = document.createElement("div");
      tooltip.className = "tooltip";
      tooltip.textContent = books;
      document.body.appendChild(tooltip);

      // Positioning tooltip near the mouse
      document.addEventListener("mousemove", function moveTooltip(event) {
        tooltip.style.left = event.pageX + 10 + "px";
        tooltip.style.top = event.pageY + 10 + "px";
      });

      // Remove tooltip when mouse leaves
      element.addEventListener("mouseleave", function () {
        tooltip.remove();
        document.removeEventListener("mousemove", moveTooltip);
      }, { once: true });
    });
  });
});
