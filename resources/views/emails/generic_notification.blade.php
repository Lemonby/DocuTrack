<x-mail::message>
# Halo,

{{ $messageBody }}

@if($ctaUrl && $ctaText)
<x-mail::button :url="$ctaUrl">
{{ $ctaText }}
</x-mail::button>
@endif

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
