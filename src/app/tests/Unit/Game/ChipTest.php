<?php declare(strict_types=1);


namespace App\Tests\Unit\Game;


use App\Game\Chip;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ChipTest extends TestCase
{
    /**
     * @test
     * @dataProvider chip_new_with_allowed_value_dataProvider
     *
     * @param int $value
     */
    public function chip_new_with_allowed_value(int $value): void
    {
        // Given
        $expected = $value;

        // When
        $c = new Chip($value);

        // Then
        $this->assertSame($expected, $c->getValue());
    }

    public function chip_new_with_allowed_value_dataProvider(): array
    {
        return [
            'zero'       => [0],
            'red 25'     => [25],
            'white 50'   => [50],
            'green 100'  => [100],
            'blue 500'   => [500],
            'black 1000' => [1000],
        ];
    }

    /** @test */
    public function chip__less_than_zero__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be greater or equals zero');

        // When
        new Chip(-1);
    }

    /**
     * @test
     * @dataProvider chip__value_not_dividable_by_values__dataProvider
     */
    public function chip__value_not_dividable_by_values__throws_invalid_argument_exception(int $amount): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be dividable by: 5');

        // When
        new Chip($amount);
    }

    public function chip__value_not_dividable_by_values__dataProvider(): array
    {
        return [
            'minimal allowed' => [26],
            'big allowed'     => [100001],
        ];
    }
}
