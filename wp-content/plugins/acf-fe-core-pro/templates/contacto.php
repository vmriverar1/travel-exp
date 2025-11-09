<?php
$endpoint  = get_field('endpoint') ?: '';
$base_uri  = get_field('base_uri') ?: '';
?>

<form class="acf-fecp-form" id="acf-fecp-form" novalidate autocomplete="off">
  <input type="hidden" name="endpoint" value="<?php echo esc_attr($endpoint); ?>">
  <input type="hidden" name="package_link" value="<?php echo esc_url((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">

  <!-- Grupo 1 -->
  <div class="acf-fecp-form__group">
    <div class="acf-fecp-form__field">
      <input type="text" name="first_name" placeholder="First Name" class="acf-fecp-form__input">
    </div>
    <div class="acf-fecp-form__field">
      <input type="text" name="last_name" placeholder="Last Name" class="acf-fecp-form__input">
    </div>
    <div class="acf-fecp-form__field">
      <input type="email" name="email" placeholder="E-mail Address" class="acf-fecp-form__input">
    </div>
  </div>

  <!-- Grupo 2 -->
  <div class="acf-fecp-form__group">
    <div class="acf-fecp-form__field">
      <input type="text" name="phone" placeholder="Cellphone/Telephone" class="acf-fecp-form__input">
    </div>
    <div class="acf-fecp-form__field">
      <select name="country_code" class="acf-fecp-form__select">
        <option value="US">United States</option>
        <option value="AF">Afghanistan</option>
        <option value="AX">Aland Islands</option>
        <option value="AL">Albania</option>
        <option value="DZ">Algeria</option>
        <option value="AS">American Samoa</option>
        <option value="AD">Andorra</option>
        <option value="AO">Angola</option>
        <option value="AI">Anguilla</option>
        <option value="AQ">Antarctica</option>
        <option value="AG">Antigua and Barbuda</option>
        <option value="AR">Argentina</option>
        <option value="AM">Armenia</option>
        <option value="AW">Aruba</option>
        <option value="AU">Australia</option>
        <option value="AT">Austria</option>
        <option value="AZ">Azerbaijan</option>
        <option value="BS">The Bahamas</option>
        <option value="BH">Bahrain</option>
        <option value="BD">Bangladesh</option>
        <option value="BB">Barbados</option>
        <option value="BY">Belarus</option>
        <option value="BE">Belgium</option>
        <option value="BZ">Belize</option>
        <option value="BJ">Benin</option>
        <option value="BM">Bermuda</option>
        <option value="BT">Bhutan</option>
        <option value="BO">Bolivia</option>
        <option value="BA">Bosnia and Herzegovina</option>
        <option value="BW">Botswana</option>
        <option value="BV">Bouvet Island</option>
        <option value="BR">Brazil</option>
        <option value="IO">British Indian Ocean Territory</option>
        <option value="BN">Brunei</option>
        <option value="BG">Bulgaria</option>
        <option value="BF">Burkina Faso</option>
        <option value="BI">Burundi</option>
        <option value="KH">Cambodia</option>
        <option value="CM">Cameroon</option>
        <option value="CA">Canada</option>
        <option value="CV">Cape Verde</option>
        <option value="KY">Cayman Islands</option>
        <option value="CF">Central African Republic</option>
        <option value="TD">Chad</option>
        <option value="CL">Chile</option>
        <option value="CN">China</option>
        <option value="CX">Christmas Island</option>
        <option value="CC">Cocos (Keeling) Islands</option>
        <option value="CO">Colombia</option>
        <option value="KM">Comoros</option>
        <option value="CG">Congo</option>
        <option value="CD">Democratic Republic of the Congo</option>
        <option value="CK">Cook Islands</option>
        <option value="CR">Costa Rica</option>
        <option value="CI">Cote D'Ivoire (Ivory Coast)</option>
        <option value="HR">Croatia</option>
        <option value="CU">Cuba</option>
        <option value="CY">Cyprus</option>
        <option value="CZ">Czech Republic</option>
        <option value="DK">Denmark</option>
        <option value="DJ">Djibouti</option>
        <option value="DM">Dominica</option>
        <option value="DO">Dominican Republic</option>
        <option value="TL">Timor-Leste</option>
        <option value="EC">Ecuador</option>
        <option value="EG">Egypt</option>
        <option value="SV">El Salvador</option>
        <option value="GQ">Equatorial Guinea</option>
        <option value="ER">Eritrea</option>
        <option value="EE">Estonia</option>
        <option value="ET">Ethiopia</option>
        <option value="FK">Falkland Islands</option>
        <option value="FO">Faroe Islands</option>
        <option value="FJ">Fiji Islands</option>
        <option value="FI">Finland</option>
        <option value="FR">France</option>
        <option value="GF">French Guiana</option>
        <option value="PF">French Polynesia</option>
        <option value="TF">French Southern Territories</option>
        <option value="GA">Gabon</option>
        <option value="GM">The Gambia </option>
        <option value="GE">Georgia</option>
        <option value="DE">Germany</option>
        <option value="GH">Ghana</option>
        <option value="GI">Gibraltar</option>
        <option value="GR">Greece</option>
        <option value="GL">Greenland</option>
        <option value="GD">Grenada</option>
        <option value="GP">Guadeloupe</option>
        <option value="GU">Guam</option>
        <option value="GT">Guatemala</option>
        <option value="GG">Guernsey</option>
        <option value="GN">Guinea</option>
        <option value="GW">Guinea-Bissau</option>
        <option value="GY">Guyana</option>
        <option value="HT">Haiti</option>
        <option value="HM">Heard Island and McDonald Islands</option>
        <option value="HN">Honduras</option>
        <option value="HK">Hong Kong S.A.R.</option>
        <option value="HU">Hungary</option>
        <option value="IS">Iceland</option>
        <option value="IN">India</option>
        <option value="ID">Indonesia</option>
        <option value="IR">Iran</option>
        <option value="IQ">Iraq</option>
        <option value="IE">Ireland</option>
        <option value="IL">Israel</option>
        <option value="IT">Italy</option>
        <option value="JM">Jamaica</option>
        <option value="JP">Japan</option>
        <option value="JE">Jersey</option>
        <option value="JO">Jordan</option>
        <option value="KZ">Kazakhstan</option>
        <option value="KE">Kenya</option>
        <option value="KI">Kiribati</option>
        <option value="KP">North Korea</option>
        <option value="KR">South Korea</option>
        <option value="KW">Kuwait</option>
        <option value="KG">Kyrgyzstan</option>
        <option value="LA">Laos</option>
        <option value="LV">Latvia</option>
        <option value="LB">Lebanon</option>
        <option value="LS">Lesotho</option>
        <option value="LR">Liberia</option>
        <option value="LY">Libya</option>
        <option value="LI">Liechtenstein</option>
        <option value="LT">Lithuania</option>
        <option value="LU">Luxembourg</option>
        <option value="MO">Macau S.A.R.</option>
        <option value="MK">North Macedonia</option>
        <option value="MG">Madagascar</option>
        <option value="MW">Malawi</option>
        <option value="MY">Malaysia</option>
        <option value="MV">Maldives</option>
        <option value="ML">Mali</option>
        <option value="MT">Malta</option>
        <option value="IM">Man (Isle of)</option>
        <option value="MH">Marshall Islands</option>
        <option value="MQ">Martinique</option>
        <option value="MR">Mauritania</option>
        <option value="MU">Mauritius</option>
        <option value="YT">Mayotte</option>
        <option value="MX">Mexico</option>
        <option value="FM">Micronesia</option>
        <option value="MD">Moldova</option>
        <option value="MC">Monaco</option>
        <option value="MN">Mongolia</option>
        <option value="ME">Montenegro</option>
        <option value="MS">Montserrat</option>
        <option value="MA">Morocco</option>
        <option value="MZ">Mozambique</option>
        <option value="MM">Myanmar</option>
        <option value="NA">Namibia</option>
        <option value="NR">Nauru</option>
        <option value="NP">Nepal</option>
        <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
        <option value="NL">Netherlands</option>
        <option value="NC">New Caledonia</option>
        <option value="NZ">New Zealand</option>
        <option value="NI">Nicaragua</option>
        <option value="NE">Niger</option>
        <option value="NG">Nigeria</option>
        <option value="NU">Niue</option>
        <option value="NF">Norfolk Island</option>
        <option value="MP">Northern Mariana Islands</option>
        <option value="NO">Norway</option>
        <option value="OM">Oman</option>
        <option value="PK">Pakistan</option>
        <option value="PW">Palau</option>
        <option value="PS">Palestinian Territory Occupied</option>
        <option value="PA">Panama</option>
        <option value="PG">Papua New Guinea</option>
        <option value="PY">Paraguay</option>
        <option value="PE">Peru</option>
        <option value="PH">Philippines</option>
        <option value="PN">Pitcairn Island</option>
        <option value="PL">Poland</option>
        <option value="PT">Portugal</option>
        <option value="PR">Puerto Rico</option>
        <option value="QA">Qatar</option>
        <option value="RE">Reunion</option>
        <option value="RO">Romania</option>
        <option value="RU">Russia</option>
        <option value="RW">Rwanda</option>
        <option value="SH">Saint Helena</option>
        <option value="KN">Saint Kitts and Nevis</option>
        <option value="LC">Saint Lucia</option>
        <option value="PM">Saint Pierre and Miquelon</option>
        <option value="VC">Saint Vincent and the Grenadines</option>
        <option value="BL">Saint-Barthelemy</option>
        <option value="MF">Saint-Martin (French part)</option>
        <option value="WS">Samoa</option>
        <option value="SM">San Marino</option>
        <option value="ST">Sao Tome and Principe</option>
        <option value="SA">Saudi Arabia</option>
        <option value="SN">Senegal</option>
        <option value="RS">Serbia</option>
        <option value="SC">Seychelles</option>
        <option value="SL">Sierra Leone</option>
        <option value="SG">Singapore</option>
        <option value="SK">Slovakia</option>
        <option value="SI">Slovenia</option>
        <option value="SB">Solomon Islands</option>
        <option value="SO">Somalia</option>
        <option value="ZA">South Africa</option>
        <option value="GS">South Georgia</option>
        <option value="SS">South Sudan</option>
        <option value="ES">Spain</option>
        <option value="LK">Sri Lanka</option>
        <option value="SD">Sudan</option>
        <option value="SR">Suriname</option>
        <option value="SJ">Svalbard and Jan Mayen Islands</option>
        <option value="SZ">Eswatini</option>
        <option value="SE">Sweden</option>
        <option value="CH">Switzerland</option>
        <option value="SY">Syria</option>
        <option value="TW">Taiwan</option>
        <option value="TJ">Tajikistan</option>
        <option value="TZ">Tanzania</option>
        <option value="TH">Thailand</option>
        <option value="TG">Togo</option>
        <option value="TK">Tokelau</option>
        <option value="TO">Tonga</option>
        <option value="TT">Trinidad and Tobago</option>
        <option value="TN">Tunisia</option>
        <option value="TR">Turkey</option>
        <option value="TM">Turkmenistan</option>
        <option value="TC">Turks and Caicos Islands</option>
        <option value="TV">Tuvalu</option>
        <option value="UG">Uganda</option>
        <option value="UA">Ukraine</option>
        <option value="AE">United Arab Emirates</option>
        <option value="GB">United Kingdom</option>
        <option value="US">United States</option>
        <option value="UM">United States Minor Outlying Islands</option>
        <option value="UY">Uruguay</option>
        <option value="UZ">Uzbekistan</option>
        <option value="VU">Vanuatu</option>
        <option value="VA">Vatican City State (Holy See)</option>
        <option value="VE">Venezuela</option>
        <option value="VN">Vietnam</option>
        <option value="VG">Virgin Islands (British)</option>
        <option value="VI">Virgin Islands (US)</option>
        <option value="WF">Wallis and Futuna Islands</option>
        <option value="EH">Western Sahara</option>
        <option value="YE">Yemen</option>
        <option value="ZM">Zambia</option>
        <option value="ZW">Zimbabwe</option>
        <option value="XK">Kosovo</option>
        <option value="CW">Curaçao</option>
        <option value="SX">Sint Maarten (Dutch part)</option>
      </select>
    </div>
    <div class="acf-fecp-form__field">
      <select name="holiday_type" class="acf-fecp-form__select">
        <option value="" disabled selected>Holiday Type</option>
        <option value="2">Adventure</option>
        <option value="6">Cultural</option>
        <option value="5">Gastronomy</option>
        <option value="4">Wellness</option>
        <option value="3">Local living</option>
        <option value="8">Luxury</option>
      </select>
    </div>
  </div>

  <!-- Grupo 3 -->
  <div class="acf-fecp-form__group">
    <div class="acf-fecp-form__field">
      <select name="destination_interes" class="acf-fecp-form__select">
        <option value="" disabled selected>Destination of interest</option>
        <option value="4">Cusco</option>
        <option value="7">Lima</option>
        <option value="5">Ica</option>
        <option value="1">Arequipa</option>
        <option value="10">Puno</option>
        <option value="2">Cajamarca</option>
        <option value="3">Chiclayo</option>
        <option value="6">Iquitos</option>
        <option value="11">Madre de Dios</option>
        <option value="13">Chachapoyas</option>
        <option value="14">Multidestination</option>
        <option value="15">La Paz</option>
        <option value="16">Quito</option>
      </select>
    </div>
    <div class="acf-fecp-form__field">
      <select id="package" name="package" class="acf-fecp-form__select" data-gtm-form-interact-field-id="1">
        <option value="" disabled="" selected="">Select a Package (optional)</option>
        <option value="125">Classic Inca Trail</option>
        <option value="134">Salkantay Trek to Machu Picchu</option>
        <option value="234">Machu Picchu Special</option>
        <option value="152">Salkantay Trek & Machu Picchu</option>
        <option value="243">Luxury Inca Trail - 4 Day</option>
      </select>
    </div>

    <div class="acf-fecp-form__field acf-fecp-form__radio-group">
      <div><span class="acf-fecp-form__label">Are you a travel agent?</span></div>
      <div class="acf-fecp-form__radio-group__content">
        <label id="travel_agent" class="acf-fecp-form__radio"><input type="radio" name="travel_agent" value="1"> Yes</label>
        <label id="travel_agent" class="acf-fecp-form__radio"><input type="radio" name="travel_agent" value="0" checked> No</label>
      </div>
    </div>
    <input type="hidden" name="type_lead" id="type_lead" value="0">
  </div>

  <div class="acf-fecp-form__group acf-fecp-form__company" style="display:none;">
    <div class="acf-fecp-form__field">
      <input type="text" name="company" placeholder="Company Name" class="acf-fecp-form__input">
    </div>
  </div>

  <!-- Mensaje -->
  <div class="acf-fecp-form__group">
    <div class="acf-fecp-form__field">
      <textarea name="description" placeholder="Your question" class="acf-fecp-form__textarea"></textarea>
    </div>
  </div>

  <!-- Política -->
  <div class="acf-fecp-form__checkbox-group">
    <label>
      <input type="checkbox" name="privacy" checked required>
      <span>I have read and accept the <a href="#" target="_blank">privacy policy</a></span>
    </label>
  </div>

  <button type="submit" class="acf-fecp-form__button">Submit</button>
  <div class="acf-fecp-form__msg"></div>
</form>

<script type="text/javascript">
  var __ss_noform = __ss_noform || [];
  __ss_noform.push(['baseURI', '<?php echo esc_attr($base_uri); ?>']);
  __ss_noform.push(['form', 'acf-fecp-form', '<?php echo esc_attr($endpoint); ?>']);
</script>
<script type="text/javascript" src="https://koi-3SL2TCGX3O.marketingautomation.services/client/noform.js?ver=1.24"></script>