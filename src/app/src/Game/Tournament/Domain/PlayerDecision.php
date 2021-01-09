<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use MyCLabs\Enum\Enum;

/**
 * @codeCoverageIgnore
 *
 * @method static TournamentStatus WAITING()
 * @method static TournamentStatus FOLD()
 * @method static TournamentStatus CALL()
 * @method static TournamentStatus RAISE()
 */
class PlayerDecision extends Enum
{
    public const WAITING = 'waiting';
    public const FOLD = 'fold';
    public const CALL = 'call';
    public const RAISE = 'raise';
}
