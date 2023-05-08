<?php

declare(strict_types=1);

namespace App\Twig;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    protected FilesystemOperator $bucket;

    public function __construct(FilesystemOperator $defaultStorage)
    {
        $this->bucket = $defaultStorage;
    }

    final public function getFunctions(): array
    {
        return [
            new TwigFunction('get_bucket_url', [$this, 'getBucketUrl']),
        ];
    }

    /**
     * @throws FilesystemException
     */
    final public function getBucketUrl(string $path): string|null
    {
        $url = null;

        if ($this->bucket->has($path)) {
            $url = $this->bucket->publicUrl($path);
        }

        return $url;
    }
}

