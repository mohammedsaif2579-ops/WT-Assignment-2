// Get the form elements that are used for validation and interaction.
const form = document.getElementById("registrationForm");
const ids = ["name", "roll", "email", "course", "password", "confirmPassword", "document"];
const progress = document.getElementById("progress");
const fileInput = document.getElementById("document");
const uploadBox = document.getElementById("uploadBox");
const fileName = document.getElementById("fileName");
const fileInfo = document.getElementById("fileInfo");
// Uploaded documents must be no larger than 5 MB and use one of these file types.
const allowedTypes = [
  "application/pdf",
  "image/jpeg",
  "image/png"
];

// Show or clear an error message and update the field appearance.
function showError(id, message) {
  const input = document.getElementById(id);
  const field = input.closest(".field");
  document.getElementById(id + "Error").textContent = message;
  field.classList.toggle("invalid", Boolean(message));
  field.classList.toggle("valid", !message && Boolean(input.value));
}

// Give the password a score based on length and character variety.
function passwordStrength(value) {
  let score = 0;
  if (value.length >= 8) score++;
  if (/[a-z]/.test(value) && /[A-Z]/.test(value)) {
    score++;
  }
  if (/\d/.test(value)) {
    score++;
  }
  if (/[^A-Za-z0-9]/.test(value)) {
    score++;
  }
  return score;
}

// Update the password strength bars and their text label.
function updateStrength() {
  const value = document.getElementById("password").value;
  const score = value ? passwordStrength(value) : 0;
  document.getElementById("strengthBars").dataset.level = value ? score : 0;
  document.getElementById("strengthText").textContent =
    "Password strength: " + ["not set", "weak", "fair", "good", "strong"][score];
}

// Check one field at a time and return true only when it is valid.
function validate(id) {
  const input = document.getElementById(id);
  const value = input.value.trim();
  let error = "";

  // Each case contains the validation rule for a particular form field.
  switch (id) {
    case "name":
      if (!value) error = "Please enter your full name.";
      else if (value.length < 3 || !/^[A-Za-z][A-Za-z .'-]*$/.test(value))
        error = "Enter at least 3 characters using letters and normal name punctuation.";
      break;
    case "roll":
      if (!value) error = "Please enter your roll number.";
      else if (!/^[A-Za-z0-9/-]{3,20}$/.test(value))
        error = "Use 3–20 letters, numbers, hyphens or slashes.";
      break;
    case "email":
      if (!value) error = "Please enter your email address.";
      else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value))
        error = "Enter a valid email address, such as name@example.com.";
      break;
    case "course":
      if (!value) error = "Please select a course.";
      break;
    case "password":
      if (!value) error = "Please create a password.";
      else if (passwordStrength(value) < 4)
        error = "Use 8+ characters with uppercase, lowercase, a number and a symbol.";
      break;
    case "confirmPassword":
      if (!value) error = "Please confirm your password.";
      else if (value !== document.getElementById("password").value)
        error = "The passwords do not match.";
      break;
    case "document": {
      const file = fileInput.files[0];
      if (!file) error = "Please attach a PDF, JPG or PNG document.";
      else if (!allowedTypes.includes(file.type)) error = "Only PDF, JPG and PNG files are allowed.";
      else if (file.size > MAX_SIZE) error = "File must be 5 MB or smaller.";
      break;
    }
  }
  showError(id, error);
  return !error;
}

// Calculate how many required fields have been completed.
function updateProgress() {
  const completed = ids.filter(id => {
    const el = document.getElementById(id);
    return id === "document" ? el.files.length > 0 : Boolean(el.value.trim());
  }).length + (document.getElementById("declaration").checked ? 1 : 0);
  progress.style.width = Math.round((completed / 8) * 100) + "%";
}

// Validate fields when the user leaves, edits or changes an input.
ids.forEach(id => {
  const el = document.getElementById(id);
  el.addEventListener("blur", () => validate(id));
  el.addEventListener("input", () => {
    if (id === "password") {
      updateStrength();
      if (document.getElementById("confirmPassword").value) validate("confirmPassword");
    }
    if (el.closest(".field").classList.contains("invalid") || el.closest(".field").classList.contains("valid")) validate(id);
    updateProgress();
  });
  el.addEventListener("change", () => {
    validate(id);
    updateProgress();
  });
});

// Refresh the progress bar when the declaration checkbox changes.
document.getElementById("declaration").addEventListener("change", updateProgress);

// Let the user show or hide the password while typing.
document.getElementById("togglePassword").addEventListener("click", () => {
  const password = document.getElementById("password");
  const button = document.getElementById("togglePassword");
  password.type = password.type === "password" ? "text" : "password";
  button.textContent = password.type === "password" ? "SHOW" : "HIDE";
});

// Show the selected file name and size, then validate the upload.
fileInput.addEventListener("change", () => {
  const file = fileInput.files[0];
  if (file) {
    fileName.textContent = file.name;
    fileInfo.textContent = (file.size / 1024 / 1024).toFixed(2) + " MB · Selected";
  } else {
    fileName.innerHTML = 'Drop your file here, or <em>browse</em>';
    fileInfo.textContent = "PDF, JPG or PNG · Maximum 5 MB";
  }
  validate("document");
  updateProgress();
});

// Add a visual highlight while a file is dragged over the upload area.
["dragenter", "dragover"].forEach(eventName => uploadBox.addEventListener(eventName, event => {
  event.preventDefault();
  uploadBox.classList.add("dragover");
}));
// Remove the highlight after dragging ends and handle a dropped file.
["dragleave", "drop"].forEach(eventName => uploadBox.addEventListener(eventName, event => {
  event.preventDefault();
  uploadBox.classList.remove("dragover");
  if (eventName === "drop" && event.dataTransfer.files.length) {
    fileInput.files = event.dataTransfer.files;
    fileInput.dispatchEvent(new Event("change", { bubbles: true }));
  }
}));

// Stop the normal form submission and validate all fields in the browser.
form.addEventListener("submit", event => {
  event.preventDefault();
  const results = ids.map(validate);
  const declaration = document.getElementById("declaration");
  const declarationError = document.getElementById("declarationError");
  declarationError.textContent = declaration.checked ? "" : "Please confirm the declaration before submitting.";
  updateProgress();

  // Submission is accepted only when every field and the declaration are valid.
  const allValid = results.every(Boolean) && declaration.checked;
  const success = document.getElementById("successMessage");
  if (!allValid) {
    success.hidden = true;
    const firstInvalid = form.querySelector(".field.invalid input, .field.invalid select");
    if (firstInvalid) firstInvalid.focus();
    else declaration.focus();
    return;
  }

  // Show a message explaining that the front-end checks passed.
  success.hidden = false;
  success.textContent = "Your form passed all client-side checks. This is a front-end demo; no information has been sent to a server or saved to a database.";
});
