{{-- resources/views/components/input-celular.blade.php --}}
@props(['name' => 'celular', 'value' => '', 'required' => false])

<input type="text" name="{{ $name }}" x-data
    x-on:input="
        let v = $el.value.replace(/\D/g, '').slice(0, 11);
        if (v.length > 10) v = v.replace(/(\d{2})(\d{1})(\d{4})(\d{4})/, '($1) $2 $3-$4');
        else if (v.length > 6) v = v.replace(/(\d{2})(\d{1})(\d{4})/, '($1) $2 $3');
        else if (v.length > 2) v = v.replace(/(\d{2})(\d{1})/, '($1) $2');
        else if (v.length > 0) v = v.replace(/(\d{2})/, '($1');
        $el.value = v;
    "
    value="{{ old($name, $value) }}" placeholder="(69) 9 9999-9999" maxlength="16"
    {{ $attributes->merge(['class' => 'block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}
    {{ $required ? 'required' : '' }} />
