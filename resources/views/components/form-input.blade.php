<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>

    <input
        type="{{ $type ?? 'text' }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value ?? '') }}"
        {{ ($required ?? false) ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'form-control'
        ]) }}
    >

    @error($name)
        <p style="margin-top: 4px; font-size: 11px; color: #dc2626; font-weight: 500;">
            {{ $message }}
        </p>
    @enderror
</div>