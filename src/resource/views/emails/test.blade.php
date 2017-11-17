<h3>{{$test}} test emial</h3>

@component('mail.button', ['url' => ''])
View Order
@endcomponent

<img src="{{ $mailer->embed(load_img('favicon.ico')) }}">