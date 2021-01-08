<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use MyCLabs\Enum\Enum;

/**
 * @codeCoverageIgnore
 *
 * @method static TournamentStatus PREPARATION()
 * @method static TournamentStatus SIGN_UPS()
 * @method static TournamentStatus READY()
 * @method static TournamentStatus STARTED()
 */
class TournamentStatus extends Enum
{
    public const PREPARATION = 'preparation';
    public const SIGN_UPS = 'sign-ups';
    public const READY = 'ready';
    public const STARTED = 'started';
}
