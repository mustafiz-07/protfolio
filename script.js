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
const form = document.getElementById("Form");
const formStatus = document.getElementById("form-status");

// Listen for the form submission event
form.addEventListener("submit", async function (event) {
  // Prevent the default form submission behavior (page reload)
  event.preventDefault();

  // Show a loading message
  formStatus.style.display = "block";
  formStatus.textContent = "Sending...";
  formStatus.className = "form-status";

  const formspreeEndpoint = "https://formspree.io/f/mdkdjbzz";

  // Create a FormData object from the form
  const formData = new FormData(form);

  try {
    // Send the form data to the Formspree endpoint using the fetch API
    const response = await fetch(formspreeEndpoint, {
      method: "POST",
      body: formData,
      headers: {
        Accept: "application/json",
      },
    });

    // Check if the response was successful
    if (response.ok) {
      formStatus.textContent = "Thank you! Your message has been sent.";
      formStatus.className = "form-status success";
      form.reset(); // Reset the form fields
    } else {
      const data = await response.json();
      if (data.errors) {
        formStatus.textContent = data.errors
          .map((error) => error.message)
          .join(", ");
      } else {
        formStatus.textContent =
          "Oops! There was an error sending your message.";
      }
      formStatus.className = "form-status error";
    }
  } catch (error) {
    // Handle network errors
    formStatus.textContent = "Oops! An unexpected network error occurred.";
    formStatus.className = "form-status error";
    console.error("Error submitting form:", error);
  }
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
