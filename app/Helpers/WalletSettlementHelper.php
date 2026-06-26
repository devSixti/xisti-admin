<?php

namespace App\Helpers;

use App\Classes\NotificationClass;
use App\Models\TransportDriverDetails;
use App\Models\TransportRideBook;

class WalletSettlementHelper
{
    /**
     * Debit platform commission from driver wallet on cash rides (auto-settle mode).
     */
    public static function settleDriverCommissionOnCashRide(
        TransportRideBook $ride,
        NotificationClass $notificationClass,
        ?object $generalSettings
    ): bool {
        if ((int) ($ride->driver_pay_settle_status ?? 0) === 1) {
            return true;
        }
        if (!$generalSettings || (int) ($generalSettings->auto_settle_wallet ?? 0) !== 1) {
            return false;
        }

        $driverData = TransportDriverDetails::query()
            ->select('transport_driver_details.user_id as provider_id')
            ->where('transport_driver_details.user_id', $ride->driver_id)
            ->first();

        if ($driverData === null) {
            return false;
        }

        $providerId = $driverData->provider_id;
        $walletProviderType = 0;
        $transactionType = 2;
        $storedCommission = (float) $ride->admin_commission;
        if ($storedCommission > 0) {
            $vatRate = (float) ($generalSettings->vat_rate_on_commission ?? 19);
            $amount = round($storedCommission + ($storedCommission * ($vatRate / 100)), 2);
        } else {
            $tripValue = (float) ($ride->total_pay > 0 ? $ride->total_pay : $ride->offered_price);
            $breakdown = RideInvoiceHelper::breakdownForRide($ride, $generalSettings);
            $amount = (float) ($breakdown['total_deduction'] ?? 0);
        }
        $subject = 'Admin Debited commission + VAT - # ' . $ride->ride_no;
        $subjectCode = 16;

        if ($amount <= 0) {
            $transactionType = 1;
            $subjectCode = 15;
            $subject = 'Credited by Admin -  Booking # ' . $ride->ride_no;
            $amount = abs($amount);
        }

        $updated = $notificationClass->providerUpdateWalletBalance(
            $providerId,
            $walletProviderType,
            $transactionType,
            $amount,
            $subject,
            $subjectCode,
            $ride->ride_no
        );

        if ($updated) {
            $ride->driver_pay_settle_status = 1;
            $ride->save();
        }

        return (bool) $updated;
    }
}
