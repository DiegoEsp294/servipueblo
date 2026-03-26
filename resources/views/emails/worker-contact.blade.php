@component('mail::message')
# 📲 Alguien quiere contactarte

Hola **{{ $worker->name }}**, alguien vio tu perfil en ServiPueblo y tocó el botón de WhatsApp para contactarte.

Revisá tu WhatsApp por si ya te escribieron. Si todavía no recibiste nada, es posible que estén por hacerlo.

@component('mail::button', ['url' => route('workers.show', $worker->slug), 'color' => 'green'])
Ver mi perfil
@endcomponent

Si querés actualizar tu disponibilidad o datos, contactanos a **servipueblosoporte@gmail.com**

Gracias,<br>
**ServiPueblo**
@endcomponent
