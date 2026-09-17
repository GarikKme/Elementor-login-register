# Elementor Login Register Widgets

Adds three Elementor widgets — Login Form, Registration Form, and a combined
Login + Registration Tabs widget — with AJAX submission, so site visitors can
log in or create an account without leaving the page.

**Status**: this plugin passed a full security audit covering open redirect,
CSRF/nonces, privilege escalation, password handling, SQL injection,
XSS/escaping, reCAPTCHA fail-closed behavior, direct file access, Composer
hygiene, and trait visibility, with one finding (open redirect) that has
since been fixed — see [Security](#5-security) for details.

## 1. Overview

**Requirements**

- WordPress 6.0+
- PHP 8.1+
- Elementor (free) active — the plugin refuses to load its functionality without it

**What it does**

- Renders a login form (login by email/username/both, optional "Remember Me" and
  "Forgot Password" link, configurable redirect URL) that authenticates via
  `wp_signon()`.
- Renders a registration form (first/last name, email, username, password +
  confirm) that creates a new `subscriber` user via `wp_create_user()` and logs
  them in immediately.
- Renders a tabbed widget combining both forms in one Elementor element, with
  independent tab switching handled client-side.
- All three forms submit over AJAX (`admin-ajax.php`) — no page reload, no
  external form-builder dependency.
- Every text label, the redirect URL, and a full Style tab (colors, typography,
  spacing) are editable per-widget-instance from the Elementor panel.
- Show/hide password toggle on every password field (login, registration,
  confirm password).
- Optional Google reCAPTCHA v2 (checkbox) on both Login and Registration
  forms, toggled independently per widget instance.

## reCAPTCHA Setup (optional)

1. Register your domain at
   [google.com/recaptcha/admin](https://www.google.com/recaptcha/admin) as
   reCAPTCHA v2 "I'm not a robot" Checkbox to get a **Site Key** and
   **Secret Key**.
2. Add both to `wp-config.php`, **before** the line that loads
   `wp-settings.php`:
   ```php
   define( 'ELR_RECAPTCHA_SITE_KEY', 'your-site-key' );
   define( 'ELR_RECAPTCHA_SECRET_KEY', 'your-secret-key' );
   ```
3. Then enable **"Enable reCAPTCHA"** per-widget under Content → Form
   Settings (Login Form, Registration Form, or each panel independently
   inside the Tabs widget).
4. Leaving both keys empty (the default) disables reCAPTCHA entirely with
   zero behavior change — no checkbox is rendered, no verification call is
   made.

**Design decision**: keys are set via `wp-config.php` constants rather than
a plugin settings page. The TOR didn't specify a UI for this, and a global
admin settings page would be a one-off exception to how every other setting
in this plugin works — labels, redirect URLs, and styling are all already
configured per-widget-instance through Elementor itself, not globally, so
adding a single global settings page just for two API keys would be
inconsistent with that pattern. `wp-config.php` is also how WordPress core
itself recommends storing sensitive keys (see how `AUTH_KEY`/`SECRET_KEY`
are handled) — it keeps secrets out of the database and out of anything a
plugin update might overwrite. A dedicated settings page (Settings API +
options table) would be the natural next step if this plugin needed more
global configuration later.

## 2. Installation

1. Copy (or clone) the `elementor-login-register` folder into
   `wp-content/plugins/`.
2. If installing from source (not a pre-built release zip), run `composer install`
   inside the plugin folder to generate `vendor/autoload.php` — the plugin will not
   load without it.
3. Make sure **Elementor** is installed and activated first.
4. Activate **Elementor Login Register Widgets** from the WordPress Plugins screen.
   - Activation enforces PHP 8.1+; on an older PHP version the plugin
     deactivates itself with an error message instead of fataling.
   - If Elementor isn't active, an admin notice is shown and the rest of the
     plugin (widgets, AJAX handlers) never loads.

## 3. Widgets

All three widgets live under the **"Login & Registration"** category in the
Elementor widget panel (custom category, icon `eicon-lock-user`).

| Widget | Elementor name | Icon | What it does |
|---|---|---|---|
| Login Form | `login_form` | `eicon-lock-user` | Standalone login form. |
| Registration Form | `registration_form` | `eicon-person` | Standalone registration form; auto-logs in the new user on success. |
| Login + Registration (Tabs) | `login_register_tabs` | `eicon-tabs` | Both forms in one widget, switchable via a tab nav; each tab keeps independent settings and styling. |

Each widget's Content tab exposes the field labels, the toggles (Remember Me,
Forgot Password link, required name fields), and the post-submit redirect URL.
Each has its own Style tab (input typography/colors/border, submit button
typography/colors/hover color, password-toggle icon color, field spacing). The
Tabs widget additionally has a "Tabs Settings" section (default active tab, tab
labels) and a "Tabs Nav Style" section (tab typography, active/inactive colors).

## 4. Architecture

### Folder structure

```
plugin.php              Bootstrap: header, constants, activation hook, Elementor-active gate
composer.json            PSR-4 autoload map
includes/
  Plugin.php             Singleton; hooks widget/category registration, asset registration, AJAX handlers
widgets/
  Login_Form.php
  Registration_Form.php
  Login_Register_Tabs.php
  Traits/
    Login_Fields_Trait.php         Login controls + render markup, reused by Login_Form and the Tabs widget
    Registration_Fields_Trait.php  Registration controls + render markup, reused by Registration_Form and the Tabs widget
    Password_Toggle_Trait.php      Show/hide password button markup, reused by both field traits
ajax/
  Login_Handler.php       wp_ajax_llr_login / wp_ajax_nopriv_llr_login
  Registration_Handler.php  wp_ajax_llr_register / wp_ajax_nopriv_llr_register
assets/
  css/login-register.css  Structural/layout CSS only — no hardcoded colors (those are Style tab controls)
  js/login-register.js    Form submit (AJAX), tab switching, password toggle — all delegated event handlers
```

### Trait-based composition

PHP classes support only single inheritance, and every widget must extend
Elementor's `\Elementor\Widget_Base`. Since `Login_Register_Tabs` needs to render
*both* the login fields and the registration fields inside one widget instance,
that logic couldn't live as a base class — traits were the only mechanism that
lets the same `register_controls()`/`render()` code be reused across widget
classes that already have a fixed parent.

- `Login_Fields_Trait` and `Registration_Fields_Trait` each expose a
  `register_*_content_controls()`, a `register_*_style_controls()`, and a
  `render_*_form_markup()` method, all parameterized by an optional
  `$id_prefix`/`$label_prefix`. `Login_Form` and `Registration_Form` call these
  with no prefix (so their control IDs are unchanged from a plain single-form
  widget). `Login_Register_Tabs` uses **both** traits together and calls each
  method twice with `login_`/`register_` prefixes, so the ~30 controls from both
  forms coexist in one widget without ID collisions.
- `Password_Toggle_Trait` holds the show/hide-password button markup and is
  itself `use`d inside both field traits, so the toggle only needs to be written
  once.

Because `Login_Register_Tabs` combines two traits that each independently pull
in `Password_Toggle_Trait`, PHP sees two copies of `render_password_toggle_button()`
and refuses to compile without an explicit resolution:

```php
class Login_Register_Tabs extends Widget_Base {
    use Login_Fields_Trait, Registration_Fields_Trait {
        Login_Fields_Trait::render_password_toggle_button insteadof Registration_Fields_Trait;
    }
    // ...
}
```

Both copies are byte-identical, so picking either is correct — this is a PHP
compile-time formality, not a behavioral choice.

### Autoloading

Composer PSR-4, declared in `composer.json`:

```json
"autoload": {
    "psr-4": {
        "ElementorLoginRegister\\Includes\\": "includes/",
        "ElementorLoginRegister\\Widgets\\": "widgets/",
        "ElementorLoginRegister\\Ajax\\": "ajax/"
    }
}
```

`ElementorLoginRegister\Widgets\Traits\*` resolves under the `widgets/` mapping
automatically (PSR-4 subdirectories). Every class/trait filename matches its
class name exactly (`Login_Form.php`, not `class-login-form.php`) — required by
PSR-4, not a WordPress-style naming convention.

### AJAX flow

1. `assets/js/login-register.js` binds one delegated `submit` listener on
   `document` matching `.llr-login-form__form, .llr-register-form__form`. On
   submit it calls `preventDefault()`, disables the submit button, and POSTs the
   form's `FormData` via `fetch()` to `ajaxUrl` (localized into
   `window.llrLoginRegister` by `wp_localize_script()` in `Plugin::register_assets()`).
