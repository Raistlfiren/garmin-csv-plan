<?php

namespace App\Http\Mfa;

interface CodeProviderInterface
{
    public function provide(): string;
}
