<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use App\Account\Domain\AccountId;
use DateTimeImmutable;
use DateTimeInterface;

class Participant
{
    private string $id;
    private string $account;
    private string $tournament;
    private DateTimeInterface $signUpDate;

    public function __construct(ParticipantId $participant, AccountId $account, TournamentId $tournament)
    {
        $this->id      = $participant->toString();
        $this->account = $account->toString();
        $this->tournament = $tournament->toString();
        $this->signUpDate    = new DateTimeImmutable();
    }

    public static function create(AccountId $account, TournamentId $tournament): self
    {
        return new self (ParticipantId::create(), $account, $tournament);
    }

    public function getId(): ParticipantId
    {
        return ParticipantId::fromString($this->id);
    }

    public function getAccountId(): AccountId
    {
        return AccountId::fromString($this->account);
    }

    public function getSignUpDate(): DateTimeInterface
    {
        return $this->signUpDate;
    }
}
