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

				$type = $arguments[0];
				$photo = $arguments[1];
				$path = $arguments[2];

				if( "stayforlong" == $photo['source'] && !empty($photo['sync'])){
					return $this->getUrlBucket($type, $photo['path']);
				}

				return $path.$photo['path'];
			}, ['is_safe' => ['html']]
		  ),
		];
	}

	private function getUrlBucket($type, $path)
	{
		$bucket = config('filesystems.disks.s3.bucket');
		$command = Storage::getAdapter()->getClient()->getCommand('GetObject', [
			'Bucket' => $bucket,
			'Key'    => "$type/$path",
		]);

		$request = Storage::getAdapter()->getClient()->createPresignedRequest($command, '+5 minutes');
		return (string)$request->getUri();
	}
}
