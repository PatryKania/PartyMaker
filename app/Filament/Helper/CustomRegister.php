<?php

namespace App\Filament\Helper;

use Filament\Auth\Pages\Register;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Text;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\RenderHook;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Support\Htmlable;

class CustomRegister extends Register
{
    public static string $layout = 'filament.auth.layout';


    public function getSubheading(): string | Htmlable | null
    {
        $html = '
        <div class="social-wrapper">

          <a href="/auth/google/redirect"
               class="social-btn google-btn fi-btn fi-size-md  fi-ac-btn-action" />
               <img src="/svg/google_logo.svg"/>
                <span>Sign up with Google</span>
            </a>

            <a href="/auth/facebook/redirect"
               class="social-btn fb-btn fi-btn fi-size-md  fi-ac-btn-action">
                 <img src="/svg/fb_logo.svg"/>
                <span>Sign up with Facebook</span>
            </a>
        <div class="divider">or</div>
        </div>
    ';

        return new HtmlString($html);
    }

    public function getLoginPageRedirectComponent(): Htmlable | null
    {
        $html = '<div class="register-info">Already have an account?' . ' ' . ' <a class="btn-link" href=' . $this->loginAction->getUrl() . '>Log in here.</a></div>';
        return new HtmlString($html);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                RenderHook::make(PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE),
                $this->getFormContentComponent(),
                $this->getLoginPageRedirectComponent(),
                RenderHook::make(PanelsRenderHook::AUTH_REGISTER_FORM_AFTER),
            ]);
    }
}
