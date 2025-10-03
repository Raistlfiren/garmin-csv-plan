<?php

namespace App\Http\Mfa;

use Symfony\Component\Console\Style\StyleInterface;

class StyleCodeProvider implements CodeProviderInterface
{
    private $style;

    public function __construct(StyleInterface $style)
    {
        $this->style = $style;
    }

    public function provide(): string
    {
        return $this->style->askHidden('Enter the MFA code');
    }
}
