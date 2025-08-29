<?php

namespace Eazpl;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Elements\Barcode;
use Eazpl\Elements\Charset;
use Eazpl\Elements\Comment;
use Eazpl\Elements\HexIndicator;
use Eazpl\Elements\Mode;
use Eazpl\Elements\Position;
use Eazpl\Elements\Raw;
use Eazpl\Elements\ReversePrint;
use Eazpl\Elements\Table;
use Eazpl\Elements\TextBlock;
use Eazpl\Elements\TextGroup;
use Eazpl\Elements\Wrapper;
use Eazpl\Exceptions\ConnectionException;
use Eazpl\Exceptions\InvalidIPAddressException;
use Eazpl\Exceptions\InvalidPortException;
use Eazpl\Exceptions\NeedIPAddressException;
use Eazpl\Utils\PrinterUtils;
use InvalidArgumentException;
use JsonSerializable;

class ZplPrinter implements JsonSerializable
{
    // TODO: Parse these and create more elements
    // ^XA^MMP^PW300^LS0^LT0^FT10,60^APN,30,30^FH\^FDSAMPLE TEXT^FS^XZ
    // ^FO10,10^GC150,150,B^FS

    /**
     * @var array
     */
    protected array $elements = [];

    /**
     * @var array
     */
    protected array $validElements = [
        Barcode::class,
        Charset::class,
        Comment::class,
        HexIndicator::class,
        Mode::class,
        Position::class,
        Raw::class,
        ReversePrint::class,
        Table::class,
        TextBlock::class,
        TextGroup::class,
        Wrapper::class,
    ];

    /**
     * @param string|null $ipAddress
     * @param int $port
     */
    public function __construct(protected ?string $ipAddress = null, protected int $port = 9100)
    {
    }

    /**
     * @param string|null $ipAddress
     * @param int $port
     * @return static
     */
    public static function make(?string $ipAddress = null, int $port = 9100): static
    {
        return new static($ipAddress, $port);
    }

    /**
     * @param string $ipAddress
     * @return static
     */
    public function setIpAddress(string $ipAddress): static
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @param int $port
     * @return static
     */
    public function setPort(int $port): static
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param RendererInterface $element
     * @return static
     */
    public function addElement(RendererInterface $element): static
    {
        if (!$this->isValidElement($element)) {
            throw new InvalidArgumentException(
                'Please provide a valid element. Valid elements are: [' .
                implode(', ', $this->validElements) .
                ']'
            );
        }

        $this->elements[] = $element;
        return $this;
    }

    /**
     * @param RendererInterface ...$elements
     * @return static
     */
    public function addElements(RendererInterface ...$elements): static
    {
        foreach ($elements as $element) {
            $this->addElement($element);
        }

        return $this;
    }

    /**
     * @param RendererInterface $element
     * @return bool
     */
    protected function isValidElement(RendererInterface $element): bool
    {
        return (bool)count(
            array_filter($this->validElements, fn($valid) => $element instanceof $valid)
        );
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $zpl = '^XA' . "\n\n";

        foreach ($this->elements as $element) {
            $zpl .= $element->render();
        }

        return trim($zpl) . "\n\n" . '^XZ';
    }

    /**
     * @return bool
     * @throws ConnectionException
     * @throws InvalidPortException
     * @throws InvalidIPAddressException
     * @throws NeedIPAddressException
     */
    public function send(): bool
    {
        if (is_null($this->ipAddress)) {
            throw new NeedIPAddressException('Please provide IP address.');
        }

        $ipAddress = PrinterUtils::getValidIpAddressOf($this->ipAddress);
        $port = PrinterUtils::getValidPortOf($this->port);

        // Create a TCP/IP socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (false === $socket) {
            throw new ConnectionException("Unable to create socket: " . socket_strerror(socket_last_error()));
        }

        // Connect to the printer
        $result = socket_connect($socket, $ipAddress, $port);
        if (false === $result) {
            throw new ConnectionException(
                "Unable to connect to printer: " . socket_strerror(socket_last_error($socket))
            );
        }

        $zplCommand = $this->build();

        socket_write($socket, $zplCommand, strlen($zplCommand));
        socket_close($socket);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->build();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }
}
