<?php

namespace App\Domains\Order\Entities;

class StoreOrderPaymentEntity
{
    // 'order_id' => $storedOrder->id,
    // 'payer_name' => $requestPayment->payer_name,
    // 'paid_amount' => $requestPayment->paid_amount,
    // 'change_amount' => $requestPayment->change_amount,
    // 'payment_type' => $requestPayment->payment_type,

    public string $order_id;
    public string $payer_name;
    public int $paid_amount;
    public int $change_amount;
    public string $payment_type;
    public function __construct(object $params) {
        $this->order_id = $params->order_id;
        $this->payer_name = $params->payer_name;
        $this->paid_amount = $params->paid_amount;
        $this->change_amount = $params->change_amount;
        $this->payment_type = $params->payment_type;

        return $this;
    }
}
