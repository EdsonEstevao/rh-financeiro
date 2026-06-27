{{-- resources/views/components/input-telefone.blade.php --}}
@props(['name' => 'telefone', 'value' => '', 'required' => false])

<input type="text" name="{{ $name }}" x-data
    x-on:input="
        let v = $el.value.replace(/\D/g, '').slice(0, 10);
        if (v.length > 6) v = v.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        else if (v.length > 2) v = v.replace(/(\d{2})(\d{4})/, '($1) $2');
        else if (v.length > 0) v = v.replace(/(\d{2})/, '($1');
        $el.value = v;
    "
    value="{{ old($name, $value) }}" placeholder="(69) 9999-9999" maxlength="14"
    {{ $attributes->merge(['class' => 'block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}
    {{ $required ? 'required' : '' }} />
