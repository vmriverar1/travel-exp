// === VALIDACIÓN FORMULARIO FECP ===
window.FECP_VALIDATE_CONTACTO = function (form, showErrors = true) {
  const isEmail   = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  const isLetters = (v) => /^[a-zA-ZÀ-ÿ\s]+$/.test(v);
  const isCompany = (v) => /^[\p{L}\d\s.\-&',]+$/u.test(v);
  const isPhone   = (v) => /^\+?\d{7,15}$/.test(v);

  const companyWrapper = form.querySelector('.acf-fecp-form__company');
  const companyInput   = companyWrapper ? companyWrapper.querySelector('input[name="company"]') : null;
  const typeLeadInput  = form.querySelector('#type_lead');

  const getTravelAgent = (frm) => {
    const checked = frm.querySelector('input[name="travel_agent"]:checked');
    return checked ? checked.value : '0';
  };

  // Mostrar/ocultar campo company y sincronizar type_lead
  const toggleCompany = () => {
    const isAgent = getTravelAgent(form) === '1';
    if (!companyWrapper || !companyInput) return;
    companyWrapper.style.display = isAgent ? 'block' : 'none';
    typeLeadInput.value = isAgent ? '1' : '0';

    if (isAgent) {
      companyInput.setAttribute('required', 'required');
    } else {
      companyInput.removeAttribute('required');
      companyInput.value = '';
      clearError(companyInput);
    }
  };

  const clearError = (el) => {
    const field = el.closest(".acf-fecp-form__field") || el.closest(".acf-fecp-form__checkbox-group") || el.closest(".acf-fecp-form__radio-group");
    if (!field) return;
    const msg = field.querySelector(".fecp-error");
    if (msg) msg.remove();
    el.classList.remove("error", "valid");
  };

  const showError = (el, msg) => {
    if (!showErrors) return;
    clearError(el);
    const field = el.closest(".acf-fecp-form__field") || el.closest(".acf-fecp-form__checkbox-group") || el.closest(".acf-fecp-form__radio-group");
    if (!field) return;
    el.classList.add("error");
    const span = document.createElement("span");
    span.className = "fecp-error";
    span.textContent = msg;
    field.appendChild(span);
  };

  const markValid = (el) => {
    clearError(el);
    el.classList.add("valid");
  };

  const validateField = (el) => {
    const value = el.type === "checkbox" ? el.checked : (el.value || "").trim();
    const name = el.name;
    let valid = true;

    switch (name) {
      case "first_name":
        if (!value) showError(el, "First name is required"), valid = false;
        else if (!isLetters(value)) showError(el, "Only letters allowed"), valid = false;
        else markValid(el);
        break;

      case "last_name":
        if (!value) showError(el, "Last name is required"), valid = false;
        else if (!isLetters(value)) showError(el, "Only letters allowed"), valid = false;
        else markValid(el);
        break;

      case "email":
        if (!value) showError(el, "Email is required"), valid = false;
        else if (!isEmail(value)) showError(el, "Invalid email format"), valid = false;
        else markValid(el);
        break;

      case "phone":
        if (value && !isPhone(value)) showError(el, "Invalid phone number"), valid = false;
        else markValid(el);
        break;

      case "country_code":
        if (!value) showError(el, "Country is required"), valid = false;
        else markValid(el);
        break;

      case "holiday_type":
        if (!value) showError(el, "Holiday type is required"), valid = false;
        else markValid(el);
        break;

      case "destination_interes":
        if (!value) showError(el, "Destination is required"), valid = false;
        else markValid(el);
        break;

      case "package":
        if (!value) showError(el, "Package is required"), valid = false;
        else markValid(el);
        break;

      case "description":
        if (!value || value.length < 3) showError(el, "Message too short"), valid = false;
        else markValid(el);
        break;

      case "privacy":
        if (!el.checked) showError(el, "You must accept the privacy policy"), valid = false;
        else markValid(el);
        break;

      case "company":
        if (getTravelAgent(form) === '1') {
          if (!value) showError(el, "Company is required for agents"), valid = false;
          else if (!isCompany(value)) showError(el, "Invalid company name"), valid = false;
          else markValid(el);
        } else {
          clearError(el);
        }
        break;
    }

    return valid;
  };

  // Actualiza visibilidad y sincroniza type_lead antes de validar
  toggleCompany();

  let ok = true;
  const fields = form.querySelectorAll("input, select, textarea");
  fields.forEach((el) => {
    if (!validateField(el)) ok = false;
  });

  return ok;
};

// === EVENTOS EN TIEMPO REAL ===
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".acf-fecp-form");
  if (!form) return;

  // Inicializa estado de company y type_lead
  window.FECP_VALIDATE_CONTACTO(form, false);

  // Entrada de texto / textarea
  form.addEventListener("input", (e) => {
    if (["text", "email"].includes(e.target.type) || e.target.tagName === "TEXTAREA") {
      window.FECP_VALIDATE_CONTACTO(form, false);
    }
  });

  // Selects, radios, checkbox
  form.addEventListener("change", (e) => {
    if (["select-one", "radio", "checkbox"].includes(e.target.type)) {
      window.FECP_VALIDATE_CONTACTO(form, false);
    }
    // Si cambia el radio travel_agent, actualizar company + type_lead
    if (e.target.name === "travel_agent") {
      window.FECP_VALIDATE_CONTACTO(form, false);
    }
  });

  // Blur delegado
  form.addEventListener("blur", (e) => {
    if (e.target.matches("input, select, textarea")) {
      window.FECP_VALIDATE_CONTACTO(form, false);
    }
  }, true);
});
