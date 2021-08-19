define([
        'jquery',
        'Magento_Ui/js/modal/alert',
        'mage/translate',
        'jquery/ui'
    ],

    function($, modal){
        'use strict';

        $.widget('mage.iwdOrderManagerUpgrade', {
            options: {
                upgradeClass: '.iwd-upgrade-to-pro, #order-items-update, #om-edit-payment-update, #om-edit-shipping-update'
            },

            init: function() {
                var self = this;
                $(document).off('click touchstart', this.options.upgradeClass);
                $(document).on('click touchstart', this.options.upgradeClass, (function(e) {
                    e.preventDefault();
                    self.upgradePopup();
                }));
            },

            upgradePopup: function(){
                modal({
                    title: '',
                    content: '<div class="iwd-logo"></div><div class="iwd-ext-title">Order Manager</div>' +
                    '<div class="iwd-ext-desc">IWD\'s Order Manager enhances your order processing experience through powerful features and faster editing. Upgrade to PRO today for these great features: </div>' +
                    '<ul class="iwd-ext-pro-features">' +
                    '<li>Manage Purchased Items</li>' +
                    '<li>Update Payment & Shipping</li>' +
                    '<li>Edit Invoices, Shipments & Credit Memos</li>' +
                    '<li>Manage Multiple Inventories</li>' +
                    '<li>Sales Reps</li>' +
                    '<li>Re-authorize with Authorize.Net CIM</li>' +
                    '</ul>',
                    modalClass: "iwd-order-manager-unlock-pro",
                    clickableOverlay: false,
                    buttons:[]
                })
            }
        });

        return $.mage.iwdOrderManagerUpgrade;
    });