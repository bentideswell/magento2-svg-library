<?php
/**
 *
 */
declare(strict_types=1);

namespace FishPig\SvgLibrary\App;

class SvgMinifier
{
    /**
     *
     */
    public function minify(string $data): string
    {
        $data = str_replace(["\r", "\n"], ' ', $data);
        $data = preg_replace('/>\s+</s', '><', $data);
        
        return trim($data);
    }
}
