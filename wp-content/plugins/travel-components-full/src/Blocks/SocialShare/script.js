document.addEventListener('DOMContentLoaded', () => {
  const popupSelectors = [
    '.tc-ss-facebook',
    '.tc-ss-pinterest',
    '.tc-ss-linkedin'
  ];

  const popupLinks = document.querySelectorAll(popupSelectors.join(','));

  popupLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      const url = link.getAttribute('href');
      if (!url) return;

      const width = 600;
      const height = 500;
      const left = (screen.width / 2) - (width / 2);
      const top = (screen.height / 2) - (height / 2);

      window.open(
        url,
        'sharePopup',
        `width=${width},height=${height},top=${top},left=${left},resizable=yes,scrollbars=yes,status=no`
      );
    });
  });
});
