<?php
	/*
	  Plugin Name: Popside
	  Version: 1.0
	  Description: Automatically adds Popside.co tracker to site
	  Author: Popside
	  Author URI: https://popside.co/
	  License: GPL v2 or later
      License URI: https://www.gnu.org/licenses/gpl-2.0.html
	  */
    if ( ! defined( 'ABSPATH' ) ) exit;
	/* Version check */
	global $wp_version;

	$exit_msg = ' 
  Popside requires WordPress 4.7 or newer. 
  <a href="http://codex.wordpress.org/Upgrading_WordPress"> 
  Please update!</a>';

	if ( version_compare( $wp_version, "4.7", "<" ) ) {
		exit( esc_html($exit_msg) );
	}

	class PopsideSettingsPage {
		/**
		 * Holds the values to be used in the fields callbacks
		 */
		private $options;

		/**
		 * Start up
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'popside_add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'popside_page_init' ) );
		}

		/**
		 * Add options page
		 */
		public function popside_add_plugin_page() {
			// This page will be under "Settings"
			add_options_page(
				'Popside Settings',
				'Popside',
				'manage_options',
				'popside-setting-admin',
				array( $this, 'popside_create_admin_page' )
			);
		}

		/**
		 * Options page callback
		 */
		public function popside_create_admin_page() {

			// Set class property
			$this->options = get_option( 'popside_options' );
			?>

            <style>
                input.valid, input.valid:focus{
                    border-color: #0cd046;
                    box-shadow: 0 0 0 1px #0cd046 !important;
                    background: #0cd04682 !important;
                }
                input.notvalid, input.notvalid:focus{
                    border-color: #e15858 !important;
                    box-shadow: 0 0 0 1px #e15858 !important;
                }
                input.unset, input.unset:focus{
                    border-color: #5c68ff !important;
                    box-shadow: 0 0 0 1px #5c68ff !important;
                }
            </style>

            <div class="wrap">

                <div id="total"></div>
                <!--<h1>Popside Settings</h1>-->
				<?php if ( is_plugin_active( 'wp-super-cache/wp-cache.php' ) || is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) || is_plugin_active( 'wp-fastest-cache/wpFastestCache.php' ) ) { ?>
                    <div style="border: 2px solid #ff1212;border-radius: 10px;padding: 10px; background: #f7ff88;float: left;">
                        <span style="font-weight: bold;height: 20px;font-size: 1.5em;vertical-align: sub;margin-right: 5px;">⚠</span>️<b>Uwaga!</b>
                        Wyglda na to, że posiadasz plugin do obsługi cache.
                        Pamiętaj aby po zapisaniu kodu <b>wyczyścić cache strony</b> w ustawieniach pluginu.
                    </div>
				<?php } ?>

                <form style="float:left;display: block;clear: both;" method="post" action="options.php">
					<?php
						// This prints out all hidden setting fields
						settings_fields( 'popside_options_group' );
						do_settings_sections( 'popside-setting-admin' );
						submit_button();
					?>
                </form>
            </div>

            <h2 class="clear">Shortcode</h2>
            <ul>
                <li><code><b>[popside-company]</b></code> - do użycia w edytorze wizualnym.</li>

                <li>
                    <code><b><?php esc_html_e( "<?php echo do_shortcode('[popside-company]'); ?>" ); ?></b></code>
                    - do użycia w kodzie szablonu strony.
                </li>
            </ul>
            <hr>
            <h3 class="clear">Parametry do użycia w shortcode</h3>
            <div class="clear">
                <ul>
                    <li><b>name</b> - Treść jaka pojawi się jeśli nie zostawnie wykryta firma <i>[Domyślnie = puste]</i>
                    </li>
                    <li><b>lowernext</b> - Wartość ustawiona na "1" = następna litera po nazwie firmy zmieniana jest na
                        małą <i>[Domyślnie = "0"]</i></li>
                    <li><b>maxlength</b> - Maksymalna długość nazwy firmy, jeśli nazwa przekroczy zadaną wartość
                        dodawane jest "..." <i>[Domyślnie = "9999"]</i></li>
                    <li><b>color</b> - Color fonta wykrytej firmy <i>[Domyślnie = "inherit"]</i></li>
                    <li><b>prefix</b> - Tekst jaki pojawi się przed wykrytą firmą <i>[Domyślnie = puste]</i></li>
                    <li><b>removestatut</b> - Wartość ustawiona na "1" = usuwa statut firm np. "Sp. z. o.o.", "S.A."
                        itp. <i>[Domyślnie = "0"]</i></li>
                </ul>
            </div>
            <hr>
            <h3>Przykład użycia:</h3>
            <div><code>[popside-company"] Świetnie dziś wyglądasz! Miłego dnia.</code></div>
            <ul>
                <li><b>Rozpoznana Firma:</b> Nazwa-Firmy Sp. z o.o. Świetnie dziś wyglądasz! Miłego dnia.</li>
                <li><b>Nierozpoznana Firma:</b> Świetnie dziś wyglądasz! Miłego dnia.</li>
            </ul>

            <div><code>[popside-company name ="Czytelniku"] Świetnie dziś wyglądasz! Miłego dnia.</code></div>
            <ul>
                <li><b>Rozpoznana Firma:</b> Nazwa-Firmy Sp. z o.o. Świetnie dziś wyglądasz! Miłego dnia.</li>
                <li><b>Nierozpoznana Firma:</b> Czytelniku Świetnie dziś wyglądasz! Miłego dnia.</li>
            </ul>

            <div><code>[popside-company lowernext="1"] Świetnie dziś wyglądasz! Miłego dnia.</code></div>
            <ul>
                <li><b>Rozpoznana Firma:</b> Nazwa-Firmy Sp. z o.o. świetnie dziś wyglądasz! Miłego dnia.</li>
            </ul>

            <div><code>[popside-company maxlength ="8"] Świetnie dziś wyglądasz! Miłego dnia.</code></div>
            <ul>
                <li><b>Rozpoznana Firma:</b> Nazwa-Fi... Świetnie dziś wyglądasz! Miłego dnia.</li>
            </ul>

            <div><code>[popside-company color="#00F"] Świetnie dziś wyglądasz! Miłego dnia.</code></div>
            <ul>
                <li><b>Rozpoznana Firma:</b> <span style="color:#00F">Nazwa-Firmy Sp. z o.o.</span> Świetnie dziś
                    wyglądasz! Miłego dnia.
                </li>
            </ul>

            <div><code>[popside-company prefix ="Przyjacielu z"] Świetnie dziś wyglądasz! Miłego dnia.</code></div>
            <ul>
                <li><b>Rozpoznana Firma:</b> Przyjacielu z Nazwa-Firmy Sp. z o.o. Świetnie dziś wyglądasz! Miłego dnia.
                </li>
            </ul>

            <div><code>[popside-company suffix =","] Świetnie dziś wyglądasz! Miłego dnia.</code></div>
            <ul>
                <li><b>Rozpoznana Firma:</b> Nazwa-Firmy Sp. z o.o., Świetnie dziś wyglądasz! Miłego dnia.
                </li>
            </ul>

            <div><code>[popside-company removestatut ="1"] Świetnie dziś wyglądasz! Miłego dnia.</code></div>
            <ul>
                <li><b>Rozpoznana Firma:</b> Nazwa-Firmy Świetnie dziś wyglądasz! Miłego dnia.</li>
            </ul>
            <hr>
            <h4>Parametry można łączyć ze sobą np.:</h4>
            <div><code>[popside-company lowernext="1" name="Przyjacielu" prefix="Pracowniku" suffix ="," color="#73b20b"
                    removestatut="1"] Świetnie dziś wyglądasz! Miłego dnia.</code></div>

            <h4>W przypadku wykrycia firmy (np. "Nazwa-Firmy Sp. z o.o.") otrzymamy:</h4>
            <i>Pracowniku <b style="color:#73b20b">Nazwa-Firmy</b>, świetnie dziś wyglądasz! Miłego dnia.</i>
            <script>
                function updateValue() {
                    var iv = document.getElementById("ind-valid");
                    var inv = document.getElementById("ind-nvalid");
                    var input = document.getElementById("popside_tracker");
                    var total = document.getElementById("popside_tracker").value;
                    total = total.replace(/\s/g, '');

                    if ((total.length === 0)){
                        iv.classList.add("hidden");
                        inv.classList.add("hidden");
                        input.classList.remove("valid");
                        input.classList.remove("notvalid");
                        input.classList.add("unset");
                    }
                    else if (total.length === 9) {
                        input.classList.remove("unset");
                        input.classList.add("valid");
                        input.classList.remove("notvalid");
                        iv.classList.remove("hidden");
                        inv.classList.add("hidden");
                    } else {
                        input.classList.remove("unset");
                        input.classList.add("notvalid");
                        input.classList.remove("valid");
                        inv.classList.remove("hidden");
                        iv.classList.add("hidden");
                    }
                }
                // Register event handlers.
                updateValue();
                var inputelem = document.getElementById("popside_tracker");
                inputelem.addEventListener('keypress', updateValue);
                inputelem.addEventListener('keyup', updateValue);
                inputelem.addEventListener('input', updateValue);
                inputelem.addEventListener('change', updateValue);
            </script>
			<?php

		}

		/**
		 * Register and add settings
		 */
		public function popside_page_init() {
			register_setting(
				'popside_options_group', // Option group
				'popside_options' // Option name
			);

			add_settings_section(
				'setting_section_id', // ID
				'Popside Tracker', // Title
				array( $this, 'popside_print_section_info' ), // Callback
				'popside-setting-admin' // Page
			);

			add_settings_field(
				'popside_tracker', // ID
				'Popside Tracking Code', // Title
				array( $this, 'popside_id_number_callback' ), // Callback
				'popside-setting-admin', // Page
				'setting_section_id' // Section
			);
		}

		/**
		 * Print the Section text
		 */
		public function popside_print_section_info() {
			print 'Wprowadź kod Popside poniżej:';
		}

		/**
		 * Get the settings option array and print one of its values
		 */
		public function popside_id_number_callback() {
			printf(
				'BI-<input type="text" id="popside_tracker" name="popside_options[popside_tracker]" value="%s" />',
				isset( $this->options['popside_tracker'] ) ? esc_attr( $this->options['popside_tracker'] ) : ''
			);
			$popside_options = get_option( 'popside_options' );
			$popside_tracker = $popside_options['popside_tracker'];
/*			if ( popside_is_valid( $popside_tracker ) ) {
				printf( '<span class="indicator-valid" style="margin-left: 2px;border: 1px solid green;border-radius: 25px;padding: 0px 4px;background: green;color: #fff;">&#x2713;</span>' );
			} else {
				printf( '<span class="indicator-notvalid" style="margin-left: 2px;border: 1px solid red;border-radius: 25px;padding: 0px 5px;background: red;color: #fff;">X</span>' );
			}*/
			printf( '<span id="ind-valid" class="indicator-valid hidden" style="margin-left: 2px;border: 1px solid green;border-radius: 25px;padding: 0px 4px;background: green;color: #fff;">&#x2713;</span>');
            printf( '<span id="ind-nvalid" class="indicator-notvalid hidden" style="margin-left: 2px;border: 1px solid red;border-radius: 25px;padding: 0px 5px;background: red;color: #fff;">X</span>');
		}
	}

	function popside_is_valid( $popside_tracker ) {
		// scenario 1: empty
		if ( empty( $popside_tracker ) ) {
			return false;
		}

		// scenario 2: incorrect format
		if ( ! preg_match( '/^\d{9}$/', $popside_tracker ) ) {
			return false;
		}

		// passed successfully
		return true;
	}


	// Add scripts to wp_head()
	function popside_header_script() {
		$popside_options = get_option( 'popside_options' );
		$popside_tracker = $popside_options['popside_tracker'];
		if ( is_array( $popside_options ) && array_key_exists( 'popside_tracker', $popside_options ) && ( $popside_options['popside_tracker'] != '' ) ) {
			if ( popside_is_valid( $popside_tracker ) ) {
				?>
                <!-- Popside Tracker -->
                <script>
                    var _popsideid = 'BI-<?php echo esc_html($popside_options['popside_tracker']); ?>';
                    (function (d, o, u) {
                        a = d.createElement(o),
                            m = d.getElementsByTagName(o)[0];
                        a.async = 1;
                        a.src = u;
                        m.parentNode.insertBefore(a, m);
                    })(document, 'script', '//c.popside.co/t.min.js');
                </script>
                <!-- END Popside Tracker v.1.4 -->
			<?php }
		}
	}

	if ( is_admin() ) {
		$popside_settings_page = new PopsideSettingsPage();
	}

    if (!function_exists('popside_company_shortcode')) {
        add_action('wp_head', 'popside_header_script');
    }


	/*	Dodanie shortcode*/


	function popside_company_shortcode( $atts ) {
		$popsideAtts = shortcode_atts( array(
			'name'         => '',
			'lowernext'    => '0',
			'maxlength'    => '99999',
			'color'        => 'inherit',
			'prefix'       => '',
			'removestatut' => '0',
			'suffix'       => '',
		), $atts );

		$AltName      = esc_attr( $popsideAtts['name'] );
		$length       = esc_attr( $popsideAtts['maxlength'] );
		$lowerNext    = esc_attr( $popsideAtts['lowernext'] );
		$color        = esc_attr( $popsideAtts['color'] );
		$prefix       = esc_attr( $popsideAtts['prefix'] );
		$removeStatut = esc_attr( $popsideAtts['removestatut'] );
		$suffix       = esc_attr( $popsideAtts['suffix'] );

		return '<span class="popside-company" color="' . $color . ';" maxlenght="' . $length . '" lowernext="' . $lowerNext . '" prefix="' . $prefix . '" suffix="' . $suffix . '" removestatut="' . $removeStatut . '">' . $AltName . '</span>';
	}

    if (!function_exists('popside_company_shortcode')) {
        add_shortcode('popside-company', 'popside_company_shortcode');
    }

	// Add scripts to wp_footer()
	function popside_footer_script() { ?>
        <script>
            window.addEventListener('load', function () {
                let popsideInterval = setInterval(popsideReplacer, 300);

                function stopTimer() {
                    window.clearInterval(popsideInterval);
                }

                setTimeout(stopTimer, 30000);

                function popsideReplacer() {
                    let getBody = document.getElementsByTagName("body")[0];
                    let company = getBody.getAttribute("popside-company");
                    if (!!company) {
                        stopTimer();
                        let elems = document.getElementsByClassName('popside-company');
                        for (let i = 0; i < elems.length; i++) {
                            company = getBody.getAttribute("popside-company");
                            let length = elems[i].attributes.maxlenght.value;
                            let color = elems[i].attributes.color.value;
                            let prefix = elems[i].attributes.prefix.value;
                            let suffix = elems[i].attributes.suffix.value;
                            let lowerNext = elems[i].attributes.lowerNext.value;
                            let removestatut = elems[i].attributes.removestatut.value;
                            if (removestatut == 1) {
                                let statutName = ['Sp. z o.o.', 'sp. z o.o.', 'spółka z o.o.', 'SP. Z O.O.', 'Spółka z o.o.', 'Sp. Z O.o.', 'S.A.', 's.a.', 'SA', 'sp. j.', 'Sp. j.', 'Sp.j.', 'Sp. J.', 'sp.p.', 'Ltd.', 'Ltd', 'L.L.C.',
                                    'LLC', ' Inc.', ' Inc', 'Corp.', 'Corp', 'GmbH', 'sp.k.', 'Sp.k.', 'Sp. k.', 'sp. k.', 'Sp. K.', 'S.K.A.', 's.k.a.', 's.r.o.', 'S.R.O', 'S.K.', 'S. K.', 'GmbH & Co.', 'Co.', 'KG', 's.c.', 'S.C.',
                                    'S.A.S', 'a.s.', 'a. s.', 'BV', 'B.V.', 'PPHU', 'AG', 'LLP', 's.r.o.', 'S.p.A.', 'j.v', 'e.V.', 'A / S', 'A/S', 'AB', 'B.V.', 's.r.l.', 'S.R.L', 'SL', 'oHG', 'Sp. k.wa'];
                                statutName.forEach(function (item) {
                                    company = company.replace(' ' + item, '');
                                });
                            }
                            if (length < company.length) {
                                elems[i].innerHTML = company.substring(0, length) + '...</span>';
                                company = company.substring(0, length) + '...</span>';
                            } else {
                                elems[i].innerHTML = company + '</span>';
                                company = elems[i].innerHTML + '</span>';
                            }
                            let parentNode = elems[i].parentNode.innerHTML;
                            let toChange = parentNode.split(company).pop();
                            toChange = toChange.trim();
                            let changedString = parentNode;
                            if (lowerNext == 1) {
                                let toChangelow = toChange.charAt(0).toLowerCase() + toChange.slice(1);
                                changedString = parentNode.replace(toChange, toChangelow);
                            }
                            if (prefix.length > 0) {
                                var companySpan = prefix + ' <span style="color:' + color + '">' + company;
                            } else {
                                companySpan = '<span style="color:' + color + '">' + company;
                            }
                            if (suffix.length > 0){
                                companySpan = companySpan + suffix;
                            }
                            changedString = changedString.replace(company, companySpan);
                            elems[i].parentNode.innerHTML = changedString;
                        }
                    }
                }
            });

        </script>

	<?php }

    if (!function_exists('popside_footer_script')) {
        add_action('wp_footer', 'popside_footer_script');
    }


	function popside_enqueue_plugin_scripts( $plugin_array ) {
		//enqueue TinyMCE plugin script with its ID.
		$plugin_array["popside_shortcode_button_plugin"] = plugin_dir_url( __FILE__ ) . "shortcode-editor.js";

		return $plugin_array;
	}

    if (!function_exists('popside_enqueue_plugin_scripts')) {
        add_filter("mce_external_plugins", "popside_enqueue_plugin_scripts");
    }

	function popside_register_buttons_editor( $buttons ) {
		//register buttons with their id.
		array_push( $buttons, "popside" );

		return $buttons;
	}

    if (!function_exists('popside_register_buttons_editor')) {
        add_filter("mce_buttons", "popside_register_buttons_editor");
    }


?>
