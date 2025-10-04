<?php
/**
 * WooCommerce Smart Coupons Helper - Coupon
 *
 * Helper functions for creating and managing Smart Coupons in tests.
 * Based on actual WooCommerce Smart Coupons plugin meta keys and structure.
 *
 * @package Greys\WooCommerce\SmartCoupons\Tests\Helpers
 * @since   1.0.0
 */

namespace Greys\WooCommerce\SmartCoupons\Tests\Helpers;

use WC_Coupon;

/**
 * Smart Coupon helper class.
 *
 * @since 1.0.0
 */
class Coupon {

	/**
	 * Create a store credit coupon.
	 *
	 * Uses actual Smart Coupons meta keys:
	 * - discount_type: 'smart_coupon'
	 * - sc_disable_email_restriction: 'yes'|'no'
	 * - auto_generate_coupon: 'yes'|'no' for URL coupons
	 * - sc_coupon_validity + validity_suffix: expiry period
	 *
	 * @since 1.0.0
	 * @param array $args Coupon arguments.
	 * @return \WC_Coupon
	 */
	public static function create_store_credit( $args = [] ) {
		$defaults = [
			'code'                          => 'CREDIT-' . wp_generate_password( 8, false ),
			'amount'                        => 50,
			'discount_type'                 => 'smart_coupon',
			'customer_email'                => '',
			'usage_limit'                   => 1,
			'individual_use'                => true,
			'sc_disable_email_restriction'  => 'no',
			'auto_generate_coupon'          => 'no',
			'sc_coupon_validity'            => '',
			'validity_suffix'               => 'days',
		];

		$args = wp_parse_args( $args, $defaults );

		// Create coupon.
		$coupon = new WC_Coupon();
		$coupon->set_code( $args['code'] );
		$coupon->set_amount( $args['amount'] );
		$coupon->set_discount_type( $args['discount_type'] );
		$coupon->set_usage_limit( $args['usage_limit'] );
		$coupon->set_individual_use( $args['individual_use'] );

		if ( ! empty( $args['customer_email'] ) ) {
			$coupon->set_email_restrictions( [ $args['customer_email'] ] );
		}

		$coupon->save();

		// Set Smart Coupon specific meta (actual plugin meta keys).
		update_post_meta( $coupon->get_id(), 'sc_disable_email_restriction', $args['sc_disable_email_restriction'] );
		update_post_meta( $coupon->get_id(), 'auto_generate_coupon', $args['auto_generate_coupon'] );

		if ( ! empty( $args['sc_coupon_validity'] ) ) {
			update_post_meta( $coupon->get_id(), 'sc_coupon_validity', $args['sc_coupon_validity'] );
			update_post_meta( $coupon->get_id(), 'validity_suffix', $args['validity_suffix'] );
		}

		return $coupon;
	}

	/**
	 * Create a gift certificate.
	 *
	 * In Smart Coupons, gift certificates use:
	 * - wc_sc_coupon_receiver_details: Array of receiver details per coupon
	 * - gift_receiver_message: Stored on order meta
	 * - is_gift: Stored on order meta ('yes'|'no')
	 * - wc_sc_schedule_gift_sending: Schedule delivery date
	 *
	 * @since 1.0.0
	 * @param array $args Gift certificate arguments.
	 * @return \WC_Coupon
	 */
	public static function create_gift_certificate( $args = [] ) {
		$defaults = [
			'code'                   => 'GIFT-' . wp_generate_password( 8, false ),
			'amount'                 => 100,
			'discount_type'          => 'smart_coupon',
			'receiver_email'         => '',
			'receiver_name'          => '',
			'message'                => '',
			'sc_coupon_validity'     => '',
			'validity_suffix'        => 'days',
		];

		$args = wp_parse_args( $args, $defaults );

		// Create coupon.
		$coupon = new WC_Coupon();
		$coupon->set_code( $args['code'] );
		$coupon->set_amount( $args['amount'] );
		$coupon->set_discount_type( $args['discount_type'] );
		$coupon->save();

		// Set receiver details (actual Smart Coupons meta structure).
		$receiver_details = [
			[
				'email'   => $args['receiver_email'],
				'name'    => $args['receiver_name'],
				'message' => $args['message'],
			],
		];

		update_post_meta( $coupon->get_id(), 'wc_sc_coupon_receiver_details', $receiver_details );

		if ( ! empty( $args['sc_coupon_validity'] ) ) {
			update_post_meta( $coupon->get_id(), 'sc_coupon_validity', $args['sc_coupon_validity'] );
			update_post_meta( $coupon->get_id(), 'validity_suffix', $args['validity_suffix'] );
		}

		return $coupon;
	}

