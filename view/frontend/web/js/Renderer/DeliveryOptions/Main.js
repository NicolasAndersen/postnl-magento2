/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
define([
    'uiComponent',
    'ko',
    'TIG_PostNL/js/Helper/State',
    'TIG_PostNL/js/Helper/AddressFinder'
], function (
    Component,
    ko,
    State,
    AddressFinder
) {
    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/DeliveryOptions/Main',
            shipmentType: State.currentOpenPane
        },

        initObservable: function () {
            this._super().observe([
                'shipmentType'
            ]);

            this.isLoading = State.isLoading;

            return this;
        },

        canUseDeliveryOptions: ko.computed(function () {
            var address = AddressFinder();
            var deliveryOptionsAreAvailable = State.deliveryOptionsAreAvailable();
            var isNL = (address && address !== false && address.countryCode == 'NL');

            return deliveryOptionsAreAvailable && isNL;
        }),

        canUsePickupLocations: ko.computed(function () {
            var isActive = window.checkoutConfig.shipping.postnl.pakjegemak_active;
            return isActive == 1 && State.pickupOptionsAreAvailable() && AddressFinder() !== false;
        }),

        setDelivery: function () {
            State.currentOpenPane('delivery');
        },

        setPickup: function () {
            State.currentOpenPane('pickup');
        }
    });
});
