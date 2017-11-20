@component('mail.message', ['url' => ''])
    @component('mail.button', ['url' => ''])
    View Order
    @endcomponent

 <img src="{{ $mailer->embed($icon) }}">
@endcomponent

