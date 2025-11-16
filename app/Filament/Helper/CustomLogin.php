<?php

namespace App\Filament\Helper;

use Filament\Auth\Pages\Login;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Text;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\RenderHook;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Support\Htmlable;

class CustomLogin extends Login
{
    public static string $layout = 'filament.auth.layout';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                // $this->getRememberFormComponent(),
                Text::make(new HtmlString('<a href="" class="btn-forget btn-link" >Forgot your password?</a>'))
            ]);
    }

    public function getSubheading(): string | Htmlable | null
    {
        $html = '
        <div class="social-wrapper">

            <a href="/auth/redirect/google"
               class="social-btn google-btn fi-btn fi-size-md  fi-ac-btn-action" />
               <img src="/svg/google_logo.svg"/>
                <span>Sign in with Google</span>
            </a>

            <a href="/auth/redirect/facebook"
               class="social-btn fb-btn fi-btn fi-size-md  fi-ac-btn-action">
               <img src="/svg/fb_logo.svg"/>
                <span>Sign in with Facebook</span>
            </a>
        <div class="divider">or</div>
        </div>
    ';

        return new HtmlString($html);
    }

    public function getRegisterPageRedirectComponent(): Htmlable | null
    {
        $html = '<div class="register-info">Don’t have an account yet?' . ' ' . ' <a class="btn-link" href=' . $this->registerAction->getUrl() . '>Register here.</a></div>';
        return new HtmlString($html);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                RenderHook::make(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE),
                $this->getFormContentComponent(),
                $this->getMultiFactorChallengeFormContentComponent(),
                $this->getRegisterPageRedirectComponent(),
                RenderHook::make(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER),
            ]);
    }
}
