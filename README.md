# WooCommerce Smart Coupons PHPUnit Framework

A comprehensive PHPUnit testing framework for WooCommerce Smart Coupons extensions. This package provides helper classes, mock objects, and custom assertions to make testing Smart Coupons functionality easier and more reliable.

## Features

- **Base Test Case**: Specialized `UnitTestCase` with Smart Coupons-specific setup
- **Helper Classes**: Create store credit, gift cards, combination coupons, and more
- **Mock Objects**: Test without requiring the full Smart Coupons plugin
- **Custom Assertions**: Domain-specific assertions for coupon testing
- **Realistic Test Data**: Examples using production-like data patterns

## Installation

```bash
composer require --dev greys/woocommerce-smart-coupons-phpunit-framework
```

## Requirements

- PHP 7.4 or higher
- PHPUnit 9.x
- WooCommerce
- `greys/woocommerce-phpunit-framework`

## Quick Start

### 1. Extend the Base Test Case

```php
<?php
namespace YourPlugin\Tests;

use Greys\WooCommerce\SmartCoupons\Tests\UnitTestCase;

class YourSmartCouponTest extends UnitTestCase {

    public function test_store_credit_creation() {
        // Arrange.
        $args = array(
            'code'           => 'CREDIT-12345',
            'amount'         => 50,
            'customer_email' => 'customer@example.com',
        );

        // Act.
        $coupon = $this->create_store_credit( $args );

        // Assert.
        $this->assertIsSmartCoupon( $coupon );
        $this->assertCouponBalance( 50, $coupon );
    }
}
```

### 2. Use Helper Classes

```php
<?php
use Greys\WooCommerce\SmartCoupons\Tests\Helpers\Coupon;

// Create store credit
$coupon = Coupon::create_store_credit( array(
    'code'           => 'CREDIT-12345',
    'amount'         => 100,
    'customer_email' => 'john@example.com',
) );

// Create gift card
$gift = Coupon::create_gift_card( array(
    'code'           => 'GIFT-67890',
    'amount'         => 75,
    'receiver_email' => 'jane@example.com',
    'message'        => 'Happy Birthday!',
) );

// Create combination coupon
$combo = Coupon::create_combination_coupon( array(
    'code'                  => 'COMBO-11111',
    'amount'                => 20,
    'coupon_to_generate'    => array( 'CREDIT-50' ),
    'number_of_coupons_gen' => 2,
) );

// Create URL coupon
$url_coupon = Coupon::create_url_coupon( array(
    'code'       => 'URL-22222',
    'amount'     => 15,
    'url'        => home_url( '/special-offer' ),
    'unique_url' => 'yes',
) );
```

### 3. Use Custom Assertions

```php
<?php
// Assert coupon type
$this->assertIsSmartCoupon( $coupon );
$this->assertIsGiftCard( $gift_coupon );

// Assert balance
$this->assertCouponBalance( 100, $coupon );
$this->assertRemainingBalance( 50, $coupon );

// Assert restrictions
$this->assertCouponRestrictedToEmail( 'customer@example.com', $coupon );
$this->assertCouponValidForProducts( array( 123, 456 ), $coupon );

// Assert generation settings
$this->assertCouponGeneratesCoupons( $combo_coupon );
$this->assertNumberOfCouponsToGenerate( 2, $combo_coupon );

// Assert URL settings
$this->assertIsUrlCoupon( $url_coupon );
$this->assertCouponUrl( home_url( '/shop' ), $url_coupon );

// Assert customer balance
$this->assertCustomerCreditBalance( 150.00, $customer_id );
```

## Available Helper Methods

### Coupon

#### Store Credit
```php
// Create store credit
create_store_credit( $args = array() )

// Apply credit to customer
apply_credit_to_customer( $customer_id, $amount, $args = array() )

// Use store credit
use_store_credit( $coupon, $amount )

// Get customer credit balance
get_customer_credit_balance( $customer_id )
```

#### Gift Cards
```php
// Create gift card
create_gift_card( $args = array() )
```

#### Combination Coupons
```php
// Create combination coupon
create_combination_coupon( $args = array() )
```

#### URL Coupons
```php
// Create URL coupon
create_url_coupon( $args = array() )
```

#### Product Credit
```php
// Create product credit coupon
create_product_credit( $args = array() )
```

