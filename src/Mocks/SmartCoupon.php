<?php
/**
 * Mock WC_Smart_Coupon class for testing.
 *
 * This is a minimal mock since Smart Coupons functionality is primarily
 * implemented through WC_Coupon meta data, not a separate class.
 *
 * @package Greys\WooCommerce\SmartCoupons\Tests\Mocks
 * @since   1.0.0
 */

namespace Greys\WooCommerce\SmartCoupons\Tests\Mocks;

use WC_Coupon;

/**
 * Mock Smart Coupon class.
 *
 * Note: In the actual Smart Coupons plugin, there is NO separate WC_Smart_Coupon class.
 * All functionality is implemented through standard WC_Coupon with custom meta keys.
 * This mock exists only to prevent errors if code expects this class.
 *
 * @since 1.0.0
 */
class SmartCoupon extends WC_Coupon {

	/**
	 * Constructor.
	 *
	 * @param mixed $data Coupon data or ID.
	 */
	public function __construct( $data = '' ) {
		parent::__construct( $data );
	}

	/**
	 * Get if email restriction is disabled.
	 *
	 * @return bool
	 */
	public function is_email_restriction_disabled() {
		return 'yes' === $this->get_meta( 'sc_disable_email_restriction' );
	}

	/**
	 * Get coupon receiver details.
	 *
	 * @return array Receiver details.
	 */
	public function get_receiver_details() {
		return $this->get_meta( 'wc_sc_coupon_receiver_details' );
	}

	/**
	 * Get coupon validity period.
	 *
	 * @return int Validity value.
	 */
	public function get_validity() {
		return absint( $this->get_meta( 'sc_coupon_validity' ) );
	}

	/**
	 * Get validity suffix.
	 *
	 * @return string Suffix (days|weeks|months|years).
	 */
	public function get_validity_suffix() {
		$suffix = $this->get_meta( 'validity_suffix' );
		return ! empty( $suffix ) ? $suffix : 'days';
	}

	/**
	 * Check if auto-generation is enabled.
	 *
	 * @return bool
	 */
	public function is_auto_generate() {
		return 'yes' === $this->get_meta( 'auto_generate_coupon' );
	}

	/**
	 * Check if auto-apply is enabled.
	 *
	 * @return bool
	 */
	public function is_auto_apply() {
		return 'yes' === $this->get_meta( 'wc_sc_auto_apply_coupon' );
	}

	/**
	 * Get maximum discount amount.
	 *
	 * @return float Maximum discount.
	 */
	public function get_max_discount() {
		return floatval( $this->get_meta( 'wc_sc_max_discount' ) );
	}

	/**
	 * Get allowed user role IDs.
	 *
	 * @return array User role IDs.
	 */
	public function get_user_role_ids() {
		$roles = $this->get_meta( 'wc_sc_user_role_ids' );
		return is_array( $roles ) ? $roles : [];
	}

	/**
	 * Get excluded user role IDs.
	 *
	 * @return array Excluded user role IDs.
	 */
	public function get_excluded_user_role_ids() {
		$roles = $this->get_meta( 'wc_sc_exclude_user_role_ids' );
		return is_array( $roles ) ? $roles : [];
	}

	/**
	 * Get coupons to generate on purchase.
	 *
	 * @return array Coupon codes to generate.
	 */
	public function get_coupons_to_generate() {
		$coupons = $this->get_meta( '_coupon_title' );
		return is_array( $coupons ) ? $coupons : [];
	}
}
