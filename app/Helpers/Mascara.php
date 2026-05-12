<?php

namespace App\Helpers;

class Mascara
{
    public static function cpf(?string $valor): string
    {
        if (!$valor) return '—';
        $valor = preg_replace('/\D/', '', $valor);
        if (strlen($valor) !== 11) return $valor;
        return substr($valor, 0, 3) . '.' .
               substr($valor, 3, 3) . '.' .
               substr($valor, 6, 3) . '-' .
               substr($valor, 9, 2);
    }

    public static function telefone(?string $valor): string
    {
        if (!$valor) return '—';
        $valor = preg_replace('/\D/', '', $valor);
        if (strlen($valor) === 11) {
            return '(' . substr($valor, 0, 2) . ') ' .
                   substr($valor, 2, 5) . '-' .
                   substr($valor, 7, 4);
        }
        if (strlen($valor) === 10) {
            return '(' . substr($valor, 0, 2) . ') ' .
                   substr($valor, 2, 4) . '-' .
                   substr($valor, 6, 4);
        }
        return $valor;
    }

    public static function cep(?string $valor): string
    {
        if (!$valor) return '—';
        $valor = preg_replace('/\D/', '', $valor);
        if (strlen($valor) !== 8) return $valor;
        return substr($valor, 0, 5) . '-' . substr($valor, 5, 3);
    }

    public static function moeda(?float $valor): string
    {
        if ($valor === null) return 'R$ 0,00';
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }

    public static function cnpj(?string $valor): string
    {
        if (!$valor) return '—';
        $valor = preg_replace('/\D/', '', $valor);
        if (strlen($valor) !== 14) return $valor;
        return substr($valor, 0, 2) . '.' .
               substr($valor, 2, 3) . '.' .
               substr($valor, 5, 3) . '/' .
               substr($valor, 8, 4) . '-' .
               substr($valor, 12, 2);
    }
}