#### Utilities
```php
// Delete coupon
delete_coupon( $coupon_id )

// Generate coupon code
generate_coupon_code( $prefix = 'SC', $length = 10 )

// Expire coupon
expire_coupon( $coupon )

// Set coupon validity
set_coupon_validity( $coupon, $days )

// Check if can auto-apply
can_auto_apply( $coupon )
```

## Complete Test Example

```php
<?php
/**
 * Tests for Smart Coupon functionality.
 *
 * @package YourPlugin\Tests
 */

namespace YourPlugin\Tests;

use Greys\WooCommerce\SmartCoupons\Tests\UnitTestCase;
use Greys\WooCommerce\SmartCoupons\Tests\Helpers\Coupon;

/**
 * Smart Coupon Test class.
 */
class SmartCouponTest extends UnitTestCase {

	/**
	 * Test store credit can be applied to customer.
	 *
	 * @return void
	 */
	public function test_apply_store_credit_to_customer() {
		// Arrange.
		$customer = $this->factory->customer->create( array(
			'email' => 'customer@example.com',
		) );
		$amount = 100.00;

		// Act.
		$coupon = Coupon::apply_credit_to_customer( $customer, $amount );

		// Assert.
		$this->assertIsSmartCoupon( $coupon );
		$this->assertCouponBalance( 100.00, $coupon );
		$this->assertCouponRestrictedToEmail( 'customer@example.com', $coupon );
		$this->assertCustomerCreditBalance( 100.00, $customer );
	}

	/**
	 * Test gift card creation with receiver details.
	 *
	 * @return void
	 */
	public function test_gift_card_has_receiver_details() {
		// Arrange.
		$args = array(
			'amount'         => 75,
			'receiver_email' => 'receiver@example.com',
			'message'        => 'Happy Birthday!',
		);

		// Act.
		$coupon = Coupon::create_gift_card( $args );

		// Assert.
		$this->assertIsGiftCard( $coupon );
		$this->assertCouponBalance( 75, $coupon );
		$this->assertSame( 'receiver@example.com', $coupon->get_meta( 'gift_receiver_email' ) );
		$this->assertSame( 'Happy Birthday!', $coupon->get_meta( 'gift_receiver_message' ) );
	}

	/**
	 * Test combination coupon generates other coupons.
	 *
	 * @return void
	 */
	public function test_combination_coupon_generation_settings() {
		// Arrange.
		$args = array(
			'amount'                => 20,
			'coupon_to_generate'    => array( 'CREDIT-50' ),
			'number_of_coupons_gen' => 3,
		);

		// Act.
		$coupon = Coupon::create_combination_coupon( $args );

		// Assert.
		$this->assertCouponGeneratesCoupons( $coupon );
		$this->assertNumberOfCouponsToGenerate( 3, $coupon );
		$this->assertContains( 'CREDIT-50', $coupon->get_meta( 'wc_sc_coupon_to_generate' ) );
	}

	/**
	 * Test URL coupon has correct URL settings.
	 *
	 * @return void
	 */
	public function test_url_coupon_settings() {
		// Arrange.
		$url = home_url( '/special-offer' );
		$args = array(
			'url'        => $url,
			'unique_url' => 'yes',
		);

		// Act.
		$coupon = Coupon::create_url_coupon( $args );

		// Assert.
		$this->assertIsUrlCoupon( $coupon );
		$this->assertCouponUrl( $url, $coupon );
		$this->assertSame( 'yes', $coupon->get_meta( 'auto_generate_coupon' ) );
	}

	/**
	 * Test using partial store credit.
	 *
	 * @return void
	 */
	public function test_use_partial_store_credit() {
		// Arrange.
		$coupon = Coupon::create_store_credit( array(
			'amount' => 100,
		) );

		// Act.
		$result = Coupon::use_store_credit( $coupon, 30 );

		// Assert.
		$this->assertTrue( $result );
		$this->assertCouponBalance( 70, $coupon );
	}

	/**
	 * Test using more credit than available fails.
	 *
	 * @return void
	 */
	public function test_use_more_credit_than_available_fails() {
		// Arrange.
		$coupon = Coupon::create_store_credit( array(
			'amount' => 50,
		) );

		// Act.
		$result = Coupon::use_store_credit( $coupon, 75 );

		// Assert.
		$this->assertFalse( $result );
		$this->assertCouponBalance( 50, $coupon );
	}

	/**
	 * Test product credit coupon settings.
	 *
	 * @return void
	 */
	public function test_product_credit_coupon() {
		// Arrange.
		$product = $this->factory->product->create( array(
			'sku' => 'PRODUCT-123',
		) );
		$args = array(
			'product_ids' => array( $product ),
		);

		// Act.
		$coupon = Coupon::create_product_credit( $args );

		// Assert.
		$this->assertIsSmartCoupon( $coupon );
		$this->assertCouponValidForProducts( array( $product ), $coupon );
		$this->assertCouponPicksPriceOfProduct( $coupon );
	}

	/**
	 * Test coupon validity period.
	 *
	 * @return void
	 */
	public function test_coupon_validity_period() {
		// Arrange.
		$coupon = Coupon::create_store_credit();
		$days   = 30;

		// Act.
		Coupon::set_coupon_validity( $coupon, $days );

		// Assert.
		$this->assertCouponValidity( 30, $coupon );
		$this->assertNotNull( $coupon->get_date_expires() );
	}

	/**
	 * Test multiple credits sum to customer balance.
	 *
	 * @return void
	 */
	public function test_multiple_credits_sum_to_balance() {
		// Arrange.
		$customer = $this->factory->customer->create( array(
			'email' => 'multi@example.com',
		) );

		// Act.
		Coupon::apply_credit_to_customer( $customer, 50 );
		Coupon::apply_credit_to_customer( $customer, 75 );
		Coupon::apply_credit_to_customer( $customer, 25 );

		// Assert.
		$this->assertCustomerCreditBalance( 150.00, $customer );
	}
}
```

