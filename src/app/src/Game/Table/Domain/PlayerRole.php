<?php declare(strict_types=1);


namespace App\Game\Table\Domain;


use MyCLabs\Enum\Enum;

/**
 * @codeCoverageIgnore
 *
 * @method static PlayerRole NONE()
 * @method static PlayerRole SMALL_BLIND()
 * @method static PlayerRole BIG_BLIND()
 */
class PlayerRole extends Enum
{
    public const NONE = 'none';
    public const SMALL_BLIND = 'small';
    public const BIG_BLIND = 'big';
}
