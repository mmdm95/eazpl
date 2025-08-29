<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\AlignmentEnums;
use Eazpl\Enums\CoordinationEnums;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class Position implements RendererInterface
{
    /**
     * @var array|\class-string[]
     */
    protected array $notValidElements = [
        Barcode::class,
        Position::class,
        Table::class,
    ];

    /**
     * @var array
     */
    protected array $elements = [];

    /**
     * @var AlignmentEnums|null
     */
    protected ?AlignmentEnums $alignment = null;

    /**
     * @var CoordinationEnums
     */
    protected CoordinationEnums $coordinator = CoordinationEnums::TOP_LEFT_CORNER;

    /**
     * @param int $x
     * @param int $y
     * @param RendererInterface ...$elements
     */
    public function __construct(protected int $x, protected int $y, RendererInterface ...$elements)
    {
        $this->x = RenderUtils::getValidXYValue($this->x);
        $this->y = RenderUtils::getValidXYValue($this->y);

        foreach ($elements as $element) {
            if (!$this->isValidElement($element)) {
                throw new InvalidArgumentException('Invalid element provided: ' . gettype($element));
            }
        }

        $this->elements = $elements;
    }

    /**
     * @param RendererInterface $element
     * @return bool
     */
    protected function isValidElement(RendererInterface $element): bool
    {
        foreach ($this->notValidElements as $notValidElement) {
            if ($element instanceof $notValidElement) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param CoordinationEnums $coordinator
     * @return $this
     */
    public function coordinator(CoordinationEnums $coordinator): static
    {
        $this->coordinator = $coordinator;
        return $this;
    }

    /**
     * @param int $alignment
     * @return static
     */
    public function alignment(AlignmentEnums|int $alignment): static
    {
        $this->alignment = RenderUtils::getValidAlignment($alignment);
        return $this;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return sprintf(
                '^F%s%d,%d',
                $this->coordinator === CoordinationEnums::BOTTOM_LEFT_CORNER ? 'T' : 'O',
                $this->x,
                $this->y
            ) .
            ($this->alignment ? ',' . $this->alignment->value : '') .
            sprintf('%s^FS%s',
                RenderUtils::renderInsiderElements($this->elements),
                "\n"
            );
    }
}
