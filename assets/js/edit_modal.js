// Function to open the edit modal and fill in book details
function openEditModal(id, title, author, status) {
  document.getElementById("edit_id").value = id;
  document.getElementById("edit_title").value = title;
  document.getElementById("edit_author").value = author;
  document.getElementById("edit_status").value = status;
  document.getElementById("editModal").style.display = "block";
}

// Function to close the modal
function closeEditModal() {
  document.getElementById("editModal").style.display = "none";
}

