<?php

namespace App;

/**
 * Class file
 * 
 * PHP @version 7.4
 */
class File
{

    private array $file;
    private array $pathinfo;
    public string $filename;
    private string $base;
    public string $destination;
    private int $i;

    /**
     * Class constructor.
     */
    public function __construct(array $file)
    {
        $this->file = $file;

        $this->sanitize();

        $this->destination = "../public/uploads/$this->filename";

        $this->i = 1;
    }


    public function sanitize(): void
    {
        $this->pathinfo = pathinfo($this->file['file']['name']);

        $this->base = $this->pathinfo['filename'];

        $this->base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->base);

        $this->base = substr($this->base, 0, 200);

        $this->filename = $this->base . '.' . $this->pathinfo['extension'];
    }

    public function renamingDuplicates(): void
    {

        while (file_exists($this->destination)) {

            $this->filename = $this->base . "-" . $this->i . "." . $this->pathinfo['extension'];
            $this->destination = "../public/uploads/" . $this->filename;
            $this->i++;
        }
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
