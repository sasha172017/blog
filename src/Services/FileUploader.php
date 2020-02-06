<?php


namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 * @package App\Services
 */
class FileUploader
{
	/**
	 * @var ParameterBagInterface
	 */
	private $params;

	public function __construct(ParameterBagInterface $params)
	{
		$this->params = $params;
	}

	/**
	 * @param UploadedFile $file
	 * @param string       $parameter
	 * @param string|null  $newFileName
	 *
	 * @return string
	 */
	public function upload(UploadedFile $file, string $parameter, string $newFileName = null): string
	{
		$newFilename = $newFileName ?? uniqid(null, false) . '.' . $file->guessExtension();

		try
		{
			$file->move(
				$this->params->get($parameter),
				$newFilename
			);
		}
		catch (FileException $e)
		{
			throw new FileException($e->getMessage());
		}

		return $newFilename;
	}

}
