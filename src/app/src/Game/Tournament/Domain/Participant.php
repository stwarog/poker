<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use App\Account\Domain\AccountId;

class Participant
{
    private string $id;
    private string $account;

    public function __construct(ParticipantId $participant, AccountId $account)
    {
        $this->id      = $participant->toString();
        $this->account = $account->toString();
    }

    public static function create(AccountId $account): self
    {
        return new self (ParticipantId::create(), $account);
    }

    public function getId(): ParticipantId
    {
        return ParticipantId::fromString($this->id);
    }

    public function getAccountId(): AccountId
    {
        return AccountId::fromString($this->account);
    }
}
