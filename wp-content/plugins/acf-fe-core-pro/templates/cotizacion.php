<form class="acf-fecp-form" novalidate autocomplete="off">
  <div style="display:flex; gap:12px;">
    <label style="flex:1">Name<br><input type="text" name="nombre"></label>
    <label style="flex:1">Phone (optional)<br><input type="text" name="phone" placeholder="+51987654321"></label>
  </div>
  <div style="display:flex; gap:12px; margin-top:8px;">
    <label style="flex:1">Model<br><input type="text" name="modelo"></label>
    <label style="flex:1">Version<br>
      <select name="version"><option value="">-- Select --</option><option>Classic</option><option>Premium</option></select>
    </label>
  </div>
  <p style="margin-top:8px;"><button type="submit" class="button button-primary">Send</button></p>
  <div class="fecp-global-msg" style="margin-top:6px;"></div>
</form>