	/**
	 * Create a combination coupon.
	 *
	 * @since 1.0.0
	 * @param array $args Coupon arguments.
	 * @return \WC_Coupon
	 */
	public static function create_combination_coupon( $args = [] ) {
		$defaults = [
			'code'            => 'COMBO-' . wp_generate_password( 8, false ),
			'amount'          => 50,
			'discount_type'   => 'smart_coupon',
			'number_of_coupons_to_generate' => 1,
			'generated_coupon_details' => [],
		];

		$args = wp_parse_args( $args, $defaults );

		$coupon = new WC_Coupon();
		$coupon->set_code( $args['code'] );
		$coupon->set_amount( $args['amount'] );
		$coupon->set_discount_type( $args['discount_type'] );
		$coupon->save();

		// Set combination coupon meta.
		if ( ! empty( $args['number_of_coupons_to_generate'] ) ) {
			update_post_meta( $coupon->get_id(), 'number_of_coupons_to_generate', $args['number_of_coupons_to_generate'] );
		}

		if ( ! empty( $args['generated_coupon_details'] ) ) {
			update_post_meta( $coupon->get_id(), 'generated_coupon_details', $args['generated_coupon_details'] );
		}

		return $coupon;
	}

	/**
	 * Create a URL coupon.
	 *
	 * @since 1.0.0
	 * @param array $args Coupon arguments.
	 * @return \WC_Coupon
	 */
	public static function create_url_coupon( $args = [] ) {
		$defaults = [
			'code'                   => 'URL-' . wp_generate_password( 8, false ),
			'amount'                 => 25,
			'discount_type'          => 'smart_coupon',
			'auto_generate_coupon'   => 'yes',
			'wc_sc_auto_apply_coupon' => 'yes',
		];

		$args = wp_parse_args( $args, $defaults );

		$coupon = new WC_Coupon();
		$coupon->set_code( $args['code'] );
		$coupon->set_amount( $args['amount'] );
		$coupon->set_discount_type( $args['discount_type'] );
		$coupon->save();

		// Set URL coupon meta.
		update_post_meta( $coupon->get_id(), 'auto_generate_coupon', $args['auto_generate_coupon'] );
		update_post_meta( $coupon->get_id(), 'wc_sc_auto_apply_coupon', $args['wc_sc_auto_apply_coupon'] );

		return $coupon;
	}

	/**
	 * Create a product credit coupon.
	 *
	 * @since 1.0.0
	 * @param array $args Coupon arguments.
	 * @return \WC_Coupon
	 */
	public static function create_product_credit( $args = [] ) {
		$defaults = [
			'code'            => 'PRODUCT-' . wp_generate_password( 8, false ),
			'amount'          => 10,
			'discount_type'   => 'smart_coupon',
			'product_ids'     => [],
		];

		$args = wp_parse_args( $args, $defaults );

		$coupon = new WC_Coupon();
		$coupon->set_code( $args['code'] );
		$coupon->set_amount( $args['amount'] );
		$coupon->set_discount_type( $args['discount_type'] );

		if ( ! empty( $args['product_ids'] ) ) {
			$coupon->set_product_ids( $args['product_ids'] );
		}

		$coupon->save();

		return $coupon;
	}

