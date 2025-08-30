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

window.onscroll = () => {
  closeNavbar();
};

// Ensure correct menu icon display on resize
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

  // Disable submit button during submission
  submitButton.value = "Sending...";
  submitButton.disabled = true;

  fetch("send_email.php", {
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

// Theme toggle functionality
const themeToggle = document.getElementById("theme-toggle");
const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");

// Function to toggle theme
function toggleTheme() {
  if (document.documentElement.getAttribute("data-theme") === "dark") {
    document.documentElement.removeAttribute("data-theme");
    localStorage.setItem("theme", "light");
    themeToggle.textContent = "🌙"; // moon emoji for dark mode option
  } else {
    document.documentElement.setAttribute("data-theme", "dark");
    localStorage.setItem("theme", "dark");
    themeToggle.textContent = "☀️"; // sun emoji for light mode option
  }
}

// Set initial theme based on user's preference or localStorage
function initializeTheme() {
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark" || (!savedTheme && prefersDarkScheme.matches)) {
    document.documentElement.setAttribute("data-theme", "dark");
    themeToggle.textContent = "☀️";
  } else {
    themeToggle.textContent = "🌙";
  }
}

// Add event listeners
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

  let speed = isDeleting ? 50 : 120; // typing / deleting speed

  if (!isDeleting && charIndex === currentWord.length + 1) {
    isDeleting = true;
    speed = 1000; // pause before deleting
  } else if (isDeleting && charIndex === -1) {
    isDeleting = false;
    wordIndex = (wordIndex + 1) % words.length; // move to next word
    speed = 500; // pause before typing new word
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
});
