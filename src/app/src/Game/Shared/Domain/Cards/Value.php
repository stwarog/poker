<?php declare(strict_types=1);


namespace App\Game\Shared\Domain\Cards;


use MyCLabs\Enum\Enum;

/**
 * @method static self TWO()
 * @method static self THREE()
 * @method static self FOUR()
 * @method static self FIVE()
 * @method static self SIX()
 * @method static self SEVEN()
 * @method static self EIGHT()
 * @method static self NINE()
 * @method static self TEN()
 * @method static self JACK()
 * @method static self QUEEN()
 * @method static self KING()
 * @method static self ACE()
 */
class Value extends Enum
{
    const TWO = '2';
    const THREE = '3';
    const FOUR = '4';
    const FIVE = '5';
    const SIX = '6';
    const SEVEN = '7';
    const EIGHT = '8';
    const NINE = '9';
    const TEN = '10';
    const JACK = 'jack';
    const QUEEN = 'queen';
    const KING = 'king';
    const ACE = 'ace';
}
