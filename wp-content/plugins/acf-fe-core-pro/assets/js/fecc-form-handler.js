(function () {
  "use strict";

  /**
   * Serializa todos los campos del formulario a objeto plano
   */
  function serialize(form) {
    const data = {};
    const elements = form.querySelectorAll("[name]");

    elements.forEach((el) => {
      const name = el.name;
      const type = el.type;

      if (type === "radio") {
        if (el.checked) data[name] = el.value;
      } else if (type === "checkbox") {
        data[name] = el.checked ? "1" : "0";
      } else {
        data[name] = (el.value || "").trim();
      }
    });

    return data;
  }

  /**
   * Muestra o esconde un preloader circular junto al botón
   */
  function toggleLoader(form, show = true) {
    const btn = form.querySelector(".acf-fecp-form__button");
    if (!btn) return;

    if (show) {
      btn.disabled = true;
      btn.classList.add("loading");

      if (!btn.querySelector(".fecp-loader")) {
        const loader = document.createElement("img");
        loader.className = "fecp-loader";
        loader.src =
          "https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif";
        loader.alt = "Loading...";
        loader.style.width = "20px";
        loader.style.height = "20px";
        loader.style.marginLeft = "10px";
        loader.style.verticalAlign = "middle";
        btn.appendChild(loader);
      }
    } else {
      btn.disabled = false;
      btn.classList.remove("loading");
      const loader = btn.querySelector(".fecp-loader");
      if (loader) loader.remove();
    }
  }

  /**
   * Envío principal via fetch con manejo de errores y debug
   */
  async function post(form, type, fields) {
    const base = window.FECP_KEYS?.rest_url || `${window.location.origin}/wp-json`;
    const url = base;

    const msgBox = form.querySelector(".acf-fecp-form__msg") || form.querySelector(".fecp-global-msg");
    msgBox.textContent = "";
    toggleLoader(form, true);

    try {
      fields.base_uri = FECP_KEYS.base_uri;
      fields.endpoint = FECP_KEYS.endpoint;

      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ form_type: type, fields: fields }),
      });

      console.log("📤 Sending to:", url);
      console.log("🧾 Fields:", fields);

      const data = await response.json();
      toggleLoader(form, false);

      if (data.ok) {
        msgBox.textContent = "✅ Sent successfully (Lead #" + (data.id || "") + ")";
        msgBox.style.color = "green";
        form.reset();
      } else {
        msgBox.textContent = "❌ " + (data.body || "Error sending form");
        msgBox.style.color = "red";
      }
    } catch (err) {
      toggleLoader(form, false);
      msgBox.textContent = "❌ Network error: " + err.message;
      msgBox.style.color = "red";
    }
  }

  /**
   * Escucha el evento submit globalmente
   */
  document.addEventListener(
    "submit",
    function (e) {
      const form = e.target;
      if (!form.matches(".acf-fecp-form")) return;

      // e.preventDefault();
      // e.stopPropagation();

      const wrap = form.closest(".fecp-form-wrap");
      if (!wrap) return;

      const type = wrap.getAttribute("data-form-type") || "contacto";
      const endpoint = wrap.getAttribute("data-endpoint") || "";

      let ok = true;
      if (type === "contacto" && window.FECP_VALIDATE_CONTACTO)
        ok = window.FECP_VALIDATE_CONTACTO(form, true);
      if (type === "cotizacion" && window.FECP_VALIDATE_COTIZACION)
        ok = window.FECP_VALIDATE_COTIZACION(form, true);

      if (!ok) {
        console.warn("🚫 Validation failed, request not sent.");
        return;
      }

      const fields = serialize(form);
      post(form, type, fields);
    },
    true
  );
})();

document.addEventListener("DOMContentLoaded", function () {
  const radios = document.querySelectorAll('input[name="travel_agent"]');
  const typeLead = document.getElementById('type_lead');

  radios.forEach(radio => {
    radio.addEventListener('change', () => {
      // Si selecciona "Yes", type_lead = 1; si "No", type_lead = 0
      typeLead.value = radio.value;
      console.log("Type lead actualizado:", typeLead.value);
    });
  });
});
