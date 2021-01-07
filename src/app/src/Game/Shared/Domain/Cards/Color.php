<?php declare(strict_types=1);


namespace App\Game\Shared\Domain\Cards;


use MyCLabs\Enum\Enum;

/**
 * @method static self DIAMOND()
 * @method static self SPADE()
 * @method static self CLUB()
 * @method static self HEART()
 */
class Color extends Enum
{
    const DIAMOND = 'diamond';
    const SPADE = 'spade';
    const CLUB = 'club';
    const HEART = 'heart';
}
