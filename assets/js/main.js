document.addEventListener("DOMContentLoaded", function () {
  console.log("JavaScript loaded!"); // Debugging

  // Tooltip for borrowed books
  const borrowedBookElements = document.querySelectorAll(".borrowed-books");

  borrowedBookElements.forEach(element => {
      element.addEventListener("mouseover", function () {
          let books = this.getAttribute("data-books");
          if (!books) return;

          let tooltip = document.createElement("div");
          tooltip.className = "tooltip";
          tooltip.textContent = books;
          document.body.appendChild(tooltip);

          document.addEventListener("mousemove", function moveTooltip(event) {
              tooltip.style.left = event.pageX + 10 + "px";
              tooltip.style.top = event.pageY + 10 + "px";
          });

          element.addEventListener("mouseleave", function () {
              tooltip.remove();
              document.removeEventListener("mousemove", moveTooltip);
          }, { once: true });
      });
  });

  // Borrow Confirmation Popup
  const borrowButtons = document.querySelectorAll(".borrow-btn");

  if (!borrowButtons.length) {
      console.error("No borrow buttons found!");
      return;
  }

  if (!document.querySelector(".borrow-popup")) {
      const popup = document.createElement("div");
      popup.className = "borrow-popup";
      popup.style.display = "none";
      popup.innerHTML = `
          <div class="popup-content">
              <h3>Confirm Borrow</h3>
              <p>You have 4 weeks to return this book. If you don't, there will be a penalty.</p>
              <div class="popup-buttons">
                  <button id="confirm-borrow">Yes, I'm sure</button>
                  <button id="cancel-borrow">Cancel</button>
              </div>
          </div>
      `;

      const overlay = document.createElement("div");
      overlay.className = "borrow-overlay";
      overlay.style.display = "none";

      document.body.appendChild(popup);
      document.body.appendChild(overlay);
  }

  const popup = document.querySelector(".borrow-popup");
  const overlay = document.querySelector(".borrow-overlay");
  let selectedForm = null;

  borrowButtons.forEach(button => {
      button.addEventListener("click", (e) => {
          e.preventDefault();
          selectedForm = button.closest("form");

          if (!selectedForm) {
              console.error("Form not found for borrow button!");
              return;
          }

          popup.style.display = "block";
          overlay.style.display = "block";
      });
  });

  document.body.addEventListener("click", (event) => {
      if (event.target.id === "cancel-borrow") {
          popup.style.display = "none";
          overlay.style.display = "none";
          selectedForm = null;
      }

      if (event.target.id === "confirm-borrow" && selectedForm) {
          console.log("Submitting form for book:", selectedForm.querySelector("input[name='book_id']").value);
          popup.style.display = "none";
          overlay.style.display = "none";
          selectedForm.submit();
      }

      if (event.target.classList.contains("borrow-overlay")) {
          popup.style.display = "none";
          overlay.style.display = "none";
          selectedForm = null;
      }
  });

  console.log("Borrow popup script initialized successfully.");
});



