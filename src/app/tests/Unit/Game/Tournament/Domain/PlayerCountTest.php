<?php declare(strict_types=1);


namespace Unit\Game\Tournament\Domain;


use App\Game\Tournament\Domain\PlayerCount;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PlayerCountTest extends TestCase
{
    /**
     * 0
     * @test
     */
    public function new__with_no_values__creates_with_2_12_range(): void
    {
        // Given
        $expectedMin = 2;
        $expectedMax = 12;

        // When
        $playerCount = new PlayerCount();

        // Then
        $this->assertSame($expectedMax, $playerCount->getMax());
        $this->assertSame($expectedMin, $playerCount->getMin());
    }

    /**
     * 1
     * @test
     */
    public function new__with_values__creates_with_them(): void
    {
        // Given
        $expectedMin = 5;
        $expectedMax = 11;

        // When
        $playerCount = new PlayerCount($expectedMin, $expectedMax);

        // Then
        $this->assertSame($expectedMax, $playerCount->getMax());
        $this->assertSame($expectedMin, $playerCount->getMin());
    }

    /** @test */
    public function new__with_values_out_of_2_12_range__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('~Players count ~');

        // Given
        $expectedMin = 1;
        $expectedMax = 11;

        // When
        new PlayerCount($expectedMin, $expectedMax);
    }
}
