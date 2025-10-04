<?php
/**
 * Smart Coupon Assertions Trait
 *
 * Provides custom assertions for Smart Coupons testing based on actual plugin implementation.
 *
 * @package Greys\WooCommerce\SmartCoupons\Tests\Traits
 * @since   1.0.0
 */

namespace Greys\WooCommerce\SmartCoupons\Tests\Traits;

use WC_Coupon;

/**
 * Smart Coupon Assertions trait.
 *
 * @since 1.0.0
 */
trait Assertions {

	/**
	 * Assert coupon is a smart coupon (store credit).
	 *
	 * @since 1.0.0
	 * @param WC_Coupon $coupon Coupon object.
	 * @param string    $message Optional. Message to display on failure.
	 * @return void
	 */
	public function assertIsSmartCoupon( $coupon, $message = '' ) {
		$this->assertSame(
			'smart_coupon',
			$coupon->get_discount_type(),
			$message ?: 'Failed asserting coupon is a smart coupon.'
		);
	}

	/**
	 * Assert coupon has gift certificate receiver details.
	 *
	 * In Smart Coupons, gift certificates are identified by having receiver details,
	 * not a separate meta key.
	 *
	 * @since 1.0.0
	 * @param WC_Coupon $coupon Coupon object.
	 * @param string    $message Optional. Message to display on failure.
	 * @return void
	 */
	public function assertIsGiftCertificate( $coupon, $message = '' ) {
		$receiver_details = get_post_meta( $coupon->get_id(), 'wc_sc_coupon_receiver_details', true );
		$this->assertNotEmpty( $receiver_details,
			$message ?: 'Failed asserting coupon is a gift certificate.' );
	}

	/**
	 * Assert coupon has specific balance/amount.
	 *
	 * @since 1.0.0
	 * @param float     $expected Expected balance.
	 * @param WC_Coupon $coupon   Coupon object.
	 * @param string    $message  Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponBalance( $expected, $coupon, $message = '' ) {
		$this->assertEquals(
			$expected,
			floatval( $coupon->get_amount() ),
			$message ?: "Failed asserting coupon has balance of {$expected}."
		);
	}

	/**
	 * Assert coupon is restricted to specific email.
	 *
	 * @since 1.0.0
	 * @param string    $email   Email address.
	 * @param WC_Coupon $coupon  Coupon object.
	 * @param string    $message Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponRestrictedToEmail( $email, $coupon, $message = '' ) {
		$restrictions = $coupon->get_email_restrictions();
		$this->assertContains( $email,
			$restrictions,
			$message ?: "Failed asserting coupon is restricted to {$email}." );
	}

	/**
	 * Assert email restriction is disabled.
	 *
	 * Uses actual Smart Coupons meta key: sc_disable_email_restriction
	 *
	 * @since 1.0.0
	 * @param WC_Coupon $coupon  Coupon object.
	 * @param string    $message Optional. Message to display on failure.
	 * @return void
	 */
	public function assertEmailRestrictionDisabled( $coupon, $message = '' ) {
		$disabled = get_post_meta( $coupon->get_id(), 'sc_disable_email_restriction', true );
		$this->assertSame( 'yes',
			$disabled,
			$message ?: 'Failed asserting email restriction is disabled.' );
	}

	/**
	 * Assert coupon is valid for specific products.
	 *
	 * @since 1.0.0
	 * @param array     $product_ids Expected product IDs.
	 * @param WC_Coupon $coupon      Coupon object.
	 * @param string    $message     Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponValidForProducts( $product_ids, $coupon, $message = '' ) {
		$coupon_products = $coupon->get_product_ids();
		$this->assertEquals( $product_ids,
			$coupon_products,
			$message ?: 'Failed asserting coupon is valid for specified products.' );
	}

	/**
	 * Assert coupon will generate other coupons on purchase.
	 *
	 * Uses actual Smart Coupons meta key: _coupon_title (array of coupon codes)
	 *
	 * @since 1.0.0
	 * @param WC_Coupon $coupon  Coupon object.
	 * @param string    $message Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponGeneratesCoupons( $coupon, $message = '' ) {
		$coupons_to_generate = get_post_meta( $coupon->get_id(), '_coupon_title', true );
		$this->assertNotEmpty( $coupons_to_generate,
			$message ?: 'Failed asserting coupon generates other coupons.' );
	}

	/**
	 * Assert coupon has auto-generation enabled.
	 *
	 * Uses actual Smart Coupons meta key: auto_generate_coupon
	 *
	 * @since 1.0.0
	 * @param WC_Coupon $coupon  Coupon object.
	 * @param string    $message Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponAutoGenerates( $coupon, $message = '' ) {
		$auto_generate = get_post_meta( $coupon->get_id(), 'auto_generate_coupon', true );
		$this->assertSame( 'yes',
			$auto_generate,
			$message ?: 'Failed asserting coupon has auto-generation enabled.' );
	}

	/**
	 * Assert coupon can be auto-applied.
	 *
	 * Uses actual Smart Coupons meta key: wc_sc_auto_apply_coupon
	 *
	 * @since 1.0.0
	 * @param WC_Coupon $coupon  Coupon object.
	 * @param string    $message Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponAutoApplies( $coupon, $message = '' ) {
		$auto_apply = get_post_meta( $coupon->get_id(), 'wc_sc_auto_apply_coupon', true );
		$this->assertSame( 'yes',
			$auto_apply,
			$message ?: 'Failed asserting coupon can be auto-applied.' );
	}

	/**
	 * Assert coupon has specific validity period.
	 *
	 * Uses actual Smart Coupons meta keys:
	 * - sc_coupon_validity: Number value
	 * - validity_suffix: 'days'|'weeks'|'months'|'years'
	 *
	 * @since 1.0.0
	 * @param int       $expected Expected validity value.
	 * @param string    $suffix   Expected suffix (days|weeks|months|years).
	 * @param WC_Coupon $coupon   Coupon object.
	 * @param string    $message  Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponValidity( $expected, $suffix, $coupon, $message = '' ) {
		$validity        = get_post_meta( $coupon->get_id(), 'sc_coupon_validity', true );
		$validity_suffix = get_post_meta( $coupon->get_id(), 'validity_suffix', true );

		$this->assertSame(
			$expected,
			absint( $validity ),
			$message ?: "Failed asserting coupon has validity of {$expected} {$suffix}."
		);

		$this->assertSame( $suffix,
			$validity_suffix,
			$message ?: "Failed asserting coupon validity suffix is {$suffix}." );
	}

	/**
	 * Assert coupon has maximum discount limit.
	 *
	 * Uses actual Smart Coupons meta key: wc_sc_max_discount
	 *
	 * @since 1.0.0
	 * @param float     $expected Expected max discount.
	 * @param WC_Coupon $coupon   Coupon object.
	 * @param string    $message  Optional. Message to display on failure.
	 * @return void
	 */
	public function assertMaxDiscount( $expected, $coupon, $message = '' ) {
		$max_discount = get_post_meta( $coupon->get_id(), 'wc_sc_max_discount', true );
		$this->assertEquals(
			$expected,
			floatval( $max_discount ),
			$message ?: "Failed asserting coupon has max discount of {$expected}."
		);
	}

