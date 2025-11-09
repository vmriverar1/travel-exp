(function () {
  document.addEventListener("DOMContentLoaded", () => {
    const swiperEl = document.querySelector(".vtc-department__swiper");
    if (!swiperEl) {
      console.warn("[VTC] No hay .vtc-department__swiper en el DOM.");
      return;
    }
    if (typeof Swiper === "undefined") {
      console.error("[VTC] Swiper no encontrado.");
      return;
    }

    // 🔹 Mapeo de departamentos a regiones SVG
    const regionMap = {
      lima: ["cls-6"], // Costa
      piura: ["cls-6"], // Costa
      trujillo: ["cls-6"], // Costa
      arequipa: ["cls-5"], // Sierra
      cusco: ["cls-5"], // Sierra
      iquitos: ["cls-3"], // Selva
      ucayali: ["cls-3"], // Selva
      loreto: ["cls-3"], // Selva
    };

    const swiper = new Swiper(swiperEl, {
      slidesPerView: 1,
      spaceBetween: 16,
      speed: 600,
      loop: false,
      navigation: {
        nextEl: ".vtc-dep-next",
        prevEl: ".vtc-dep-prev",
      },
      on: {
        init() {
          requestAnimationFrame(() => applyHighlight(swiper));
        },
        slideChange() {
          applyHighlight(swiper);
        },
      },
    });

    // 🔸 Click en puntos del mapa => mover slide correspondiente
    document.querySelectorAll(".vtc-map__point").forEach((point) => {
      point.addEventListener("click", () => {
        const dept = norm(point.dataset.department);
        const idx = [...swiper.slides].findIndex(
          (s) => norm(s.dataset.department) === dept
        );
        if (idx !== -1) swiper.slideTo(idx);
      });
    });

    // 🔸 Función principal
    function applyHighlight(sw) {
      const slide = sw.slides[sw.activeIndex];
      if (!slide) {
        console.warn("[VTC] No hay slide activo aún.");
        return;
      }

      const dept = norm(slide.dataset.department);
      if (!dept) {
        console.warn("[VTC] Slide activo sin data-department:", slide);
        return;
      }

      let found = false;

      // 🟠 Puntos del mapa (círculos)
      document.querySelectorAll(".vtc-map__point").forEach((p) => {
        const isMatch = norm(p.dataset.department) === dept;
        p.classList.toggle("is-active", isMatch);
        if (isMatch) found = true;
      });

      // 🟢 Control de regiones SVG
      const allRegions = [".cls-3", ".cls-5", ".cls-6"];
      const visibleRegions = regionMap[dept] || [];

      allRegions.forEach((cls) => {
        document.querySelectorAll(cls).forEach((el) => {
          const isVisible = visibleRegions.includes(cls.substring(1)); // quita el "."
          el.classList.toggle("is-visible", isVisible);
          el.classList.toggle("is-transparent", !isVisible);
        });
      });

      if (!found) {
        console.warn(`[VTC] No se encontró punto para "${dept}".`);
      } else {
        console.log(`[VTC] Activo -> ${dept}`);
      }
    }

    // 🔸 Normaliza texto
    function norm(v) {
      return (v || "").toString().trim().toLowerCase();
    }
  });
})();
