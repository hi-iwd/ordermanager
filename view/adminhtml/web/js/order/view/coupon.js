define([
        'jquery',
        'IWD_OrderManager/js/order/view/coupon',
        'jquery/ui'
    ],

    function ($) {
        'use strict';

        $.widget('mage.iwdOrderManagerCoupon', $.mage.iwdOrderManagerActions, {
            options: {
                cancelButtonId: '#iwd_om_coupon_remove',
                updateButtonId: '#iwd_om_coupon_apply',

                couponCode: '#iwd_om_coupon',

                blockId: '#iwd-om-coupon-section',
                formBlockId: '#iwd-om-coupon-section',
                resultFormId: '',

                scrollToTopAfterAction: false
            },
            couponCode: '',

            init: function () {
                this.moveSectionToNeededPlace();
                this.initCouponField();
            },
            moveSectionToNeededPlace: function() {
                var section = $('#iwd-om-coupon-section');
                $('.order-totals').closest('section').before($(section));
                $(section).show();
            },
            initCouponField: function() {
                var self = this;

                this.couponCode = $(self.options.couponCode).val();
                self.disableApplyButton();

                $(document).on('keyup change', self.options.couponCode, function () {
                    var couponCode = $(self.options.couponCode).val();
                    if (couponCode.length == 0 || self.couponCode == couponCode) {
                        self.disableApplyButton();
                    } else {
                        self.enableApplyButton();
                    }
                });
            },

            enableApplyButton: function () {
                $(this.options.updateButtonId).removeClass('disabled').removeAttr('disabled');
            },

            disableApplyButton: function () {
                $(this.options.updateButtonId).addClass('disabled').attr('disabled', 'disabled');
            }
        });

        return $.mage.iwdOrderManagerCoupon;
    });