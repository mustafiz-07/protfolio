// Admin Panel JavaScript

// Modal Functions for Projects
function openProjectModal(projectId = null) {
  const modal = document.getElementById("projectModal");
  const form = document.getElementById("projectForm");
  const title = document.getElementById("modalTitle");

  if (projectId) {
    // Edit mode
    title.textContent = "Edit Project";
    form.querySelector('input[name="action"]').value = "edit_project";
    document.getElementById("projectId").value = projectId;

    // Fetch project data via AJAX
    fetch(`api/get_record.php?type=project&id=${projectId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          alert("Error fetching project data: " + data.error);
          return;
        }

        document.getElementById("projectTitle").value = data.title;
        document.getElementById("projectDescription").value = data.description;
        document.getElementById("projectLink").value = data.link || "";
        document.getElementById("projectStatus").value = data.status;
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Error fetching project data");
      });
  } else {
    // Add mode
    title.textContent = "Add New Project";
    form.querySelector('input[name="action"]').value = "add_project";
    form.reset();
    document.getElementById("projectId").value = "";
  }

  modal.style.display = "block";
  document.body.style.overflow = "hidden";
}

function closeProjectModal() {
  const modal = document.getElementById("projectModal");
  modal.style.display = "none";
  document.body.style.overflow = "auto";
}

function editProject(projectId) {
  openProjectModal(projectId);
}

function deleteProject(projectId) {
  if (confirm("Are you sure you want to delete this project?")) {
    // Create a form to submit the delete request
    const form = document.createElement("form");
    form.method = "POST";
    form.style.display = "none";

    const actionInput = document.createElement("input");
    actionInput.type = "hidden";
    actionInput.name = "action";
    actionInput.value = "delete_project";

    const idInput = document.createElement("input");
    idInput.type = "hidden";
    idInput.name = "project_id";
    idInput.value = projectId;

    form.appendChild(actionInput);
    form.appendChild(idInput);
    document.body.appendChild(form);
    form.submit();
  }
}

// Modal Functions for Education
function openEducationModal(educationId = null) {
  const modal = document.getElementById("educationModal");
  const form = document.getElementById("educationForm");
  const title = document.getElementById("educationModalTitle");

  if (educationId) {
    // Edit mode
    title.textContent = "Edit Education Record";
    form.querySelector('input[name="action"]').value = "edit_education";
    document.getElementById("educationId").value = educationId;

    // Fetch education data via AJAX
    fetch(`api/get_record.php?type=education&id=${educationId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          alert("Error fetching education data: " + data.error);
          return;
        }

        document.getElementById("educationDegree").value = data.degree;
        document.getElementById("educationInstitution").value =
          data.institution;
        document.getElementById("educationYear").value = data.year;
        document.getElementById("educationDescription").value =
          data.description;
        document.getElementById("educationStatus").value = data.status;
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Error fetching education data");
      });
  } else {
    // Add mode
    title.textContent = "Add Education Record";
    form.querySelector('input[name="action"]').value = "add_education";
    form.reset();
    document.getElementById("educationId").value = "";
  }

  modal.style.display = "block";
  document.body.style.overflow = "hidden";
}

function closeEducationModal() {
  const modal = document.getElementById("educationModal");
  modal.style.display = "none";
  document.body.style.overflow = "auto";
}

function editEducation(educationId) {
  openEducationModal(educationId);
}

function deleteEducation(educationId) {
  if (confirm("Are you sure you want to delete this education record?")) {
    // Create a form to submit the delete request
    const form = document.createElement("form");
    form.method = "POST";
    form.style.display = "none";

    const actionInput = document.createElement("input");
    actionInput.type = "hidden";
    actionInput.name = "action";
    actionInput.value = "delete_education";

    const idInput = document.createElement("input");
    idInput.type = "hidden";
    idInput.name = "education_id";
    idInput.value = educationId;

    form.appendChild(actionInput);
    form.appendChild(idInput);
    document.body.appendChild(form);
    form.submit();
  }
}

// Close modals when clicking outside
document.addEventListener("DOMContentLoaded", function () {
  // Auto-hide success/error messages after 5 seconds
  const messages = document.querySelectorAll(
    ".success-message, .error-message"
  );
  messages.forEach((message) => {
    setTimeout(() => {
      message.style.opacity = "0";
      setTimeout(() => {
        message.style.display = "none";
      }, 300);
    }, 5000);
  });

  // Close modals when clicking outside
  window.addEventListener("click", function (event) {
    const projectModal = document.getElementById("projectModal");
    const educationModal = document.getElementById("educationModal");

    if (event.target === projectModal) {
      closeProjectModal();
    }

    if (event.target === educationModal) {
      closeEducationModal();
    }
  });

  // Handle form submissions with loading states
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML =
          '<i class="fas fa-spinner fa-spin"></i> Saving...';

        // Re-enable after 3 seconds in case of failure
        setTimeout(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = submitBtn.dataset.originalText || "Save";
        }, 3000);
      }
    });
  });

  // Store original button text
  document.querySelectorAll('button[type="submit"]').forEach((btn) => {
    btn.dataset.originalText = btn.innerHTML;
  });

  // Session timeout warning
  let sessionWarningShown = false;
  setInterval(() => {
    if (!sessionWarningShown) {
      // Show warning 5 minutes before session expires (25 minutes)
      setTimeout(() => {
        if (
          confirm(
            "Your session will expire in 5 minutes. Do you want to stay logged in?"
          )
        ) {
          // Refresh the page to extend session
          window.location.reload();
        }
        sessionWarningShown = true;
      }, 25 * 60 * 1000); // 25 minutes
    }
  }, 1000);
});

// Image preview for project form
function handleImagePreview(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      // Create or update image preview
      let preview = document.getElementById("imagePreview");
      if (!preview) {
        preview = document.createElement("img");
        preview.id = "imagePreview";
        preview.style.maxWidth = "200px";
        preview.style.maxHeight = "150px";
        preview.style.marginTop = "10px";
        preview.style.borderRadius = "5px";
        input.parentNode.appendChild(preview);
      }
      preview.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Add event listener for image input
document.addEventListener("DOMContentLoaded", function () {
  const imageInput = document.getElementById("projectImage");
  if (imageInput) {
    imageInput.addEventListener("change", function () {
      handleImagePreview(this);
    });
  }
});

// Responsive sidebar toggle for mobile
function toggleSidebar() {
  const sidebar = document.querySelector(".sidebar");
  sidebar.style.transform =
    sidebar.style.transform === "translateX(0px)"
      ? "translateX(-100%)"
      : "translateX(0px)";
}

// Add hamburger menu for mobile (you can add this to the header)
document.addEventListener("DOMContentLoaded", function () {
  if (window.innerWidth <= 768) {
    const header = document.querySelector(".admin-header");
    const hamburger = document.createElement("button");
    hamburger.innerHTML = '<i class="fas fa-bars"></i>';
    hamburger.className = "hamburger-menu";
    hamburger.style.cssText = `
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--admin-primary);
            cursor: pointer;
            margin-right: 1rem;
        `;
    hamburger.addEventListener("click", toggleSidebar);
    header.insertBefore(hamburger, header.firstChild);
  }
});

// Auto-refresh dashboard stats every 30 seconds
if (
  window.location.pathname.includes("dashboard.php") &&
  new URLSearchParams(window.location.search).get("page") === "dashboard"
) {
  setInterval(() => {
    // Update time
    const timeElement = document.querySelector(".stat-card .stat-info h3");
    if (timeElement && timeElement.textContent.includes(":")) {
      timeElement.textContent = new Date().toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
      });
    }
  }, 30000);
}
