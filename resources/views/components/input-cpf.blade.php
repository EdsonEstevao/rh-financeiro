{{-- resources/views/components/input-cpf.blade.php --}}
@props(['name' => 'cpf', 'value' => '', 'required' => false])

<input type="text" name="{{ $name }}" x-data
    x-on:input="
        let value = $el.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        if (value.length > 9) value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        else if (value.length > 6) value = value.replace(/(\d{3})(\d{3})(\d{3})/, '$1.$2.$3');
        else if (value.length > 3) value = value.replace(/(\d{3})(\d{3})/, '$1.$2');
        else if (value.length > 0) value = value.replace(/(\d{3})/, '$1');
        $el.value = value;
    "
    value="{{ old($name, $value) }}" placeholder="000.000.000-00" maxlength="14"
    {{ $attributes->merge(['class' => 'block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}
    {{ $required ? 'required' : '' }} />
