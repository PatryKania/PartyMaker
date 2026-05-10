<!DOCTYPE html>
<html>
<body style="background-color: #f3f4f6; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; padding: 40px 20px; margin: 0;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; overflow: hidden;">
        
        <div style="padding: 40px;">
            
            <div style="border: 3px solid {{ $primaryColor ?? '#3b82f6' }}; border-radius: 16px; padding: 35px 20px; text-align: center; margin-bottom: 40px; background-color: #ffffff;">
                
                <div style="display: inline-block; background-color: #f3f4f6; color: {{ $primaryColor ?? '#3b82f6' }}; padding: 6px 16px; border-radius: 9999px; font-size: 13px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 25px;">
                    {{ __('Invitation') }}
                </div>
                
                <p style="font-size: 12px; color: #9ca3af; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">{{ __('for') }}</p>
                
                <h2 style="font-size: 32px; color: #111827; margin: 0 0 25px 0; font-weight: 800; letter-spacing: -1px;">
                    {{ $guestName }}
                </h2>

                <p style="font-size: 12px; color: #9ca3af; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">{{ __('to the event') }}</p>
                
                <h3 style="font-size: 24px; color: {{ $primaryColor ?? '#3b82f6' }}; margin: 0; font-weight: 700;">
                    {{ $eventName }}
                </h3>
                
            </div>

            <div style="text-align: center; line-height: 1.8; color: #4b5563; font-size: 16px;">
                {!! $content !!}
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ $loginUrl ?? '#' }}" style="display: inline-block; background-color: {{ $primaryColor ?? '#3b82f6' }}; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 16px;">
                    {{ __('Log in to the app') }}
                </a>
            </div>

        </div>

    </div>

</body>
</html>