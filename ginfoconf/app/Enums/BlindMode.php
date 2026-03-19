<?php

namespace App\Enums;

enum BlindMode: string
{
    case Open   = 'open';
    case Single = 'single'; // reviewers know authors, authors don't know reviewers
    case Double = 'double'; // neither side knows the other
}
