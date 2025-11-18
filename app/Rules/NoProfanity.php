<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoProfanity implements ValidationRule
{
    /**
     * Lista de palavras de baixo calão a serem bloqueadas.
     *
     * @var array<string>
     */
    private array $profanityWords = [
        'caralho',
        'porra',
        'puta',
        'puto',
        'foda',
        'foder',
        'fodido',
        'fodida',
        'merda',
        'buceta',
        'bucetão',
        'cacete',
        'caceta',
        'piranha',
        'vagabunda',
        'vagabundo',
        'viado',
        'viadinho',
        'bicha',
        'bichinha',
        'filho da puta',
        'filha da puta',
        'fdp',
        'vsf',
        'vai se foder',
        'vai tomar no cu',
        'vtnc',
        'cu',
        'bunda',
        'rabo',
        'pau',
        'rola',
        'piroca',
        'pica',
        'xoxota',
        'xota',
        'xana',
        'pênis',
        'pinto',
        'caralhada',
        'porra nenhuma',
        'puta que pariu',
        'pqp',
        'puta merda',
        'cara de pau',
        'filho de uma puta',
        'filha de uma puta',
    ];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $normalizedValue = $this->normalizeText($value);

        foreach ($this->profanityWords as $word) {
            $normalizedWord = $this->normalizeText($word);

            // Verifica se a palavra aparece como palavra completa (não como substring)
            // Usa regex com limites de palavra (\b) para evitar falsos positivos
            $pattern = '/\b'.preg_quote($normalizedWord, '/').'\b/i';

            if (preg_match($pattern, $normalizedValue)) {
                $fail('O campo :attribute contém palavras inadequadas e não pode ser salvo.');
            }
        }
    }

    /**
     * Normaliza o texto removendo acentos e convertendo para minúsculas.
     */
    private function normalizeText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = $this->removeAccents($text);

        return $text;
    }

    /**
     * Remove acentos do texto.
     */
    private function removeAccents(string $text): string
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N',
        ];

        return strtr($text, $accents);
    }
}