2. WordPress routes the request by its hidden `action` field: `llr_login` →
   `Login_Handler::handle()`, `llr_register` → `Registration_Handler::handle()`
   (hooked to `wp_ajax_*` and `wp_ajax_nopriv_*` so both logged-out and logged-in
   visitors can reach them).
3. Each handler verifies the nonce with `check_ajax_referer()` before touching
   any input — `llr_login_action` for login, `llr_register_action` for
   registration, both read from a `nonce` POST field.
4. On success/failure the handler returns `wp_send_json_success()` /
   `wp_send_json_error()` with a `message` and (on success) a `redirect_url`. The
   JS writes `message` into that form's `.llr-form-message` div and, on success,
   redirects via `window.location.href` after a short delay.
5. A separate delegated `click` listener handles `.llr-tabs [data-tab]` (tab
   switching) and `.llr-password-toggle` (show/hide password) — both are pure UI
   toggles with no AJAX involved.

### Elementor integration points

- `plugin.php` hooks `plugins_loaded` and checks `did_action( 'elementor/loaded' )`
  before requiring the Composer autoloader or touching any Elementor class — if
  Elementor isn't active, only an admin notice is registered.
- `Plugin::register_categories()` hooks `elementor/elements/categories_registered`
  to add the `login-register` category.
