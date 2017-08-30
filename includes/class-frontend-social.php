<?php

/**
 * @link              https://github.com/jupitercow/sewn-in-simple-seo
 * @since             2.1.0
 * @package           Sewn_Seo/Includes
 */

$class_name = 'Sewn_Seo_Frontend_Social';
if ( ! class_exists($class_name) ) :

class Sewn_Seo_Frontend_Social extends Sewn_Seo_Frontend
{
	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    2.1.0
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->settings['meta_fields'] = array(
			'og_locale'      => '<meta property="og:locale" content="%s">',
			'og_type'        => '<meta property="og:type" content="%s">',
			'og_title'       => '<meta property="og:title" content="%s">',
			'og_description' => '<meta property="og:description" content="%s">',
			'og_url'         => '<meta property="og:url" content="%s">',
			'og_site_name'   => '<meta property="og:site_name" name="copyright" content="%s">',
			'og_image'       => '<meta property="og:image" content="%s">',
			'tw_card'        => '<meta name="twitter:card" content="%s">',
			'tw_description' => '<meta name="twitter:description" content="%s">',
			'tw_title'       => '<meta name="twitter:title" content="%s">',
			'tw_image'       => '<meta name="twitter:image" content="%s">',
		);
		$this->settings = apply_filters( "{$this->prefix}/seo/frontend/social", $this->settings );
	}

	/**
	 * Initialize the plugin once during run.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function init()
	{
		add_action( "{$this->prefix}/seo/head",                array($this, 'wp_head'), 1 );

		/* Facebook Open Graph */
		add_action( "{$this->prefix}/seo/og",                  array($this, 'meta_og') );
		add_action( "{$this->prefix}/seo/og:locale",           array($this, 'meta_og_locale') );
		add_action( "{$this->prefix}/seo/og:type",             array($this, 'meta_og_type') );
		add_action( "{$this->prefix}/seo/og:title",            array($this, 'meta_og_title') );
		add_action( "{$this->prefix}/seo/og:description",      array($this, 'meta_og_description') );
		add_action( "{$this->prefix}/seo/og:url",              array($this, 'meta_og_url') );
		add_action( "{$this->prefix}/seo/og:site_name",        array($this, 'meta_og_site_name') );
		add_action( "{$this->prefix}/seo/og:image",            array($this, 'meta_og_image') );

		/* Twitter */
		add_action( "{$this->prefix}/seo/twitter",             array($this, 'meta_twitter') );
		add_action( "{$this->prefix}/seo/twitter:card",        array($this, 'meta_twitter_card') );
		add_action( "{$this->prefix}/seo/twitter:title",       array($this, 'meta_twitter_title') );
		add_action( "{$this->prefix}/seo/twitter:description", array($this, 'meta_twitter_description') );
		add_action( "{$this->prefix}/seo/twitter:image",       array($this, 'meta_twitter_image') );
	}

	/**
	 * wp_head
	 *
	 * Output all fields
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function wp_head()
	{
		do_action( "{$this->prefix}/seo/og" );
		do_action( "{$this->prefix}/seo/twitter" );
	}

	/**
	 * Facebook Open Graph fields
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_og()
	{
		do_action( "{$this->prefix}/seo/og:locale" );
		do_action( "{$this->prefix}/seo/og:type" );
		do_action( "{$this->prefix}/seo/og:title" );
		do_action( "{$this->prefix}/seo/og:description" );
		do_action( "{$this->prefix}/seo/og:url" );
		do_action( "{$this->prefix}/seo/og:site_name" );
		do_action( "{$this->prefix}/seo/og:image" );
	}

	/**
	 * Twitter fields
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_twitter()
	{
		do_action( "{$this->prefix}/seo/twitter:card" );
		do_action( "{$this->prefix}/seo/twitter:title" );
		do_action( "{$this->prefix}/seo/twitter:description" );
		do_action( "{$this->prefix}/seo/twitter:image" );
	}

	/**
	 * Output OpenGraph locale.
	 *
	 * @since 2.1.0
	 * @see  http://www.facebook.com/translations/FacebookLocales.xml for the list of supported locales
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/article/
	 * @return string $locale
	 */
	public function meta_og_locale() {
		$locale = apply_filters( '{$this->prefix}/seo/field/locale', get_locale() );

		// Catch some weird locales served out by WP that are not easily doubled up.
		$fix_locales = array(
			'ca' => 'ca_ES',
			'en' => 'en_US',
			'el' => 'el_GR',
			'et' => 'et_EE',
			'ja' => 'ja_JP',
			'sq' => 'sq_AL',
			'uk' => 'uk_UA',
			'vi' => 'vi_VN',
			'zh' => 'zh_CN',
		);

		if ( isset($fix_locales[$locale]) ) {
			$locale = $fix_locales[ $locale ];
		}

		// Convert locales like "es" to "es_ES", in case that works for the given locale (sometimes it does).
		if ( strlen( $locale ) == 2 ) {
			$locale = strtolower( $locale ) . '_' . strtoupper( $locale );
		}

		// These are the locales FB supports.
		$fb_valid_fb_locales = array(
			'af_ZA', // Afrikaans.
			'ak_GH', // Akan.
			'am_ET', // Amharic.
			'ar_AR', // Arabic.
			'as_IN', // Assamese.
			'ay_BO', // Aymara.
			'az_AZ', // Azerbaijani.
			'be_BY', // Belarusian.
			'bg_BG', // Bulgarian.
			'bn_IN', // Bengali.
			'br_FR', // Breton.
			'bs_BA', // Bosnian.
			'ca_ES', // Catalan.
			'cb_IQ', // Sorani Kurdish.
			'ck_US', // Cherokee.
			'co_FR', // Corsican.
			'cs_CZ', // Czech.
			'cx_PH', // Cebuano.
			'cy_GB', // Welsh.
			'da_DK', // Danish.
			'de_DE', // German.
			'el_GR', // Greek.
			'en_GB', // English (UK).
			'en_IN', // English (India).
			'en_PI', // English (Pirate).
			'en_UD', // English (Upside Down).
			'en_US', // English (US).
			'eo_EO', // Esperanto.
			'es_CL', // Spanish (Chile).
			'es_CO', // Spanish (Colombia).
			'es_ES', // Spanish (Spain).
			'es_LA', // Spanish.
			'es_MX', // Spanish (Mexico).
			'es_VE', // Spanish (Venezuela).
			'et_EE', // Estonian.
			'eu_ES', // Basque.
			'fa_IR', // Persian.
			'fb_LT', // Leet Speak.
			'ff_NG', // Fulah.
			'fi_FI', // Finnish.
			'fo_FO', // Faroese.
			'fr_CA', // French (Canada).
			'fr_FR', // French (France).
			'fy_NL', // Frisian.
			'ga_IE', // Irish.
			'gl_ES', // Galician.
			'gn_PY', // Guarani.
			'gu_IN', // Gujarati.
			'gx_GR', // Classical Greek.
			'ha_NG', // Hausa.
			'he_IL', // Hebrew.
			'hi_IN', // Hindi.
			'hr_HR', // Croatian.
			'hu_HU', // Hungarian.
			'hy_AM', // Armenian.
			'id_ID', // Indonesian.
			'ig_NG', // Igbo.
			'is_IS', // Icelandic.
			'it_IT', // Italian.
			'ja_JP', // Japanese.
			'ja_KS', // Japanese (Kansai).
			'jv_ID', // Javanese.
			'ka_GE', // Georgian.
			'kk_KZ', // Kazakh.
			'km_KH', // Khmer.
			'kn_IN', // Kannada.
			'ko_KR', // Korean.
			'ku_TR', // Kurdish (Kurmanji).
			'ky_KG', // Kyrgyz.
			'la_VA', // Latin.
			'lg_UG', // Ganda.
			'li_NL', // Limburgish.
			'ln_CD', // Lingala.
			'lo_LA', // Lao.
			'lt_LT', // Lithuanian.
			'lv_LV', // Latvian.
			'mg_MG', // Malagasy.
			'mi_NZ', // Maori.
			'mk_MK', // Macedonian.
			'ml_IN', // Malayalam.
			'mn_MN', // Mongolian.
			'mr_IN', // Marathi.
			'ms_MY', // Malay.
			'mt_MT', // Maltese.
			'my_MM', // Burmese.
			'nb_NO', // Norwegian (bokmal).
			'nd_ZW', // Ndebele.
			'ne_NP', // Nepali.
			'nl_BE', // Dutch (Belgie).
			'nl_NL', // Dutch.
			'nn_NO', // Norwegian (nynorsk).
			'ny_MW', // Chewa.
			'or_IN', // Oriya.
			'pa_IN', // Punjabi.
			'pl_PL', // Polish.
			'ps_AF', // Pashto.
			'pt_BR', // Portuguese (Brazil).
			'pt_PT', // Portuguese (Portugal).
			'qu_PE', // Quechua.
			'rm_CH', // Romansh.
			'ro_RO', // Romanian.
			'ru_RU', // Russian.
			'rw_RW', // Kinyarwanda.
			'sa_IN', // Sanskrit.
			'sc_IT', // Sardinian.
			'se_NO', // Northern Sami.
			'si_LK', // Sinhala.
			'sk_SK', // Slovak.
			'sl_SI', // Slovenian.
			'sn_ZW', // Shona.
			'so_SO', // Somali.
			'sq_AL', // Albanian.
			'sr_RS', // Serbian.
			'sv_SE', // Swedish.
			'sw_KE', // Swahili.
			'sy_SY', // Syriac.
			'sz_PL', // Silesian.
			'ta_IN', // Tamil.
			'te_IN', // Telugu.
			'tg_TJ', // Tajik.
			'th_TH', // Thai.
			'tk_TM', // Turkmen.
			'tl_PH', // Filipino.
			'tl_ST', // Klingon.
			'tr_TR', // Turkish.
			'tt_RU', // Tatar.
			'tz_MA', // Tamazight.
			'uk_UA', // Ukrainian.
			'ur_PK', // Urdu.
			'uz_UZ', // Uzbek.
			'vi_VN', // Vietnamese.
			'wo_SN', // Wolof.
			'xh_ZA', // Xhosa.
			'yi_DE', // Yiddish.
			'yo_NG', // Yoruba.
			'zh_CN', // Simplified Chinese (China).
			'zh_HK', // Traditional Chinese (Hong Kong).
			'zh_TW', // Traditional Chinese (Taiwan).
			'zu_ZA', // Zulu.
			'zz_TR', // Zazaki.
		);

		// Check to see if the locale is a valid FB one, if not, use en_US as a fallback.
		if ( ! in_array( $locale, $fb_valid_fb_locales ) ) {
			$locale = strtolower( substr( $locale, 0, 2 ) ) . '_' . strtoupper( substr( $locale, 0, 2 ) );
			if ( ! in_array( $locale, $fb_valid_fb_locales ) ) {
				$locale = 'en_US';
			}
		}

		if ( $locale ) {
			printf( $this->settings['meta_fields']['og_locale'] . "\n", esc_attr($locale) );
		}
	}

	/**
	 * Output OpenGraph type.
	 *
	 * @since	2.1.0
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/object/
	 * @return string $type
	 */
	public function meta_og_type() {
		$type = '';
		if ( is_front_page() || is_home() ) {
			$type = 'website';
		} elseif ( is_singular() ) {
			if ( ! empty($GLOBALS['post']) ) {
				$type = get_post_meta( $GLOBALS['post']->ID, 'og_type', true );
			}

			if ( ! $type ) {
				$type = 'article';
			}
		} else {
			// Archives, etc.
			$type = 'object';
		}

		$type = apply_filters( "{$this->prefix}/seo/field/og_type", $type );
		if ( $type ) {
			printf( $this->settings['meta_fields']['og_type'] . "\n", esc_attr($type) );
		}
	}

	/**
	 * Better SEO: open graph title.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_og_title()
	{
		if ( ! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_og_title', true) ) {
			$content = $meta;
		} else {
			$content = $this->seo_title( ',' );
		}

		$content = apply_filters( "{$this->prefix}/seo/field/og_title", $content );
		if ( $content ) {
			printf( $this->settings['meta_fields']['og_title'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: open graph description.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_og_description()
	{
		$content = '';
		if ( ! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_og_description', true) ) {
			$content = $meta;
		} else {
			$content = $this->seo_description();
		}

		$content = apply_filters( "{$this->prefix}/seo/field/og_description", $content );
		if ( $content ) {
			printf( $this->settings['meta_fields']['og_description'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: open graph image.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_og_image()
	{
		$content = '';
		if ( ! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_og_image', true) ) {
			$content = $meta;
		} elseif ( ! empty($GLOBALS['post']->ID) && $meta_array = wp_get_attachment_image_src(get_post_thumbnail_id($GLOBALS['post']->ID), 'full') ) {
			if ( ! empty($meta_array[0]) ) {
				$content = $meta_array[0];
			}
		}

		$content = apply_filters( "{$this->prefix}/seo/field/og_image", $content );
		if ( $content ) {
			printf( $this->settings['meta_fields']['og_image'] . "\n", esc_url($content) );
		}
	}

	/**
	 * Outputs the canonical URL as OpenGraph URL, which consolidates likes and shares.
	 *
	 * @since	2.1.0
	 * @link    https://developers.facebook.com/docs/reference/opengraph/object-type/article/
	 * @return  void
	 */
	public function meta_og_url() {
		$content = '';
		if ( ! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_og_url', true) ) {
			$content = $meta;
		} else {
			$content = $this->seo_canonical();
		}

		$content = apply_filters( "{$this->prefix}/seo/field/og_url", $content );
		if ( $content ) {
			printf( $this->settings['meta_fields']['og_url'] . "\n", esc_url($content) );
		}
	}

	/**
	 * Output the site name straight from the blog info.
	 *
	 * @since	2.1.0
	 * return   void;
	 */
	public function meta_og_site_name()
	{
		$content = apply_filters( "{$this->prefix}/seo/field/og_site_name", get_option('blogname') );
		if ( $content ) {
			printf( $this->settings['meta_fields']['og_site_name'] . "\n", esc_attr($content) );
		}
	}

	/**
	 * Better SEO: twitter card.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_twitter_card()
	{
		$content = '';
		if ( is_singular() && has_shortcode( $GLOBALS['post']->post_content, 'gallery' ) ) {

			$images = get_post_gallery_images();

			if ( count( $images ) > 0 ) {
				$content = 'summary_large_image';
			}
		}
		$content = apply_filters( "{$this->prefix}/seo/field/tw_card", $content );

		if ( ! in_array( $content, array(
				'summary',
				'summary_large_image',
				'app',
				'player',
			) )
		) {
			$content = 'summary';
		}

		if ( $content ) {
			printf( $this->settings['meta_fields']['tw_card'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: twitter title.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_twitter_title()
	{
		if ( ! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_tw_title', true) ) {
			$content = $meta;
		} else {
			$content = $this->seo_title( ',' );
		}

		$content = apply_filters( "{$this->prefix}/seo/field/tw_title", $content );
		if ( $content ) {
			printf( $this->settings['meta_fields']['tw_title'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: twitter description.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_twitter_description()
	{
		$content = '';
		if ( ! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_tw_description', true) ) {
			$content = $meta;
		} else {
			$content = $this->seo_description();
		}

		$content = apply_filters( "{$this->prefix}/seo/field/tw_description", $content );
		if ( $content ) {
			printf( $this->settings['meta_fields']['tw_description'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: twitter image.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_twitter_image()
	{
		$content = '';
		if ( ! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_tw_image', true) ) {
			$content = $meta;
		} elseif ( ! empty($GLOBALS['post']->ID) && $meta_array = wp_get_attachment_image_src(get_post_thumbnail_id($GLOBALS['post']->ID), 'full') ) {
			if ( ! empty($meta_array[0]) ) {
				$content = $meta_array[0];
			}
		}

		$content = apply_filters( "{$this->prefix}/seo/field/tw_image", $content );
		if ( $content ) {
			printf( $this->settings['meta_fields']['tw_image'] . "\n", esc_url($content) );
		}
	}
}

endif;
