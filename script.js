let menu = document.querySelector("#menu-icon");
let navbar = document.querySelector(".navbar");

function isMobileView() {
  return window.matchMedia("(max-width: 991px)").matches;
}

menu.onclick = () => {
  navbar.classList.toggle("active");
  if (isMobileView()) {
    menu.style.display = "none";
  }
};

window.onscroll = () => {
  navbar.classList.remove("active");
  if (isMobileView()) {
    menu.style.display = "block";
  } else {
    menu.style.display = "none";
  }
};

// Ensure correct menu icon display on resize
window.addEventListener("resize", () => {
  if (isMobileView()) {
    menu.style.display = "block";
  } else {
    menu.style.display = "none";
  }
});
// form submission status handling
const form = document.getElementById("Form");
const messageBox = document.getElementById("form-status");

form.addEventListener("submit", function (e) {
  e.preventDefault(); // prevent default redirect

  const formData = new FormData(form);

  fetch("https://formsubmit.co/YOUR_EMAIL_HERE", {
    method: "POST",
    body: formData,
    headers: {
      Accept: "application/json",
    },
  })
    .then((response) => {
      if (response.ok) {
        messageBox.style.display = "block";
        messageBox.style.color = "green";
        messageBox.innerText = "✅ Your message has been sent successfully!";
        form.reset();
      } else {
        throw new Error("Network response was not ok");
      }
    })
    .catch((error) => {
      messageBox.style.display = "block";
      messageBox.style.color = "red";
      messageBox.innerText = "❌ Oops! Something went wrong. Please try again.";
    });
});

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
