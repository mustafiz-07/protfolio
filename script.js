let menu = document.querySelector("#menu-icon");
let navbar = document.querySelector(".navbar");
let closeIcon = document.querySelector("#close-icon");

function isMobileView() {
  return window.matchMedia("(max-width: 991px)").matches;
}

function closeNavbar() {
  navbar.classList.remove("active");
  if (isMobileView()) {
    menu.style.display = "block";
  }
}

menu.onclick = () => {
  navbar.classList.toggle("active");
  if (isMobileView()) {
    menu.style.display = "none";
  }
};

closeIcon.onclick = closeNavbar;
//on scroll close the navbar
window.onscroll = () => {
  closeNavbar();
};

// correct menu icon display on resize
window.addEventListener("resize", () => {
  if (isMobileView()) {
    menu.style.display = "block";
  } else {
    menu.style.display = "none";
  }
});
// form submission handling
document.getElementById("Form").addEventListener("submit", function (e) {
  e.preventDefault();

  const formStatusDiv = document.getElementById("form-status");
  const submitButton = this.querySelector('input[type="submit"]');
  const formData = new FormData(this);

  // disable button during submission
  submitButton.value = "Sending...";
  submitButton.disabled = true;

  fetch("http://localhost/My%20website/admin/send_email.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      formStatusDiv.textContent = data.message;
      formStatusDiv.style.display = "block";
      formStatusDiv.style.color = data.status === "success" ? "green" : "red";

      if (data.status === "success") {
        // Clear the form on success
        this.reset();
      }
    })
    .catch((error) => {
      formStatusDiv.textContent = "An error occurred. Please try again.";
      formStatusDiv.style.display = "block";
      formStatusDiv.style.color = "red";
    })
    .finally(() => {
      // Re-enable submit button
      submitButton.value = "Send Message";
      submitButton.disabled = false;

      // Hide the status message after 5 seconds
      setTimeout(() => {
        formStatusDiv.style.display = "none";
      }, 5000);
    });
});

// theme toggle functionality
const themeToggle = document.getElementById("theme-toggle");
const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");

// function
function toggleTheme() {
  if (document.documentElement.getAttribute("data-theme") === "dark") {
    document.documentElement.removeAttribute("data-theme");
    localStorage.setItem("theme", "light");
    themeToggle.textContent = "🌙";
  } else {
    document.documentElement.setAttribute("data-theme", "dark");
    localStorage.setItem("theme", "dark");
    themeToggle.textContent = "☀️";
  }
}

// initialize with local storage
function initializeTheme() {
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark" || (!savedTheme && prefersDarkScheme.matches)) {
    document.documentElement.setAttribute("data-theme", "dark");
    themeToggle.textContent = "☀️";
  } else {
    themeToggle.textContent = "🌙";
  }
}

themeToggle.addEventListener("click", toggleTheme);
initializeTheme();

// Typewriter effect for the hero section
const words = ["Web Developer", "Designer", "Content Creator"];
let wordIndex = 0;
let charIndex = 0;
let isDeleting = false;

function type() {
  const currentWord = words[wordIndex];
  const display = document.getElementById("typewriter");

  if (isDeleting) {
    display.textContent = currentWord.substring(0, charIndex--);
  } else {
    display.textContent = currentWord.substring(0, charIndex++);
  }

  let speed = isDeleting ? 50 : 120;

  if (!isDeleting && charIndex === currentWord.length + 1) {
    isDeleting = true;
    speed = 1000;
  } else if (isDeleting && charIndex === -1) {
    isDeleting = false;
    wordIndex = (wordIndex + 1) % words.length;
    speed = 500;
  }

  setTimeout(type, speed);
}

type();

document.addEventListener("DOMContentLoaded", () => {
  const circles = document.querySelectorAll(".circle-progress");

  circles.forEach((circle) => {
    const percent = circle.getAttribute("data-percent");
    const circumference = 2 * 3.1416 * 40; // 2πr where r=40 (radius)
    const offset = circumference - (percent / 100) * circumference;
    circle.style.setProperty("--progress", offset);
  });

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate");
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.5,
    }
  );

  document.querySelectorAll(".circle-wrap").forEach((wrap) => {
    observer.observe(wrap);
  });

  loadProjects();
  loadEducation();
});

// Function to load projects from database
async function loadProjects() {
  try {
    const response = await fetch(
      "http://localhost/My%20website/api/portfolio.php?type=projects"
    );
    const result = await response.json();

    if (result.success && result.data.length > 0) {
      const projectGrid = document.querySelector(".project-grid");
      if (projectGrid) {
        projectGrid.innerHTML = "";

        result.data.forEach((project) => {
          const projectElement = document.createElement("div");
          projectElement.className = "project";
          projectElement.innerHTML = `
            <img src="images/${
              project.image || "cse-mini-projects.webp"
            }" alt="${project.title}" />
            <h3>${project.title}</h3>
            <p>${project.description}</p>
            <a href="${project.link || "#projects"}" class="cta-button" ${
            project.link ? 'target="_blank"' : ""
          }>
              ${project.link ? "View Project" : "Coming Soon"}
            </a>
          `;
          projectGrid.appendChild(projectElement);
        });
      }
    }
  } catch (error) {
    console.error("Error loading projects:", error);
    // Keep static if API fails
  }
}

// Function to load education from database
async function loadEducation() {
  try {
    const response = await fetch(
      "http://localhost/My%20website/api/portfolio.php?type=education"
    );
    const result = await response.json();
    const educationTimeline = document.querySelector(".education-timeline");
    if (result.success && result.data.length > 0) {
      if (educationTimeline) {
        educationTimeline.innerHTML = ""; // Clear existing content
        educationTimeline.classList.remove("no-items");
        result.data.forEach((education) => {
          const educationElement = document.createElement("div");
          educationElement.className = "education-item";
          educationElement.innerHTML = `
            <div class="education-content">
              <div class="year">${education.year}</div>
              <h3>${education.degree}</h3>
              <p class="institution">${education.institution}</p>
              <p class="description">${education.description}</p>
            </div>
          `;
          educationTimeline.appendChild(educationElement);
        });
      }
    } else {
      if (educationTimeline) {
        educationTimeline.classList.add("no-items");
        educationTimeline.innerHTML = "<p>No education items found.</p>";
      }
    }
  } catch (error) {
    console.error("Error loading education:", error);
    // Keep static content if API fails
  }
}
