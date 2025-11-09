(function () {
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tc-subscribe-form').forEach(function (form) {

      form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const msg = form.parentNode.querySelector('.tc-subscribe-msg');
        msg.style.display = 'none';
        msg.textContent = '';

        // 🔹 Mostrar preloader temporal
        let loader = document.createElement('span');
        loader.className = 'tc-loader';
        loader.style.cssText = 'margin-left:8px;width:16px;height:16px;border:2px solid #ccc;border-top-color:#333;border-radius:50%;display:inline-block;animation:spin 1s linear infinite;';
        form.querySelector('button[type="submit"]').after(loader);

        // 🔹 Preparar datos
        const data = new FormData(form);
        data.append('recipients', form.getAttribute('data-recipients') || '');

        // 🔹 Ruta REST (segura incluso en localhost)
        const base = (window.TC_SubscribeForm?.restUrl) || `${window.location.origin}/wp-json/acf-blocks/v1`;
        const rest = `${base}/subscribe`;

        try {
          const response = await fetch(rest, {
            method: 'POST',
            headers: {
              'X-WP-Nonce': window.TC_SubscribeForm?.nonce || '',
            },
            body: data
          });

          if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
          }

          const json = await response.json();
          msg.textContent = json.message || (json.success ? 'Subscribed!' : 'Error');
          msg.style.color = json.success ? 'green' : 'red';
          msg.style.display = 'block';

          if (json.success) form.reset();

        } catch (err) {
          console.error('❌ Subscribe error:', err);
          msg.textContent = err.message.includes('HTTP') 
            ? `Server error (${err.message})` 
            : 'Network error';
          msg.style.color = 'red';
          msg.style.display = 'block';

        } finally {
          loader.remove();
        }
      });
    });
  });

  // 🔹 Simple animación CSS inline
  const style = document.createElement('style');
  style.textContent = `
    @keyframes spin { 
      from { transform: rotate(0deg); } 
      to { transform: rotate(360deg); } 
    }
  `;
  document.head.appendChild(style);
})();
