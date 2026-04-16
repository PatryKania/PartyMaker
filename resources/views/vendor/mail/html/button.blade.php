@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
@php
    $sharedColor = view()->getShared()['btnColor'] ?? null;
    
    $style = $sharedColor 
        ? "background-color: {$sharedColor}; 
           border-top: 8px solid {$sharedColor}; 
           border-right: 18px solid {$sharedColor}; 
           border-bottom: 8px solid {$sharedColor}; 
           border-left: 18px solid {$sharedColor};" 
        : "";
@endphp

<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}" class="button button-{{ $color }}" style="{{ $style }}" target="_blank" rel="noopener">{!! $slot !!}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
