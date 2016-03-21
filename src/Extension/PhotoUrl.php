<?php
namespace StayForLong\TwigExtensions\Extension;

use Illuminate\Support\Facades\Storage;
use Twig_Extension;
use Twig_SimpleFunction;

class PhotoUrl extends Twig_Extension
{
	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'photo_url';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFunctions()
	{
		return [
			new Twig_SimpleFunction(
				'photo_url',
				function ($name) {
					$arguments = func_get_args();

					$type      = $arguments[0];
					$photo     = $arguments[1];
					$path      = $arguments[2];
					$encrypted = !empty($arguments[3]) ? $arguments[3] : false;

					if ("stayforlong" == $photo['source']) {
						return $this->getUrlBucket($type, $photo['path'], $encrypted);
					}

					if (!empty($photo['path'])) {
						return $path . $photo['path'];
					}
				}, ['is_safe' => ['html']]
			),
		];
	}

	private function getUrlBucket($type, $path, $encrypted = false)
	{
		$bucket = config('filesystems.disks.s3.bucket');

		if ($encrypted) {
			$url = $this->getEncryptedUrl($bucket, $type, $path);
		} else {
			$url = $this->getPublicUrl($bucket, $type, $path);
		}

		return $url;
	}

	/**
	 * @param $bucket
	 * @param $type
	 * @param $path
	 * @return string
	 */
	private function getEncryptedUrl($bucket, $type, $path)
	{
		$command = Storage::getAdapter()
			->getClient()
			->getCommand('GetObject', [
					'Bucket' => $bucket,
					'Key'    => "$type/$path",
				]
			);

		$request = Storage::getAdapter()
			->getClient()
			->createPresignedRequest($command, '+5 minutes');

		return (string)$request->getUri();
	}

	private function getPublicUrl($bucket, $type, $path)
	{
		return Storage::getAdapter()->getClient()->getObjectUrl($bucket, "$type/$path");
	}
}
