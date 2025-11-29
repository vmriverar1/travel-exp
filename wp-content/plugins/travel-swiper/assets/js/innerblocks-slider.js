document.addEventListener("DOMContentLoaded", function () {

    const sliders = document.querySelectorAll('.tsb-innerblocks-slider');

    sliders.forEach(block => {

        // Evitar doble inicialización
        if (block.classList.contains('swiper-initialized')) return;

        // Detectar si debe volverse slider (mobile y tablet)
        // Mobile = <=768, Tablet = 769-1023
        const width = window.innerWidth;
        const isMobileOrTablet = width <= 1023;

        if (!isMobileOrTablet) return; // Desktop NO es slider

        // Obtener hijos directos del bloque
        const children = Array.from(block.children).filter(el => {
            return !el.classList.contains('swiper-wrapper') &&
                   !el.classList.contains('swiper-pagination');
        });

        if (children.length <= 1) return;

        // Convertir contenedor a Swiper
        block.classList.add('swiper', 'swiper-container', 'swiper-initialized');

        const swiperWrapper = document.createElement('div');
        swiperWrapper.classList.add('swiper-wrapper');

        children.forEach(child => {
            const slide = document.createElement('div');
            slide.classList.add('swiper-slide');
            slide.appendChild(child);
            swiperWrapper.appendChild(slide);
        });

        block.innerHTML = '';
        block.appendChild(swiperWrapper);

        const pagination = document.createElement('div');
        pagination.classList.add('swiper-pagination');
        block.appendChild(pagination);

        // Inicializar Swiper
        new Swiper(block, {
            slidesPerView: 1,           // Mobile
            spaceBetween: 20,
            pagination: {
                el: pagination,
                clickable: true
            },

            breakpoints: {
                769: {
                    slidesPerView: 3,   // Tablet
                    spaceBetween: 22
                }
            }
        });
    });
});