- `Plugin::register_widgets()` hooks `elementor/widgets/register` to register all
  three widget instances.
- Every Style tab color/typography/spacing control uses Elementor's `selectors`
  mechanism with `{{WRAPPER}}`, e.g.:
  ```php
  'selectors' => [
      '{{WRAPPER}} .llr-login-form__submit' => 'background-color: {{VALUE}};',
  ],
  ```
  scoping every style to that specific widget instance instead of leaking
  globally — required because the same widget type can appear multiple times on
  a page with different settings.

## 5. Security

- **Nonces**: every AJAX request is verified with `check_ajax_referer()`
  (`llr_login_action` / `llr_register_action`) before any input is processed;
  failure returns a generic JSON error with HTTP 403 rather than exposing why.
- **Redirect validation**: the post-submit redirect URL is validated with
  `wp_validate_redirect()` (falling back to `home_url('/')`) in both
  `Login_Handler.php` and `Registration_Handler.php` — a client-tampered
  `redirect_to` hidden field pointing at an external domain is rejected
  rather than followed, closing an open-redirect phishing vector that a
  plain `esc_url_raw()` call does not close (it validates URL syntax, not
  that the host is local).
- **reCAPTCHA**: the secret key is server-side only, never exposed to the
  client or localized to JS; verification is a direct server-to-server call
  to Google's `siteverify` endpoint; all three failure modes (missing token,
  network error, `success: false`) fail closed — the action is blocked, not
  silently allowed — identically in both handlers.
- **Unique nonce field IDs**: the hidden nonce input's `id` attribute is
  generated per-widget-instance (`llr-login-nonce-{id}` /
  `llr-register-nonce-{id}`) rather than a shared literal `id="nonce"`, so
  the Login + Registration Tabs widget never renders two elements with a
  duplicate DOM id when both forms are present on the same page.
- **Sanitization**: `sanitize_text_field()` for names/usernames,
  `sanitize_email()` for email addresses, `wp_unslash()` on every raw
  `$_POST` value before use. Passwords are deliberately **not** run through
  `sanitize_text_field()` (which would strip characters) — only `wp_unslash()`
  and a cast to string, so any character a user legitimately typed survives.
- **Output escaping**: `esc_html()`/`esc_html__()` for text content,
  `esc_attr()`/`esc_attr__()` for HTML attributes, `esc_url()` for URLs —
  applied consistently across all render/trait markup. `esc_url_raw()` is no
  longer used anywhere in the codebase: the redirect-URL Elementor control
  itself has no sanitize callback (it's a plain text control), and the
  runtime `redirect_to` POST value goes through `wp_validate_redirect()`
  instead, which is a stronger validator (checks the host is local), not
  just an escaper.
- **Generic vs. specific error messages**: login failures always return the same
  "Invalid credentials" message regardless of whether the username/email or the
  password was wrong, so a failed login attempt can't be used to enumerate
  registered accounts. Registration uniqueness errors ("This email is already
  registered." / "This username is already taken.") are intentionally specific —
  that's expected, necessary UX for a signup form and doesn't leak anything
  beyond what the uniqueness constraint itself implies.
- **Passwords are never logged or echoed back**: the password fields are read
  once via `wp_unslash()`, passed straight into `wp_signon()` /
  `wp_create_user()`, and never written to a log, transient, or included in any
  JSON response.
- **Auto-login after registration** uses core WordPress session mechanisms
  (`wp_set_current_user()` + `wp_set_auth_cookie()`) — no custom token/cookie
  handling.
- New users are explicitly assigned the `subscriber` role via
  `$user->set_role( 'subscriber' )` after creation (defensive — it's already
  WordPress's default for new users, but the plugin doesn't rely on that default
  silently holding).

## 6. Known limitations / not implemented

- No rate-limiting on either form — both AJAX endpoints are open to scripted
  submission at whatever rate a client can send requests. reCAPTCHA (when
  enabled) is not a substitute for this: it targets spam/bot registration
  submissions, not brute-force login attempts against a known account —
  different threats, and login has no rate-limiting either way.
- No admin settings page — there is nothing to configure globally; all
  configuration (including the reCAPTCHA keys, set via `wp-config.php`
  constants — see reCAPTCHA Setup) is per-widget-instance via the Elementor
  editor or a config-file constant. A Settings API page would be a natural
  addition if more global configuration is needed later.
- No "resend verification email" / email confirmation flow — registration logs
  the user in immediately with no email verification step.
- No password-strength meter — only a minimum length check (8 characters)
  server-side.
- No multisite-specific handling beyond what WordPress core provides.
- No unit or integration test suite.