## Testing Tips

### 1. Clean Up After Tests
```php
protected function tearDown(): void {
	// Delete test coupons
	foreach ( $this->coupon_ids as $id ) {
		Coupon::delete_coupon( $id );
	}

	parent::tearDown();
}
```

### 2. Test Edge Cases
```php
// Test with zero amounts
$coupon = Coupon::create_store_credit( array( 'amount' => 0 ) );

// Test with very large amounts
$coupon = Coupon::create_store_credit( array( 'amount' => 999999.99 ) );

// Test with expired coupons
Coupon::expire_coupon( $coupon );
```

### 3. Test Customer Restrictions
```php
// Test email restrictions
$this->assertCouponRestrictedToEmail( 'specific@example.com', $coupon );

// Test product restrictions
$this->assertCouponValidForProducts( array( 123, 456 ), $coupon );
```

### 4. Test Generation Logic
```php
// Test coupon generates other coupons
$this->assertCouponGeneratesCoupons( $combo_coupon );
$this->assertNumberOfCouponsToGenerate( 5, $combo_coupon );
```

## Common Issues

### Issue: Mock Smart Coupon class not found
**Solution**: Ensure your bootstrap file loads the mock:
```php
if ( ! class_exists( 'WC_Smart_Coupon' ) ) {
    require_once $this->framework_dir . '/src/mocks/class-wc-smart-coupon-mock.php';
}
```

### Issue: Coupon not saving meta data
**Solution**: Call `$coupon->save()` after setting meta:
```php
$coupon->update_meta_data( 'is_gift_card', 'yes' );
$coupon->save();
```

### Issue: Customer balance calculation incorrect
**Solution**: Ensure only unused coupons are counted:
```php
if ( $coupon->get_usage_count() < $coupon->get_usage_limit() ) {
    $total_credit += floatval( $coupon->get_amount() );
}
```

## WooCommerce Coding Standards

This framework follows WooCommerce coding standards:

- **Indentation**: Use tabs, not spaces
- **Arrays**: Use `array()` instead of `[]`
- **PHPDoc**: Include `@package`, `@since`, `@return` tags
- **Comments**: End with periods ("// Arrange." not "// Arrange")
- **Naming**: Use snake_case for functions and variables

## Contributing

Contributions are welcome! Please:
- Fork the repository
- Create a feature branch
- Submit a pull request with tests

## License

MIT License - See LICENSE file for details.

## Support

For questions about using this framework:
- Check the examples in this README
- Review the test files in your project
- Consult the WooCommerce PHPUnit Framework documentation
