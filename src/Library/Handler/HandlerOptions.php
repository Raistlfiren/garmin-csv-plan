<?php

namespace App\Library\Handler;

use DateTime;

class HandlerOptions
{
    private ?string $path = null;

    private ?string $email = null;

    private ?string $password = null;

    /**
     * @var bool|false
     */
    private $delete;

    /**
     * @var bool|false
     */
    private $deleteOnly;

    /**
     * @var DateTime|string|null
     */
    private $startDate;

    /**
     * @var DateTime|string|null
     */
    private $endDate;

    private ?string $command = null;

    private ?string $prefix = null;

    /**
     * @var bool|false
     */
    protected $dryrun;

    /**
     * @var string|null
     */
    protected $poolSize;

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): HandlerOptions
    {
        $this->path = $path;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): HandlerOptions
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): HandlerOptions
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return bool|false
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * @param bool|false $delete
     */
    public function setDelete($delete): static
    {
        $this->delete = $delete;
        return $this;
    }

    /**
     * @return bool|false
     */
    public function getDeleteOnly()
    {
        return $this->deleteOnly;
    }

    /**
     * @param bool|false $deleteOnly
     */
    public function setDeleteOnly($deleteOnly): static
    {
        $this->deleteOnly = $deleteOnly;
        return $this;
    }

    /**
     * @return DateTime|string|null
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param DateTime|string|null $startDate
     */
    public function setStartDate($startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime|string|null
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param DateTime|string|null $endDate
     */
    public function setEndDate($endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand(string $command): HandlerOptions
    {
        $this->command = $command;
        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): HandlerOptions
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return bool|false
     */
    public function getDryrun()
    {
        return $this->dryrun;
    }

    /**
     * @param bool|false $dryrun
     */
    public function setDryrun($dryrun): static
    {
        $this->dryrun = $dryrun;
        return $this;
    }

    public function getPoolSize(): ?string
    {
        return $this->poolSize;
    }

    public function setPoolSize(?string $poolSize): HandlerOptions
    {
        $this->poolSize = $poolSize;
        return $this;
    }
}
