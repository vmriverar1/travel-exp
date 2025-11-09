document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.tc-dg-btn');
  if (!btn) return;

  // 1️⃣ Buscar el contenedor principal Gutenberg
  const root = document.querySelector('.entry-content.wp-block-post-content');
  if (!root) {
    alert('No se encontró el contenedor principal (.entry-content.wp-block-post-content)');
    console.warn('⚠️ [TC DownloadableGuide] Ningún contenedor Gutenberg encontrado.');
    return;
  }

  console.log('🟢 [TC DownloadableGuide] Contenedor Gutenberg encontrado:', root);

  // 2️⃣ Extraer manualmente los elementos relevantes
  const elements = root.querySelectorAll('h1,h2,h3,h4,h5,h6,p,img');
  console.log('📊 [TC DownloadableGuide] Elementos encontrados:', elements.length);

  if (!elements.length) {
    alert('No se encontró contenido relevante (títulos, párrafos o imágenes).');
    console.warn('⚠️ [TC DownloadableGuide] Ningún h1–h6, p o img encontrado.');
    return;
  }

  // 3️⃣ Construir HTML limpio solo con esos elementos
  let content = '';
  elements.forEach(el => {
    content += el.outerHTML + '\n';
  });

  // 4️⃣ Asegurar que las rutas de imagen sean absolutas
  content = content.replace(/src="\//g, `src="${window.location.origin}/`);

  console.log('🧩 [TC DownloadableGuide] HTML final a enviar (primeros 400 chars):', content.substring(0, 400));

  // 5️⃣ Enviar al endpoint REST
  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = 'Generando PDF...';

  try {
    console.log('🚀 [TC DownloadableGuide] Enviando al endpoint:', TC_DownloadableGuide.restUrl);

    const res = await fetch(TC_DownloadableGuide.restUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': TC_DownloadableGuide.nonce
      },
      body: JSON.stringify({ html: content })
    });

    console.log('📡 [TC DownloadableGuide] Respuesta del servidor:', res);

    if (!res.ok) throw new Error('Error al generar el PDF');

    const blob = await res.blob();
    const url = URL.createObjectURL(blob);
    console.log('✅ [TC DownloadableGuide] PDF generado con éxito. Tamaño:', blob.size, 'bytes');

    const a = document.createElement('a');
    a.href = url;
    a.download = 'travel-guide.pdf';
    a.click();
    URL.revokeObjectURL(url);

  } catch (err) {
    console.error('❌ [TC DownloadableGuide] Error:', err);
    alert('No se pudo generar el PDF: ' + err.message);
  } finally {
    btn.disabled = false;
    btn.textContent = originalText;
  }
});
