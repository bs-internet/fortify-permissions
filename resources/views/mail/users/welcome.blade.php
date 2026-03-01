@component('mail::message')
# Hoş Geldiniz, {{ $user->name }}

Yönetim panelinde hesabınız oluşturuldu. Sisteme giriş yapabilmek için aşağıdaki bağlantıya tıklayarak şifrenizi belirleyin.

@component('mail::button', ['url' => $resetUrl])
Şifre Belirle
@endcomponent

Bu bağlantı **60 dakika** boyunca geçerlidir. Süre dolduktan sonra giriş sayfasındaki "Şifremi Unuttum" bağlantısını kullanabilirsiniz.

Saygılarımızla,<br>
{{ site_name() }}
@endcomponent
