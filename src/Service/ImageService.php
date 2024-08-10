<?php

namespace App\Service;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckDirectoryExistence;

class ImageService
{
    /**
     * NOTE: /!\ MUST BE IN LOWER, WITHOUT ANY SPECIAL CHAR!
     */
    const string BUCKET_NAME = "stockmanagement";

    private Filesystem $filesystem;

    public function __construct(private readonly string $minioEndpoint, private readonly string $minioKey, private readonly string $minioSecret, private readonly string $minioRegion)
    {
        $client = new S3Client([
            'region' => $this->minioRegion,
            'endpoint' => $this->minioEndpoint,
            'credentials' => [
                'key' => $this->minioKey,
                'secret' => $this->minioSecret,
            ],
            'use_path_style_endpoint' => true,
        ]);

        $adapter = new AwsS3V3Adapter($client, self::BUCKET_NAME);

        $this->filesystem = new Filesystem($adapter);
    }

    private function sanitiseObjectName(string $name): string
    {
        $name = strtolower($name);
        $name = str_replace(' ', '-', $name);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $name);
    }

    /**
     * @throws FilesystemException
     */
    public function putObject(string $name, string $content): string
    {
        $name = $this->sanitiseObjectName($name);
        $this->filesystem->write($name, $content);
        return $name;
    }

    /**
     * @throws FilesystemException
     */
    public function getObject(string $name): ?string
    {
        if ($this->filesystem->has($name)) {
            $rawImageContent = $this->filesystem->read($name);
            return 'data:image/jpeg;base64,' . base64_encode($rawImageContent);
        }
        return null;
    }

    /**
     * @throws FilesystemException
     */
    public function deleteObject(string $name): void
    {
        if ($this->filesystem->has($name)) {
            $this->filesystem->delete($name);
        }
    }
}
