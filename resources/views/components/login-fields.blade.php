@props([
    'autocompleteEmail' => 'email',
    'autocompletePassword' => 'current-password',
])

<div class="input">
    <input
        type="email"
        id="login_email"
        name="email"
        placeholder=" "
        value="{{ old('email') }}"
        required
        autofocus
        autocomplete="{{ $autocompleteEmail }}">
    <label for="login_email">EMAIL</label>
</div>

<div class="input">
    <input
        type="password"
        id="login_password"
        name="password"
        placeholder=" "
        required
        autocomplete="{{ $autocompletePassword }}">
    <label for="login_password">PASSWORD</label>

    <button
        type="button"
        class="toggle pw-toggle"
        data-target="login_password"
        aria-label="Toggle password visibility"
        aria-pressed="false">
        <i class="fa fa-eye" aria-hidden="true"></i>
    </button>
</div>
