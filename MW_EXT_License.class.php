<?php

namespace MediaWiki\Extension\MW_EXT_License;

use OutputPage, Parser, Skin;
use MediaWiki\Extension\MW_EXT_Core\MW_EXT_Core;

/**
 * Class MW_EXT_License
 * ------------------------------------------------------------------------------------------------------------------ */
class MW_EXT_License {

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
	 * @throws \MWException
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setFunctionHook( 'license', [ __CLASS__, 'onRenderTag' ] );

		return true;
	}

	/**
	 * Render tag function.
	 *
	 * @param Parser $parser
	 * @param string $type
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onRenderTag( Parser $parser, $type = '' ) {
		// Argument: type.
		$getType = MW_EXT_Core::outClear( $type ?? '' ?: '' );
		$outType = MW_EXT_Core::outNormalize( $getType );

		// Check license type, set error category.
		if ( ! self::getLicense( $outType ) ) {
			$parser->addTrackingCategory( 'mw-ext-license-error-category' );

			return null;
		}

		// Get title.
		$getTitle = self::getLicenseTitle( $outType );
		$outTitle = $getTitle;

		// Get icon.
		$getIcon = self::getLicenseIcon( $outType );
		$outIcon = $getIcon;

		// Get content.
		$getContent = self::getLicenseContent( $outType );
		$outContent = empty( $getContent ) ? '' : '<p>' . MW_EXT_Core::getMessageText( 'license', $getContent ) . '</p>';

		// Get URL.
		$getURL = self::getLicenseURL( $outType );
		$outURL = empty( $getURL ) ? '<em>' . $outTitle . '</em>' : '<a href="' . $getURL . '" rel="nofollow" target="_blank"><em>' . $outTitle . '</em></a>';

		// Get description.
		$getDescription = MW_EXT_Core::getMessageText( 'license', 'description' );
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
		$msgPermissions = MW_EXT_Core::getMessageText( 'license', 'permissions' );
		$msgConditions  = MW_EXT_Core::getMessageText( 'license', 'conditions' );
		$msgLimitations = MW_EXT_Core::getMessageText( 'license', 'limitations' );

		// Render permission.
		if ( $getPermission ) {
			$outPermission = '<div class="mw-ext-license-permissions">';
			$outPermission .= '<div class="mw-ext-license-permissions-title">' . $msgPermissions . '</div>';
			$outPermission .= '<div class="mw-ext-license-permissions-list">';
			$outPermission .= '<ul>';
			foreach ( $getPermission as $value ) {
				$outPermission .= '<li>' . MW_EXT_Core::getMessageText( 'license', $value ) . '</li>';
			}
			$outPermission .= '</ul></div></div>';
		}

		// Render condition.
		if ( $getCondition ) {
			$outCondition = '<div class="mw-ext-license-conditions">';
			$outCondition .= '<div class="mw-ext-license-conditions-title">' . $msgConditions . '</div>';
			$outCondition .= '<div class="mw-ext-license-conditions-list">';
			$outCondition .= '<ul>';
			foreach ( $getCondition as $value ) {
				$outCondition .= '<li>' . MW_EXT_Core::getMessageText( 'license', $value ) . '</li>';
			}
			$outCondition .= '</ul></div></div>';
		}

		// Render limitation.
		if ( $getLimitation ) {
			$outLimitation = '<div class="mw-ext-license-limitations">';
			$outLimitation .= '<div class="mw-ext-license-limitations-title">' . $msgLimitations . '</div>';
			$outLimitation .= '<div class="mw-ext-license-limitations-list">';
			$outLimitation .= '<ul>';
			foreach ( $getLimitation as $value ) {
				$outLimitation .= '<li>' . MW_EXT_Core::getMessageText( 'license', $value ) . '</li>';
			}
			$outLimitation .= '</ul></div></div>';
		}

		// Out HTML.
		$outHTML = '<div class="mw-ext-license navigation-not-searchable mw-ext-box"><div class="mw-ext-license-body">';
		$outHTML .= '<div class="mw-ext-license-icon"><div><i class="far fa-copyright"></i><i class="' . $outIcon . '"></i></div></div>';
		$outHTML .= '<div class="mw-ext-license-content">';
		$outHTML .= '<div class="mw-ext-license-title">' . $outTitle . '</div><p>' . $outDescription . '</p>' . $outContent;
		$outHTML .= '<div class="mw-ext-license-rules">' . $outPermission . $outCondition . $outLimitation . '</div>';
		$outHTML .= '</div></div></div>';

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
		$out->addModuleStyles( [ 'ext.mw.license.styles' ] );

		return true;
	}
}
