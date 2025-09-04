// modal functions for projects
function openProjectModal(projectId = null) {
  const modal = document.getElementById("projectModal");
  const form = document.getElementById("projectForm");
  const title = document.getElementById("modalTitle");

  if (projectId) {
    // edit mode
    title.textContent = "Edit Project";
    form.querySelector('input[name="action"]').value = "edit_project";
    document.getElementById("projectId").value = projectId;

    fetch(`get_record.php?type=project&id=${projectId}`)
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
    // add mode
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
    // form to submit the delete request
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

// modal Functions for Education
function openEducationModal(educationId = null) {
  const modal = document.getElementById("educationModal");
  const form = document.getElementById("educationForm");
  const title = document.getElementById("educationModalTitle");

  if (educationId) {
    // edit mode
    title.textContent = "Edit Education Record";
    form.querySelector('input[name="action"]').value = "edit_education";
    document.getElementById("educationId").value = educationId;

    fetch(`get_record.php?type=education&id=${educationId}`)
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
    // add mode
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
    //form to submit the delete request
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

document.addEventListener("DOMContentLoaded", function () {
  // hide success/error messages after 5s
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

  // in loading state handle the button
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = "Saving...";
        // if failed re-enable the button
        setTimeout(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = submitBtn.dataset.originalText || "Save";
        }, 3000);
      }
    });
  });

  //button text
  document.querySelectorAll('button[type="submit"]').forEach((btn) => {
    btn.dataset.originalText = btn.innerHTML;
  });

  // session timeout warning
  let sessionWarningShown = false;

  // run this once when the page loads
  setTimeout(() => {
    if (!sessionWarningShown) {
      if (
        confirm(
          "Your session will expire in 5 minutes. Do you want to stay logged in?"
        )
      ) {
        window.location.reload();
      }
      sessionWarningShown = true;
    }
  }, 25 * 60 * 1000); // warns 5 min before 30-min session
  //handle image preview
  const imageInput = document.getElementById("projectImage");
  if (imageInput) {
    imageInput.addEventListener("change", function () {
      handleImagePreview(this);
    });
  }
});

// image preview project form
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
