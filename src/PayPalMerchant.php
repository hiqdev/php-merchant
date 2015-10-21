<?php

namespace hiqdev\php\merchant;

class PayPalMerchant extends Merchant
{
    public function getInputs()
    {
        return [
            'cmd'           => '_xclick',
            'bn'            => 'PP-BuyNowBF:btn_paynowCC_LG.gif:NonHostedGuest',
            'notify_url'    => $this->confirmUrl,
            'return'        => $this->successUrl,
            'business'      => $this->purse,
            'item_number'   => 1,
            'item_name'     => $this->description,
            'amount'        => $this->total,
        ];
    }

    public function validateConfirmation($data)
    {
    }
}
