<?php

function total_fee_confirm($payMoney, $exception = '')
{
    if (($info['total']*1000) == ($payMoney*1000))
    {
        $bool = true;
    }
    else
    {
        new Pay_Exception($exception . "(no:{$ordersn},订:{$info['total']},付:{$payMoney})");
    }
    return $bool;
}