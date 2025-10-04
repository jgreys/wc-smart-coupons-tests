# WooCommerce Smart Coupons - Plugin Analysis

**Plugin Version:** 9.7.0
**Analysis Date:** 2025-10-04
**Purpose:** Document actual plugin implementation for framework accuracy

---

## Table of Contents

1. [Overview](#overview)
2. [Core Concepts](#core-concepts)
3. [Meta Keys Reference](#meta-keys-reference)
4. [Framework Verification](#framework-verification)

---

## Overview

WooCommerce Smart Coupons extends WooCommerce's coupon system with store credit, gift certificates, and advanced coupon features. The plugin uses custom meta keys and extends the standard WC_Coupon functionality.

### Key Terminology

- **Store Credit** = Smart Coupon with `discount_type: 'smart_coupon'` and `is_gift = 'no'` (stored on ORDER)
- **Gift Certificate** = Smart Coupon with `discount_type: 'smart_coupon'` and `is_gift = 'yes'` (stored on ORDER)
- Both types use the **same coupon discount_type**, differentiated by the purchase workflow, not coupon properties

---

## Core Concepts

### Discount Type
```php
'discount_type' => 'smart_coupon'  // Used for both Store Credit and Gift Certificates
```

### Store Credit vs Gift Certificate
The distinction is made during **order processing**, not at coupon level:
- **Order Meta `is_gift`**: `'yes'` = Gift Certificate, `'no'` = Store Credit
- Coupon itself only knows it's a `smart_coupon` type

---

## Meta Keys Reference

### Coupon Meta Keys

| Meta Key | Type | Description | Example |
|----------|------|-------------|---------|
| `sc_disable_email_restriction` | string | Disable email restriction | `'yes'`, `'no'` |
| `wc_sc_coupon_receiver_details` | array | Receiver email, name, message | `array('email' => '...', 'name' => '...', 'message' => '...')` |
| `sc_coupon_validity` | int | Validity duration | `30` |
| `validity_suffix` | string | Validity unit | `'days'`, `'weeks'`, `'months'`, `'years'` |
| `wc_sc_auto_apply_coupon` | string | Auto-apply on cart | `'yes'`, `'no'` |
| `auto_generate_coupon` | string | Generate coupon via URL | `'yes'`, `'no'` |
| `_coupon_title` | string | Template for generated coupons | `'Generated Coupon'` |
| `wc_sc_max_discount` | float | Maximum discount amount | `100.00` |
| `wc_sc_user_role_ids` | array | Allowed user role IDs | `array(1, 2, 3)` |
| `wc_sc_exclude_user_role_ids` | array | Excluded user role IDs | `array(4, 5)` |

### Order Meta Keys

| Meta Key | Type | Description | Example |
|----------|------|-------------|---------|
| `is_gift` | string | Is this a gift certificate? | `'yes'`, `'no'` |
| `gift_receiver_message` | string | Gift message | `'Happy Birthday!'` |
| `wc_sc_coupon_receiver_details` | array | Gift receiver details | Same structure as coupon meta |

### Product Meta Keys

| Meta Key | Type | Description |
|----------|------|-------------|
| `_coupon_title` | string | Coupon title template for product-generated coupons |

---

## Framework Verification

### ✅ Verified Correct

All meta keys in the framework have been verified against the actual plugin:

#### Helper Methods
- ✅ `create_store_credit()` - Uses correct meta keys
- ✅ `create_gift_certificate()` - Uses `wc_sc_coupon_receiver_details` (array)
- ✅ Auto-apply - Uses `wc_sc_auto_apply_coupon`
- ✅ Validity - Uses `sc_coupon_validity` + `validity_suffix`
- ✅ Email restriction - Uses `sc_disable_email_restriction`

#### Assertions
- ✅ `assertIsGiftCertificate()` - Checks `wc_sc_coupon_receiver_details`
- ✅ `assertEmailRestrictionDisabled()` - Checks `sc_disable_email_restriction`
- ✅ `assertAutoApplies()` - Checks `wc_sc_auto_apply_coupon`
- ✅ `assertCouponValidity()` - Checks `sc_coupon_validity` + `validity_suffix`

### Important Notes

1. **No `is_gift_card` meta** - This doesn't exist at coupon level
2. **Gift distinction** - Made via ORDER meta `is_gift`, not coupon meta
3. **Receiver details** - Stored as array with `email`, `name`, `message` keys
4. **Validity format** - Uses numeric value + suffix (days/weeks/months/years)

---

## Source Files Analyzed

- `includes/class-wc-sc-admin-welcome.php` - Coupon meta handling
- `includes/class-wc-sc-purchase-credit.php` - Gift certificate workflow
- `includes/class-wc-sc-coupon-receiver-details.php` - Receiver details structure
- `includes/class-wc-sc-display-coupons.php` - Auto-apply implementation
- `includes/class-wc-smart-coupons.php` - Core Smart Coupon functionality

---

**Status:** ✅ Framework verified accurate against plugin v9.7.0
