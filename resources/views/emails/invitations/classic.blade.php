<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="background-color: #f3f4f6; padding: 40px 20px; margin: 0; font-family: 'Georgia', serif;">
    
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 50px 40px; border: 1px solid #e5e7eb; border-top: 6px solid {{ $primaryColor }}; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <div style="text-align: center; margin-bottom: 40px; border-bottom: 1px solid #f3f4f6; padding-bottom: 30px;">
            
            <h1 style="font-size: 24px; letter-spacing: 4px; color: {{ $primaryColor }}; margin-bottom: 15px; font-weight: normal; text-transform: uppercase;">
                {{ __('Invitation') }}
            </h1>
            
            <p style="font-size: 14px; color: #6b7280; margin: 0; font-style: italic;">{{ __('for') }}</p>
            
            <h2 style="font-size: 26px; color: #111827; margin: 5px 0 20px 0; font-weight: bold;">
                {{ $guestName }}
            </h2>

            <p style="font-size: 14px; color: #6b7280; margin: 0; font-style: italic;">{{ __('to the event') }}</p>
            
            <h3 style="font-size: 20px; color: #1f2937; margin: 5px 0 0 0; font-weight: normal;">
                {{ $eventName }}
            </h3>
            
        </div>

        <div style="text-align: center; line-height: 1.8; color: #374151; font-size: 15px;">
            {!! $content !!}
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ $loginUrl }}" style="background-color: {{ $primaryColor }}; color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 4px; font-weight: bold; font-family: Arial, sans-serif; display: inline-block; letter-spacing: 1px;">
                {{ __('Log in to the app') }}
            </a>
        </div>
    </div>

</body>
</html>