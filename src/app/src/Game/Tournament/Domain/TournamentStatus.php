<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use MyCLabs\Enum\Enum;

/**
 * @method static TournamentStatus PENDING()
 * @method static TournamentStatus READY()
 * @method static TournamentStatus STARTED()
 */
class TournamentStatus extends Enum
{
    public const PENDING = 'pending';
    public const READY = 'ready';
    public const STARTED = 'started';
}
