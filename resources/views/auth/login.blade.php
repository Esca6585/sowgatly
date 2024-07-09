<!DOCTYPE html>
<html lang="{{ app()->getlocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('img/logo/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('img/logo/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('img/logo/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/logo/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('img/logo/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('img/logo/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('img/logo/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/logo/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/logo/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('img/logo/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/logo/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/logo/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('img/logo/manifest.json') }}">
    <meta name="msapplication-TileImage" content="{{ asset('img/logo/ms-icon-144x144.png') }}">

    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="{{ asset('css/auth-style.css') }}">
         
    <title>{{ __('Login') }} | {{ config('app.name') }}</title>
</head>
<body>
    <div class="container">
        <div class="forms">
            <div class="form login">
                <div class="form-top">
                    <span class="title">{{ __('Login') }}</span>
                    <a href="{{ route('main-page', app()->getlocale() ) }}">
                        <svg width="113" height="32" viewBox="0 0 113 32" xmlns="http://www.w3.org/2000/svg" fill="#fa4d1e" data-v-d92e36ae=""><path d="M30.5097 7.72414c-1.5882 0-2.6801.60188-3.375 1.70533-.6949 1.10343-.6949 1.60503-.6949 3.31033v.9028h-1.2904v2.4076h1.2904v9.0282h2.7795v-9.0282h2.8787v-2.4076h-2.8787v-.9028c0-1.8056.0992-2.1066 1.0919-2.5078.1985-.1003.3971-.1003.5956-.1003.2978 0 .5956.1003.7941.2006l.3971.2006V8.22571l-.0993-.10032c-.397-.30094-.8934-.40125-1.489-.40125zm3.2758-.20063h2.7795V25.0784h-2.7795V7.52351zM44.8041 13.442c-3.2758 0-5.8567 2.6082-5.8567 5.9185s2.5809 5.9185 5.8567 5.9185 5.8567-2.6082 5.8567-5.9185-2.5809-5.9185-5.8567-5.9185zm0 9.5298c-1.7868 0-3.1765-1.5047-3.1765-3.6113 0-2.1066 1.3897-3.6113 3.1765-3.6113s3.1765 1.5047 3.1765 3.6113c0 2.1066-1.3897 3.6113-3.1765 3.6113zm32.7578-2.2069l-1.9853-6.0188-.397-1.1035h-1.9854l-.2978 1.1035-1.9853 6.0188-2.3824-7.022.0993-.1003h-2.8787l.0992.2007-2.3824 6.9216-1.9853-6.0188-.3971-1.1035h-1.9853l-.4963 1.1035-1.9853 6.0188-2.3824-7.1223h-2.7795l4.2685 11.4358h1.6875l2.4817-6.6207 2.4816 6.6207h1.6875l2.8788-7.6239 2.8787 7.6239h1.6875l2.4817-6.6207 2.4816 6.6207h1.9853l4.3678-11.4358h-2.7795l-2.4817 7.1223zm11.6142-7.3229c-3.2758 0-5.8567 2.6082-5.8567 5.9185s2.5809 5.9185 5.8567 5.9185 5.8567-2.6082 5.8567-5.9185-2.5809-5.9185-5.8567-5.9185zm0 9.5298c-1.7868 0-3.1765-1.5047-3.1765-3.6113 0-2.1066 1.3897-3.6113 3.1765-3.6113s3.1765 1.5047 3.1765 3.6113c0 2.1066-1.3897 3.6113-3.1765 3.6113zm20.9449-9.3292l-2.382 7.1223-1.985-6.0188-.398-1.1035h-1.985l-.298 1.1035-1.985 6.0188-2.3823-7.1223h-2.7795l4.3678 11.4358h1.687l2.482-6.6207 2.482 6.6207h1.687L113 13.6426h-2.879zm-99.4645 7.3229l-.0993-6.2194c2.8787-.4013 4.9633-2.1066 6.0553-5.11601.9926-2.60815 1.1912-6.52037.8934-8.62696-1.6876.40126-4.2685 1.80565-5.0626 2.50784C11.8477 2.40752 10.458.80251 9.4653 0c-.99267.90282-2.38239 2.40752-2.97799 3.51097-.79413-.70219-3.37505-2.10658-5.06258-2.50784-.2978 2.10659-.09927 6.01881.8934 8.62696 1.09193 3.00941 3.17652 4.71471 6.05524 5.11601v6.3197s0-.1003-.09927-.1003C6.78511 18.558 3.90639 15.9498.13427 14.9467-.65986 21.5674 2.02033 29.3919 9.4653 32c7.445-2.6081 10.1252-10.5329 9.331-17.1536-3.7721 1.1034-6.5515 3.7116-8.1398 6.1191zm-8.43764-2.8088c2.87872 1.7054 6.15451 4.9154 6.05524 10.9342-3.67285-2.4075-5.65817-6.0188-6.05524-10.9342zm7.24644-5.6175c-4.66552 0-5.75745-4.21318-5.95598-8.42635 1.29046.7022 2.48166 1.70533 3.47432 2.80878.69486-1.5047 1.29046-2.60815 2.48166-3.81191 1.1912 1.20376 1.7868 2.30721 2.4817 3.81191.9926-1.00313 2.1838-2.10658 3.4743-2.70846-.1986 4.11285-1.2905 8.32603-5.956 8.32603zm1.1912 16.5517c0-6.0188 3.2758-9.3291 6.0552-10.9342-.2978 4.9154-2.2831 8.5267-6.0552 10.9342z"></path></svg>
                    </a>
                </div>

                <form method="POST" action="{{ route('login', app()->getLocale()) }}">
                    @csrf

                    <div class="input-field">
                        <input type="text" placeholder="{{ __('Email Address') }}" class="@error('email') is-invalid @enderror" name="email" autocomplete="on" required value="{{ old('email') }}">
                        <i class="uil uil-envelope icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.50" d="M1 6C1 4.34315 2.34315 3 4 3H20C21.6569 3 23 4.34315 23 6V18C23 19.6569 21.6569 21 20 21H4C2.34315 21 1 19.6569 1 18V6Z" fill="#fa4d1e"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.23177 7.35984C5.58534 6.93556 6.2159 6.87824 6.64018 7.2318L11.3598 11.1648C11.7307 11.4739 12.2693 11.4739 12.6402 11.1648L17.3598 7.2318C17.7841 6.87824 18.4147 6.93556 18.7682 7.35984C19.1218 7.78412 19.0645 8.41468 18.6402 8.76825L13.9205 12.7013C12.808 13.6284 11.192 13.6284 10.0794 12.7013L5.35981 8.76825C4.93553 8.41468 4.87821 7.78412 5.23177 7.35984Z" fill="#fa4d1e" />
                            </svg>
                        </i>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">
                            <div>{{ $message }}</div>
                        </div>
                    @enderror
                    <div class="input-field">
                        <input type="password" class="password @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}" name="password" autocomplete="on" required value="{{ old('password') }}" >
                        <i class="uil uil-lock icon">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <defs>
                                    <polygon id="path-1" points="0 0 24 0 24 24 0 24"></polygon>
                                </defs>
                                <g id="Stockholm-icons-/-General-/-Lock" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <mask id="mask-2" fill="white">
                                        <use xlink:href="#path-1"></use>
                                    </mask>
                                    <g id="bound"></g>
                                    <path opacity="0.50" d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" id="Mask" fill="#fa4d1e"></path>
                                </g>
                            </svg>
                        </i>
                        <i class="uil uil-eye-slash showHidePw">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g id="Stockholm-icons-/-General-/-Visible" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                    <path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" id="Shape" fill="#fa4d1e" fill-rule="nonzero" opacity="0.7"></path>
                                    <path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" id="Path" fill="#000000" opacity="0.3"></path>
                                </g>
                            </svg>
                        </i>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">
                            <div>{{ $message }}</div>
                        </div>
                    @enderror

                    <div class="checkbox-text">
                        <div class="checkbox-content">
                            <input type="checkbox" id="logCheck" checked>
                            <label for="logCheck" class="text">{{ __('Remember Me') }}</label>
                        </div>
                        
                        <a href="/{{ app()->getlocale() }}/password/reset" class="text">{{ __('Forgot Your Password?') }}</a>
                    </div>

                    <div class="input-field button">
                        <input type="submit" value="{{ __('Login') }}">
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">{{ __('Dont Have An Account?') }}
                        <a class="text signup-link">{{ __('Create an Account') }}</a>
                    </span>
                </div>

                <div class="login-signup">
                    <span class="text">
                        <a href="{{ route('main-page', app()->getlocale() ) }}" class="text signup-link">Türkmenstandartlary</a>
                    </span>
                </div>

                <div class="login-signup">
                    <select id="changeLanguageLogin">
                        @foreach (Config::get('languages') as $lang => $language)
                            <option value="{{ $lang }}" {{ $lang == app()->getlocale() ? 'selected=selected' : '' }}>{{ $language['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="form signup">
                <div class="form-top">
                    <span class="title">{{ __('Register') }}</span>
                    <a href="{{ route('main-page', app()->getlocale() ) }}">
                        <svg width="113" height="32" viewBox="0 0 113 32" xmlns="http://www.w3.org/2000/svg" fill="#fa4d1e" data-v-d92e36ae=""><path d="M30.5097 7.72414c-1.5882 0-2.6801.60188-3.375 1.70533-.6949 1.10343-.6949 1.60503-.6949 3.31033v.9028h-1.2904v2.4076h1.2904v9.0282h2.7795v-9.0282h2.8787v-2.4076h-2.8787v-.9028c0-1.8056.0992-2.1066 1.0919-2.5078.1985-.1003.3971-.1003.5956-.1003.2978 0 .5956.1003.7941.2006l.3971.2006V8.22571l-.0993-.10032c-.397-.30094-.8934-.40125-1.489-.40125zm3.2758-.20063h2.7795V25.0784h-2.7795V7.52351zM44.8041 13.442c-3.2758 0-5.8567 2.6082-5.8567 5.9185s2.5809 5.9185 5.8567 5.9185 5.8567-2.6082 5.8567-5.9185-2.5809-5.9185-5.8567-5.9185zm0 9.5298c-1.7868 0-3.1765-1.5047-3.1765-3.6113 0-2.1066 1.3897-3.6113 3.1765-3.6113s3.1765 1.5047 3.1765 3.6113c0 2.1066-1.3897 3.6113-3.1765 3.6113zm32.7578-2.2069l-1.9853-6.0188-.397-1.1035h-1.9854l-.2978 1.1035-1.9853 6.0188-2.3824-7.022.0993-.1003h-2.8787l.0992.2007-2.3824 6.9216-1.9853-6.0188-.3971-1.1035h-1.9853l-.4963 1.1035-1.9853 6.0188-2.3824-7.1223h-2.7795l4.2685 11.4358h1.6875l2.4817-6.6207 2.4816 6.6207h1.6875l2.8788-7.6239 2.8787 7.6239h1.6875l2.4817-6.6207 2.4816 6.6207h1.9853l4.3678-11.4358h-2.7795l-2.4817 7.1223zm11.6142-7.3229c-3.2758 0-5.8567 2.6082-5.8567 5.9185s2.5809 5.9185 5.8567 5.9185 5.8567-2.6082 5.8567-5.9185-2.5809-5.9185-5.8567-5.9185zm0 9.5298c-1.7868 0-3.1765-1.5047-3.1765-3.6113 0-2.1066 1.3897-3.6113 3.1765-3.6113s3.1765 1.5047 3.1765 3.6113c0 2.1066-1.3897 3.6113-3.1765 3.6113zm20.9449-9.3292l-2.382 7.1223-1.985-6.0188-.398-1.1035h-1.985l-.298 1.1035-1.985 6.0188-2.3823-7.1223h-2.7795l4.3678 11.4358h1.687l2.482-6.6207 2.482 6.6207h1.687L113 13.6426h-2.879zm-99.4645 7.3229l-.0993-6.2194c2.8787-.4013 4.9633-2.1066 6.0553-5.11601.9926-2.60815 1.1912-6.52037.8934-8.62696-1.6876.40126-4.2685 1.80565-5.0626 2.50784C11.8477 2.40752 10.458.80251 9.4653 0c-.99267.90282-2.38239 2.40752-2.97799 3.51097-.79413-.70219-3.37505-2.10658-5.06258-2.50784-.2978 2.10659-.09927 6.01881.8934 8.62696 1.09193 3.00941 3.17652 4.71471 6.05524 5.11601v6.3197s0-.1003-.09927-.1003C6.78511 18.558 3.90639 15.9498.13427 14.9467-.65986 21.5674 2.02033 29.3919 9.4653 32c7.445-2.6081 10.1252-10.5329 9.331-17.1536-3.7721 1.1034-6.5515 3.7116-8.1398 6.1191zm-8.43764-2.8088c2.87872 1.7054 6.15451 4.9154 6.05524 10.9342-3.67285-2.4075-5.65817-6.0188-6.05524-10.9342zm7.24644-5.6175c-4.66552 0-5.75745-4.21318-5.95598-8.42635 1.29046.7022 2.48166 1.70533 3.47432 2.80878.69486-1.5047 1.29046-2.60815 2.48166-3.81191 1.1912 1.20376 1.7868 2.30721 2.4817 3.81191.9926-1.00313 2.1838-2.10658 3.4743-2.70846-.1986 4.11285-1.2905 8.32603-5.956 8.32603zm1.1912 16.5517c0-6.0188 3.2758-9.3291 6.0552-10.9342-.2978 4.9154-2.2831 8.5267-6.0552 10.9342z"></path></svg>
                    </a>
                </div>

                <div class="form-top" id="btnDIV">
                    <button class="btn btn_active" id="role1" data-myattribute="1" data-role-name="Raýat">{{ __('Raýat') }}</button>
                    <button class="btn" id="role2" data-myattribute="2" data-role-name="Telekeçi">{{ __('Telekeçi') }}</button>
                    <button class="btn" id="role3" data-myattribute="3" data-role-name="Döwlet edara">{{ __('Döwlet edara') }}</button>
                </div>

                <form method="POST" action="{{ route('register', app()->getLocale()) }}">
                    @csrf

                    <input type="hidden" name="role" id="role" value="raýat">

                    <div id="company_name" class="hide">
                        <div class="input-field">
                            <input type="text" placeholder="{{ __('Company name') }}" class="@error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" >
                            <i class="uil uil-user">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <g id="Stockholm-icons-/-Home-/-Building" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                        <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" id="Combined-Shape" fill="#fa4d1e"></path>
                                        <rect id="Rectangle-Copy-2" fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"></rect>
                                        <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" id="Rectangle-2" fill="#000000" opacity="0.3"></path>
                                    </g>
                                </svg>
                            </i>
                        </div>
                        @error('company_name')
                            <div class="invalid-feedback">
                                <div>{{ $message }}</div>
                            </div>
                        @enderror
                    </div>

                    <div class="input-field">
                        <input type="text" placeholder="{{ __('First Name') }}" class="@error('first_name') is-invalid @enderror" name="first_name" autocomplete="on" required value="{{ old('first_name') }}" >
                        <i class="uil uil-user">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.50" d="M17 6C17 8.76142 14.7614 11 12 11C9.23858 11 7 8.76142 7 6C7 3.23858 9.23858 1 12 1C14.7614 1 17 3.23858 17 6Z" fill="#fa4d1e"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.818 14.1248C18.2016 13.4101 17.1428 13.4469 16.3149 13.9001C15.0338 14.6013 13.5635 15 12 15C10.4365 15 8.96618 14.6013 7.68505 13.9001C6.85717 13.4469 5.79841 13.4101 5.182 14.1248C3.82222 15.7014 3 17.7547 3 20V21C3 22.1045 3.89543 23 5 23H19C20.1046 23 21 22.1045 21 21V20C21 17.7547 20.1778 15.7014 18.818 14.1248Z" fill="#fa4d1e"/>
                            </svg>
                        </i>
                    </div>
                    @error('first_name')
                        <div class="invalid-feedback">
                            <div>{{ $message }}</div>
                        </div>
                    @enderror
                    <div class="input-field">
                        <input type="text" placeholder="{{ __('Last Name') }}" class="@error('last_name') is-invalid @enderror" name="last_name" autocomplete="on" required value="{{ old('last_name') }}" >
                        <i class="uil uil-user">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.50" d="M17 6C17 8.76142 14.7614 11 12 11C9.23858 11 7 8.76142 7 6C7 3.23858 9.23858 1 12 1C14.7614 1 17 3.23858 17 6Z" fill="#fa4d1e"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.818 14.1248C18.2016 13.4101 17.1428 13.4469 16.3149 13.9001C15.0338 14.6013 13.5635 15 12 15C10.4365 15 8.96618 14.6013 7.68505 13.9001C6.85717 13.4469 5.79841 13.4101 5.182 14.1248C3.82222 15.7014 3 17.7547 3 20V21C3 22.1045 3.89543 23 5 23H19C20.1046 23 21 22.1045 21 21V20C21 17.7547 20.1778 15.7014 18.818 14.1248Z" fill="#fa4d1e"/>
                            </svg>
                        </i>
                    </div>
                    @error('last_name')
                        <div class="invalid-feedback">
                            <div>{{ $message }}</div>
                        </div>
                    @enderror
                    <div class="input-field">
                        <input type="text" placeholder="{{ __('Email Address') }}" class="@error('email') is-invalid @enderror" name="email" autocomplete="on" required value="{{ old('email') }}" >
                        <i class="uil uil-envelope icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.50" d="M1 6C1 4.34315 2.34315 3 4 3H20C21.6569 3 23 4.34315 23 6V18C23 19.6569 21.6569 21 20 21H4C2.34315 21 1 19.6569 1 18V6Z" fill="#fa4d1e"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.23177 7.35984C5.58534 6.93556 6.2159 6.87824 6.64018 7.2318L11.3598 11.1648C11.7307 11.4739 12.2693 11.4739 12.6402 11.1648L17.3598 7.2318C17.7841 6.87824 18.4147 6.93556 18.7682 7.35984C19.1218 7.78412 19.0645 8.41468 18.6402 8.76825L13.9205 12.7013C12.808 13.6284 11.192 13.6284 10.0794 12.7013L5.35981 8.76825C4.93553 8.41468 4.87821 7.78412 5.23177 7.35984Z" fill="#fa4d1e" />
                            </svg>
                        </i>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">
                            <div>{{ $message }}</div>
                        </div>
                    @enderror
                    <div class="input-field">
                        <input type="password" class="password @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}" name="password" autocomplete="on" required value="{{ old('password') }}" >
                        <i class="uil uil-lock icon">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <defs>
                                    <polygon id="path-1" points="0 0 24 0 24 24 0 24"></polygon>
                                </defs>
                                <g id="Stockholm-icons-/-General-/-Lock" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <mask id="mask-2" fill="white">
                                        <use xlink:href="#path-1"></use>
                                    </mask>
                                    <g id="bound"></g>
                                    <path opacity="0.50" d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" id="Mask" fill="#fa4d1e"></path>
                                </g>
                            </svg>
                        </i>
                        <i class="uil uil-eye-slash showHidePw">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g id="Stockholm-icons-/-General-/-Visible" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                    <path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" id="Shape" fill="#fa4d1e" fill-rule="nonzero" opacity="0.7"></path>
                                    <path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" id="Path" fill="#000000" opacity="0.3"></path>
                                </g>
                            </svg>
                        </i>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">
                            <div>{{ $message }}</div>
                        </div>
                    @enderror
                    <div class="input-field">
                        <input type="password" class="password @error('password') is-invalid @enderror" placeholder="{{ __('Confirm Password') }}" name="password_confirmation" autocomplete="on" required >
                        <i class="uil uil-lock icon ">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <defs>
                                    <polygon id="path-1" points="0 0 24 0 24 24 0 24"></polygon>
                                </defs>
                                <g id="Stockholm-icons-/-General-/-Lock" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <mask id="mask-2" fill="white">
                                        <use xlink:href="#path-1"></use>
                                    </mask>
                                    <g id="bound"></g>
                                    <path opacity="0.50" d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" id="Mask" fill="#fa4d1e"></path>
                                </g>
                            </svg>
                        </i>
                        <i class="uil uil-eye-slash showHidePw">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g id="Stockholm-icons-/-General-/-Visible" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                    <path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" id="Shape" fill="#fa4d1e" fill-rule="nonzero" opacity="0.7"></path>
                                    <path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" id="Path" fill="#000000" opacity="0.3"></path>
                                </g>
                            </svg>
                        </i>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">
                            <div>{{ $message }}</div>
                        </div>
                    @enderror

                    <div class="input-field button">
                        <input type="submit" value="{{ __('Register') }}">
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">{{ __('Have An Account?') }}
                        <a class="text login-link">{{ __('Login') }}</a>
                    </span>
                </div>

                <div class="login-signup">
                    <span class="text">
                        <a href="{{ route('main-page', app()->getlocale() ) }}" class="text signup-link">Türkmenstandartlary</a>
                    </span>
                </div>

                <div class="login-signup">
                    <select id="changeLanguageRegister">
                        @foreach (Config::get('languages') as $lang => $language)
                            <option value="{{ $lang }}" {{ $lang == app()->getlocale() ? 'selected=selected' : '' }}>{{ $language['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/auth-script.js') }}"></script>
</body>
</html>