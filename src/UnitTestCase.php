<?php
/**
 * Base Test Case for WooCommerce Smart Coupons Tests
 *
 * @package Greys\WooCommerce\SmartCoupons\Tests
 * @since   1.0.0
 */

namespace Greys\WooCommerce\SmartCoupons\Tests;

use WC_Unit_Test_Case;

/**
 * WCSC Unit Test Case class.
 *
 * Extends WC_Unit_Test_Case with Smart Coupons-specific functionality.
 *
 * @since 1.0.0
 */
class UnitTestCase extends WC_Unit_Test_Case {

	use Traits\Assertions;

	/**
	 * Set up test case.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		// Enable Smart Coupons functionality.
		update_option( 'woocommerce_enable_coupons', 'yes' );
		update_option( 'wc_sc_enable_store_credit', 'yes' );
		update_option( 'wc_sc_enable_gift_certificates', 'yes' );
	}

	/**
	 * Helper to create store credit coupon.
	 *
	 * @param array $args Coupon arguments.
	 * @return \WC_Coupon
	 */
	protected function create_store_credit( $args = [] ) {
		return Helpers\Coupon::create_store_credit( $args );
	}

	/**
	 * Helper to create gift card.
	 *
	 * @param array $args Gift card arguments.
	 * @return \WC_Coupon
	 */
	protected function create_gift_card( $args = [] ) {
		return Helpers\Coupon::create_gift_card( $args );
	}

	/**
	 * Helper to create combination coupon.
	 *
	 * @param array $args Combination coupon arguments.
	 * @return \WC_Coupon
	 */
	protected function create_combination_coupon( $args = [] ) {
		return Helpers\Coupon::create_combination_coupon( $args );
	}

	/**
	 * Helper to create URL coupon.
	 *
	 * @param array $args URL coupon arguments.
	 * @return \WC_Coupon
	 */
	protected function create_url_coupon( $args = [] ) {
		return Helpers\Coupon::create_url_coupon( $args );
	}

	/**
	 * Helper to create product credit.
	 *
	 * @param array $args Product credit arguments.
	 * @return \WC_Coupon
	 */
	protected function create_product_credit( $args = [] ) {
		return Helpers\Coupon::create_product_credit( $args );
	}

	/**
	 * Apply credit to customer.
	 *
	 * @param int   $customer_id Customer ID.
	 * @param float $amount      Credit amount.
	 * @param array $args        Optional arguments.
	 * @return \WC_Coupon
	 */
	protected function apply_credit_to_customer( $customer_id, $amount, $args = [] ) {
		return Helpers\Coupon::apply_credit_to_customer( $customer_id, $amount, $args );
	}

	/**
	 * Get customer credit balance.
	 *
	 * @param int $customer_id Customer ID.
	 * @return float
	 */
	protected function get_customer_credit_balance( $customer_id ) {
		return Helpers\Coupon::get_customer_credit_balance( $customer_id );
	}
}
