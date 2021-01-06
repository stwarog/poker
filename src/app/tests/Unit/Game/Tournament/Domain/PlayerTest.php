<?php declare(strict_types=1);


namespace App\Tests\Unit\Game\Tournament\Domain;


use App\Game\Chip;
use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @test */
    public function player__new__has_no_chips(): void
    {
        // Given
        $expectedChipAmount = new Chip(0);

        // When
        $p = new Player();

        // Then
        $this->assertTrue($expectedChipAmount->equals($p->chipsAmount()));
    }

    /**
     * 1
     * @test
     */
    public function addChips__sums_added_values(): void
    {
        // Given
        $expected = new Chip(Chip::WHITE50);

        // When
        $p = new Player();
        $p->addChips(new Chip(Chip::RED25));
        $p->addChips(new Chip(Chip::RED25));

        // Then
        $this->assertTrue($expected->equals($p->chipsAmount()));
    }

    /** @test */
    public function addChips__amount_zero__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not add 0 value chips');

        // Given
        $chip = new Chip(0);

        // When
        $p = new Player();
        $p->addChips($chip);
    }

    /** @test */
    public function takeChips__player_has_some_chips__reduces_amount_by_given_value(): void
    {
        // Given
        $expected = new Chip(Chip::RED25);
        $take     = new Chip(Chip::RED25);

        // When
        $p = new Player();
        $p->addChips(new Chip(Chip::WHITE50));
        $p->takeChips($take);

        // Then
        $this->assertTrue($expected->equals($p->chipsAmount()));
    }

    /** @test */
    public function takeChips__amount_is_zero__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not take 0 value chip');

        // Given
        $take = new Chip(0);

        // When
        $p = new Player();
        $p->takeChips($take);
    }

    /** @test */
    public function takeChips__amount_after_reduce_is_less_than_zero__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Requested to take 50 chips but user has only 25');

        // Given
        $take = new Chip(Chip::WHITE50);

        // When
        $p = new Player();
        $p->addChips(new Chip(Chip::RED25));
        $p->takeChips($take);
    }

    /** @test */
    public function getStatus__new_player__has_active(): void
    {
        // Given
        $expectedStatus = PlayerStatus::ACTIVE();

        // When
        $p = new Player();

        // Then
        $this->assertTrue($expectedStatus->equals($p->getStatus()));
    }

    /** @test */
    public function getStatus__player_has_no_chips__has_lost_status(): void
    {
        // Given
        $expectedStatus = PlayerStatus::LOST();

        // When
        $p = new Player();
        $p->addChips(new Chip(Chip::RED25));
        $this->assertTrue(PlayerStatus::ACTIVE()->equals($p->getStatus()));
        $p->takeChips(new Chip(Chip::RED25));

        // Then
        $this->assertTrue($expectedStatus->equals($p->getStatus()));
    }
}
