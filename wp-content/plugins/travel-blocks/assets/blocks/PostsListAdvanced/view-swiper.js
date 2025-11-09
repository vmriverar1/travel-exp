/**
 * Swiper solo mobile con skeleton visible desde el inicio
 */
document.addEventListener("DOMContentLoaded", () => {
  const blocks = document.querySelectorAll(".acf-gbr-pla[data-pla-enable-mobile='1']");
  if (!blocks.length || !window.PLA_FLAGS?.enableMobile) return;

  const isMobile = window.matchMedia("(max-width: 1023px)").matches;
  if (!isMobile) return;

  blocks.forEach(block => {
    const row = block.querySelector(".pla-row");
    if (!row || block.dataset.swiperInit === "true") return;

    block.dataset.swiperInit = "true";

    // === Placeholder visible desde el inicio ===
    const loader = document.createElement("div");
    loader.className = "pla-loader";
    loader.innerHTML = `<div class="pla-skeleton"></div>`;
    row.insertAdjacentElement("beforebegin", loader);
    row.style.display = "none"; // ocultar contenido real hasta que cargue

    // === Cargar Swiper dinámicamente ===
    const css = document.createElement("link");
    css.rel = "stylesheet";
    css.href = "https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css";
    document.head.appendChild(css);

    const script = document.createElement("script");
    script.src = "https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js";
    script.onload = () => {
      // Crear estructura Swiper
      const wrapper = document.createElement("div");
      wrapper.className = "swiper pla-swiper-mobile";
      const slides = document.createElement("div");
      slides.className = "swiper-wrapper";

      [...row.children].forEach(card => {
        const slide = document.createElement("div");
        slide.className = "swiper-slide";
        slide.appendChild(card);
        slides.appendChild(slide);
      });

      const pagination = document.createElement("div");
      pagination.className = "swiper-pagination";
      const prev = document.createElement("div");
      prev.className = "swiper-button-prev";
      const next = document.createElement("div");
      next.className = "swiper-button-next";

      wrapper.appendChild(slides);
      wrapper.appendChild(prev);
      wrapper.appendChild(next);
      wrapper.appendChild(pagination);

      row.innerHTML = "";
      row.appendChild(wrapper);

      // === Inicializar Swiper ===
      new Swiper(wrapper, {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: true,
        grabCursor: true,
        pagination: {
          el: pagination,
          clickable: true,
        },
        navigation: {
          nextEl: next,
          prevEl: prev,
        },
      });

      // === Fade-in del slider y fade-out del loader ===
      setTimeout(() => {
        loader.classList.add("is-hide");
        setTimeout(() => loader.remove(), 500);
        row.style.display = "block";
        row.classList.add("is-loaded");
      }, 400);
    };

    document.body.appendChild(script);
  });
});