	/**
	 * Apply credit to a customer.
	 *
	 * @since 1.0.0
	 * @param int    $customer_id Customer ID.
	 * @param float  $amount Credit amount.
	 * @param string $code Optional coupon code.
	 * @return \WC_Coupon
	 */
	public static function apply_credit_to_customer( $customer_id, $amount, $code = '' ) {
		if ( empty( $code ) ) {
			$code = 'CREDIT-' . $customer_id . '-' . wp_generate_password( 6, false );
		}

		$customer = new \WC_Customer( $customer_id );

		$coupon = new WC_Coupon();
		$coupon->set_code( $code );
		$coupon->set_amount( $amount );
		$coupon->set_discount_type( 'smart_coupon' );
		$coupon->set_email_restrictions( [ $customer->get_email() ] );
		$coupon->save();

		return $coupon;
	}

	/**
	 * Use store credit.
	 *
	 * @since 1.0.0
	 * @param \WC_Coupon $coupon Coupon object.
	 * @param float      $amount Amount to deduct.
	 * @return void
	 */
	public static function use_store_credit( $coupon, $amount ) {
		$current_amount = $coupon->get_amount();
		$new_amount = max( 0, $current_amount - $amount );
		$coupon->set_amount( $new_amount );
		$coupon->save();
	}

	/**
	 * Get customer credit balance.
	 *
	 * @since 1.0.0
	 * @param int $customer_id Customer ID.
	 * @return float Total credit balance.
	 */
	public static function get_customer_credit_balance( $customer_id ) {
		$customer = new \WC_Customer( $customer_id );
		$email = $customer->get_email();

		$args = [
			'post_type'      => 'shop_coupon',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => [
				[
					'key'     => 'discount_type',
					'value'   => 'smart_coupon',
					'compare' => '=',
				],
			],
		];

		$coupons = get_posts( $args );
		$total_credit = 0;

		foreach ( $coupons as $post ) {
			$coupon = new WC_Coupon( $post->ID );
			$email_restrictions = $coupon->get_email_restrictions();

			if ( in_array( $email, $email_restrictions, true ) ) {
				$total_credit += $coupon->get_amount();
			}
		}

		return $total_credit;
	}

	/**
	 * Delete a coupon.
	 *
	 * @since 1.0.0
	 * @param int $coupon_id Coupon ID.
	 * @return void
	 */
	public static function delete_coupon( $coupon_id ) {
		wp_delete_post( $coupon_id, true );
	}

	/**
	 * Generate a coupon code.
	 *
	 * @since 1.0.0
	 * @param string $prefix Code prefix.
	 * @param int    $length Code length (excluding prefix).
	 * @return string Generated code.
	 */
	public static function generate_coupon_code( $prefix = '', $length = 8 ) {
		$code = wp_generate_password( $length, false );

		if ( ! empty( $prefix ) ) {
			$code = $prefix . '-' . $code;
		}

		return strtoupper( $code );
	}

	/**
	 * Expire a coupon.
	 *
	 * @since 1.0.0
	 * @param \WC_Coupon $coupon Coupon object.
	 * @return void
	 */
	public static function expire_coupon( $coupon ) {
		$coupon->set_date_expires( time() - DAY_IN_SECONDS );
		$coupon->save();
	}

	/**
	 * Set coupon validity.
	 *
	 * @since 1.0.0
	 * @param \WC_Coupon $coupon Coupon object.
	 * @param int        $validity Validity period.
	 * @param string     $suffix Period suffix (days/weeks/months/years).
	 * @return void
	 */
	public static function set_coupon_validity( $coupon, $validity, $suffix = 'days' ) {
		update_post_meta( $coupon->get_id(), 'sc_coupon_validity', $validity );
		update_post_meta( $coupon->get_id(), 'validity_suffix', $suffix );
	}

	/**
	 * Check if coupon can auto-apply.
	 *
	 * @since 1.0.0
	 * @param \WC_Coupon $coupon Coupon object.
	 * @return bool
	 */
	public static function can_auto_apply( $coupon ) {
		$auto_apply = get_post_meta( $coupon->get_id(), 'wc_sc_auto_apply_coupon', true );
		return 'yes' === $auto_apply;
	}
}
