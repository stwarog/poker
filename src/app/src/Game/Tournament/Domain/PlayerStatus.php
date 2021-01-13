<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use MyCLabs\Enum\Enum;

/**
 * @codeCoverageIgnore
 *
 * @method static TournamentStatus ACTIVE()
 * @method static TournamentStatus NOT_JOINED()
 * @method static TournamentStatus LOST()
 * @method static TournamentStatus WON()
 */
class PlayerStatus extends Enum
{
    public const ACTIVE = 'active';
    public const NOT_JOINED = 'not-joined';
    public const LOST = 'lost';
    public const WON = 'won';
}
