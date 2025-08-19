let menu = document.querySelector("#menu-icon");
let navbar = document.querySelector(".navbar");

menu.onclick = () => {
  navbar.classList.toggle("active");
  menu.style.display = "none";
};

window.onscroll = () => {
  navbar.classList.remove("active");
  menu.style.display = "block";
};
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
