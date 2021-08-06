<?php

namespace ExternalEvents\Interfaces;

interface ExternalEventsInterface
{
    public function pack(): array;
    public function name(): string;
    public function shouldNotSend(): bool;
}