	/**
	 * Assert coupon is restricted to specific user roles.
	 *
	 * Uses actual Smart Coupons meta key: wc_sc_user_role_ids
	 *
	 * @since 1.0.0
	 * @param array     $expected_roles Expected user role IDs.
	 * @param WC_Coupon $coupon         Coupon object.
	 * @param string    $message        Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponUserRoles( $expected_roles, $coupon, $message = '' ) {
		$user_roles = get_post_meta( $coupon->get_id(), 'wc_sc_user_role_ids', true );
		$this->assertEquals( $expected_roles,
			$user_roles,
			$message ?: 'Failed asserting coupon is restricted to specified user roles.' );
	}

	/**
	 * Assert coupon excludes specific user roles.
	 *
	 * Uses actual Smart Coupons meta key: wc_sc_exclude_user_role_ids
	 *
	 * @since 1.0.0
	 * @param array     $expected_roles Expected excluded user role IDs.
	 * @param WC_Coupon $coupon         Coupon object.
	 * @param string    $message        Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponExcludesUserRoles( $expected_roles, $coupon, $message = '' ) {
		$excluded_roles = get_post_meta( $coupon->get_id(), 'wc_sc_exclude_user_role_ids', true );
		$this->assertEquals( $expected_roles,
			$excluded_roles,
			$message ?: 'Failed asserting coupon excludes specified user roles.' );
	}

	/**
	 * Assert customer has specific credit balance.
	 *
	 * Calculates total available store credit across all customer's smart coupons.
	 *
	 * @since 1.0.0
	 * @param float  $expected    Expected balance.
	 * @param int    $customer_id Customer ID.
	 * @param string $message     Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCustomerCreditBalance( $expected, $customer_id, $message = '' ) {
		$customer = new \WC_Customer( $customer_id );
		$email    = $customer->get_email();

		$args = [
			'post_type'      => 'shop_coupon',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => 'customer_email',
					'value'   => $email,
					'compare' => 'LIKE',
				],
				[
					'key'     => 'discount_type',
					'value'   => 'smart_coupon',
					'compare' => '=',
				],
			],
		];

		$total_credit = 0;
		$coupon_posts = get_posts( $args );

		foreach ( $coupon_posts as $post ) {
			$coupon = new \WC_Coupon( $post->ID );

			if ( $coupon->get_usage_count() < $coupon->get_usage_limit() ) {
				$total_credit += floatval( $coupon->get_amount() );
			}
		}

		$this->assertEquals( $expected,
			$total_credit,
			$message ?: "Failed asserting customer has credit balance of {$expected}." );
	}

	/**
	 * Assert coupon has receiver details (gift certificate).
	 *
	 * Uses actual Smart Coupons meta key: wc_sc_coupon_receiver_details
	 *
	 * @since 1.0.0
	 * @param string    $receiver_email Expected receiver email.
	 * @param WC_Coupon $coupon         Coupon object.
	 * @param string    $message        Optional. Message to display on failure.
	 * @return void
	 */
	public function assertCouponReceiverEmail( $receiver_email, $coupon, $message = '' ) {
		$receiver_details = get_post_meta( $coupon->get_id(), 'wc_sc_coupon_receiver_details', true );

		$this->assertIsArray( $receiver_details, 'Receiver details should be an array.' );
		$this->assertArrayHasKey( 'email', $receiver_details, 'Receiver details should have email key.' );
		$this->assertSame( $receiver_email,
			$receiver_details['email'],
			$message ?: "Failed asserting coupon receiver email is {$receiver_email}."
		);
	}

	/**
	 * Assert product has linked coupons.
	 *
	 * Uses actual Smart Coupons meta key: _coupon_title (on product)
	 *
	 * @since 1.0.0
	 * @param array         $expected_coupons Expected coupon codes.
	 * @param \WC_Product   $product          Product object.
	 * @param string        $message          Optional. Message to display on failure.
	 * @return void
	 */
	public function assertProductHasLinkedCoupons( $expected_coupons, $product, $message = '' ) {
		$linked_coupons = get_post_meta( $product->get_id(), '_coupon_title', true );
		$this->assertEquals( $expected_coupons,
			$linked_coupons,
			$message ?: 'Failed asserting product has linked coupons.' );
	}
}
