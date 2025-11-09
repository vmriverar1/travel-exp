(function(){
  function isLetters(v){ return /^[a-zA-Z\s]+$/.test(v); }
  function isPhone(v){ return /^\+?\d{7,15}$/.test(v); }
  function showError(el, msg){ clearError(el); el.classList.add('error'); var s=document.createElement('span'); s.className='fecp-error'; s.textContent=msg; el.parentNode.appendChild(s); }
  function clearError(el){ el.classList.remove('error'); var s=el.parentNode.querySelector('.fecp-error'); if(s)s.remove(); }
  function markValid(el){ el.classList.remove('error'); el.classList.add('valid'); var s=el.parentNode.querySelector('.fecp-error'); if(s)s.remove(); }
  function validate(form, show){
    var ok=true, name=form.querySelector('[name="nombre"]'), phone=form.querySelector('[name="phone"]'), model=form.querySelector('[name="modelo"]'), ver=form.querySelector('[name="version"]');
    if (!name.value.trim() || !isLetters(name.value.trim())) { if(show) showError(name,'Only letters allowed'); ok=false; } else { markValid(name); }
    if (phone && phone.value.trim() && !isPhone(phone.value.trim())) { if(show) showError(phone,'Invalid phone number'); ok=false; } else if (phone && !phone.value.trim()) { clearError(phone); phone.classList.remove('valid'); } else if (phone) { markValid(phone); }
    if (!model.value.trim()) { if(show) showError(model,'Model is required'); ok=false; } else { markValid(model); }
    if (!ver.value.trim()) { if(show) showError(ver,'Version is required'); ok=false; } else { markValid(ver); }
    return ok;
  }
  window.FECP_VALIDATE_COTIZACION = function(form, show){ return validate(form, show); };
  document.addEventListener('input', function(e){ var form=e.target.closest('.acf-fecp-form'); if(form) validate(form, true); });
  document.addEventListener('blur',  function(e){ var form=e.target.closest && e.target.closest('.acf-fecp-form'); if(form) validate(form, true); }, true);
})();