@component('mail::message')
@if($enabled)
# İki Faktörlü Doğrulama Etkinleştirildi

Merhaba {{ $user->name }},

Hesabınız için iki faktörlü doğrulama **{{ $changedAt }}** tarihinde etkinleştirildi.
@else
# İki Faktörlü Doğrulama Devre Dışı Bırakıldı

Merhaba {{ $user->name }},

Hesabınız için iki faktörlü doğrulama **{{ $changedAt }}** tarihinde devre dışı bırakıldı.
@endif

@component('mail::panel')
**IP Adresi:** {{ $ipAddress }}<br>
**Tarayıcı:** {{ $userAgent }}
@endcomponent

Bu işlemi siz yapmadıysanız, lütfen derhal hesabınızın güvenliğini kontrol edin.

Saygılarımızla,<br>
{{ site_name() }}
@endcomponent
