<?php

namespace App\Library\Handler;

use DateTime;

class HandlerOptions
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var bool|false
     */
    private $delete;

    /**
     * @var DateTime|string|null
     */
    private $startDate;

    /**
     * @var DateTime|string|null
     */
    private $endDate;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string|null
     */
    private $prefix;

    /**
     * @var bool|false
     */
    protected $dryrun;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return HandlerOptions
     */
    public function setPath(string $path): HandlerOptions
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return HandlerOptions
     */
    public function setEmail(?string $email): HandlerOptions
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return HandlerOptions
     */
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
     * @return HandlerOptions
     */
    public function setDelete($delete)
    {
        $this->delete = $delete;
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
     * @return HandlerOptions
     */
    public function setStartDate($startDate)
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
     * @return HandlerOptions
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param string $command
     * @return HandlerOptions
     */
    public function setCommand(string $command): HandlerOptions
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @param string|null $prefix
     * @return HandlerOptions
     */
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
     * @return HandlerOptions
     */
    public function setDryrun($dryrun)
    {
        $this->dryrun = $dryrun;
        return $this;
    }
}
