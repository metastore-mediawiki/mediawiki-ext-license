<?php

/**
 * Class MW_EXT_License
 * ------------------------------------------------------------------------------------------------------------------ */

class MW_EXT_License {

	/**
	 * * Clear DATA (escape html).
	 *
	 * @param $string
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function clearData( $string ) {
		$outString = htmlspecialchars( trim( $string ), ENT_QUOTES );

		return $outString;
	}

	/**
	 * Convert DATA (replace space & lower case).
	 *
	 * @param $string
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function convertData( $string ) {
		$outString = mb_strtolower( str_replace( ' ', '-', $string ), 'UTF-8' );

		return $outString;
	}

	/**
	 * Get MediaWiki message.
	 *
	 * @param $string
	 *
	 * @return string
	 */
	private static function getMsgText( $string ) {
		$outString = wfMessage( 'mw-ext-license-' . $string )->inContentLanguage()->text();

		return $outString;
	}

	/**
	 * Get JSON data.
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getData() {
		$getData = file_get_contents( __DIR__ . '/storage/license.json' );
		$outData = json_decode( $getData, true );

		return $outData;
	}

	/**
	 * Get license.
	 *
	 * @param $license
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getLicense( $license ) {
		$getData = self::getData();

		if ( ! isset( $getData['license'][ $license ] ) ) {
			return false;
		}

		$getLicense = $getData['license'][ $license ];
		$outLicense = $getLicense;

		return $outLicense;
	}

	/**
	 * Get license title.
	 *
	 * @param $license
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getLicenseTitle( $license ) {
		$license = self::getLicense( $license ) ? self::getLicense( $license ) : '';

		if ( ! isset( $license['title'] ) ) {
			return false;
		}

		$getTitle = $license['title'];
		$outTitle = $getTitle;

		return $outTitle;
	}

	/**
	 * Get license icon.
	 *
	 * @param $license
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getLicenseIcon( $license ) {
		$license = self::getLicense( $license ) ? self::getLicense( $license ) : '';

		if ( ! isset( $license['icon'] ) ) {
			return false;
		}

		$getIcon = $license['icon'];
		$outIcon = $getIcon;

		return $outIcon;
	}

	/**
	 * Get license content.
	 *
	 * @param $license
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getLicenseContent( $license ) {
		$license = self::getLicense( $license ) ? self::getLicense( $license ) : '';

		if ( ! isset( $license['content'] ) ) {
			return false;
		}

		$getContent = $license['content'];
		$outContent = $getContent;

		return $outContent;
	}

	/**
	 * Get license URL.
	 *
	 * @param $license
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getLicenseURL( $license ) {
		$license = self::getLicense( $license ) ? self::getLicense( $license ) : '';

		if ( ! isset( $license['url'] ) ) {
			return false;
		}

		$getURL = $license['url'];
		$outURL = $getURL;

		return $outURL;
	}

	/**
	 * Get license rule.
	 *
	 * @param $license
	 * @param $rule
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getLicenseRule( $license, $rule ) {
		$license = self::getLicense( $license ) ? self::getLicense( $license ) : '';

		if ( ! isset( $license['rule'][ $rule ] ) ) {
			return false;
		}

		$getRule = $license['rule'][ $rule ];
		$outRule = $getRule;

		return $outRule;
	}

	/**
	 * Register tag function.
	 *
	 * @param Parser $parser
	 *
	 * @return bool
	 * @throws MWException
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setFunctionHook( 'license', __CLASS__ . '::onRenderTag' );

		return true;
	}

	/**
	 * Render tag function.
	 *
	 * @param Parser $parser
	 * @param string $type
	 *
	 * @return bool|string
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onRenderTag( Parser $parser, $type = '' ) {
		// Argument: type.
		$getType = self::clearData( $type ?? '' ?: '' );
		$outType = self::convertData( $getType );

		// Check license type, set error category.
		if ( ! self::getLicense( $outType ) ) {
			$parser->addTrackingCategory( 'mw-ext-license-error-category' );

			return false;
		}

		// Get title.
		$getTitle = self::getLicenseTitle( $outType );
		$outTitle = $getTitle;

		// Get icon.
		$getIcon = self::getLicenseIcon( $outType );
		$outIcon = $getIcon;

		// Get content.
		$getContent = self::getLicenseContent( $outType );
		$outContent = empty( $getContent ) ? '' : '<p>' . self::getMsgText( $getContent ) . '</p>';

		// Get URL.
		$getURL = self::getLicenseURL( $outType );
		$outURL = empty( $getURL ) ? '<em>' . $outTitle . '</em>' : '<a href="' . $getURL . '" rel="nofollow" target="_blank"><em>' . $outTitle . '</em></a>';

		// Get description.
		$getDescription = self::getMsgText( 'description' );
		$outDescription = $getDescription . ': ' . $outURL;

		// Get permission.
		$getPermission = self::getLicenseRule( $outType, 'permission' );
		$outPermission = '';

		// Get condition.
		$getCondition = self::getLicenseRule( $outType, 'condition' );
		$outCondition = '';

		// Get limitation.
		$getLimitation = self::getLicenseRule( $outType, 'limitation' );
		$outLimitation = '';

		// Loading messages.
		$msgPermissions = self::getMsgText( 'permissions' );
		$msgConditions  = self::getMsgText( 'conditions' );
		$msgLimitations = self::getMsgText( 'limitations' );

		// Render permission.
		if ( $getPermission ) {
			$outPermission = '<div class="mw-ext-license-permissions"><h4>' . $msgPermissions . '</h4><ul>';
			foreach ( $getPermission as $value ) {
				$outPermission .= '<li>' . self::getMsgText( $value ) . '</li>';
			}
			$outPermission .= '</ul></div>';
		}

		// Render condition.
		if ( $getCondition ) {
			$outCondition = '<div class="mw-ext-license-conditions"><h4>' . $msgConditions . '</h4><ul>';
			foreach ( $getCondition as $value ) {
				$outCondition .= '<li>' . self::getMsgText( $value ) . '</li>';
			}
			$outCondition .= '</ul></div>';
		}

		// Render limitation.
		if ( $getLimitation ) {
			$outLimitation = '<div class="mw-ext-license-limitations"><h4>' . $msgLimitations . '</h4><ul>';
			foreach ( $getLimitation as $value ) {
				$outLimitation .= '<li>' . self::getMsgText( $value ) . '</li>';
			}
			$outLimitation .= '</ul></div>';
		}

		// Out HTML.
		$outHTML = '<div class="mw-ext-license"><div class="mw-ext-license-body">';
		$outHTML .= '<div class="mw-ext-license-icon"><div><i class="far fa-copyright"></i><i class="' . $outIcon . '"></i></div></div>';
		$outHTML .= '<div class="mw-ext-license-content">';
		$outHTML .= '<h4>' . $outTitle . '</h4><p>' . $outDescription . '</p>' . $outContent;
		$outHTML .= '<div class="mw-ext-license-rules">' . $outPermission . $outCondition . $outLimitation . '</div>';
		$outHTML .= '</div>';
		$outHTML .= '</div></div>';

		// Out parser.
		$outParser = $parser->insertStripItem( $outHTML, $parser->mStripState );

		return $outParser;
	}

	/**
	 * Load resource function.
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 *
	 * @return bool
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		$out->addModuleStyles( array( 'ext.mw.license.styles' ) );

		return true;
	}
}
