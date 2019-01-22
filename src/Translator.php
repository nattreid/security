<?php

declare(strict_types=1);

namespace NAttreid\Security;

use NAttreid\AppManager\AppManager;
use Symfony\Component\Translation\Writer\TranslationWriter;

/**
 * Class Translator
 *
 * @author Attreid <attreid@gmail.com>
 */
class Translator
{
	/** @var string */
	private $langDir;

	/** @var \Kdyby\Translation\Translator */
	private $translator;

	/** @var TranslationWriter */
	private $writer;

	/** @var AppManager */
	private $appManager;

	public function __construct(string $langDir, ?\Kdyby\Translation\Translator $translator, ?TranslationWriter $writer, AppManager $appManager)
	{
		$this->langDir = $langDir;
		$this->translator = $translator;
		$this->writer = $writer;
		$this->appManager = $appManager;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function translate(string $name): String
	{
		if ($this->translator !== null) {
			return $this->translator->translate('security.roles.' . $name);
		}
		return $name;
	}

	/**
	 * @param string $name
	 * @param string $title
	 * @return string
	 */
	public function set(string $name, string $title): string
	{
		if ($this->translator !== null) {
			$translator = $this->translator;
			$catalogue = $translator->getCatalogue($translator->getLocale());
			foreach ($catalogue->all('security') as $key => $value) {
				$catalogue->set($key, $value, 'security');
			}
			$catalogue->set('roles.' . $name, $title, 'security');

			$this->writer->write($catalogue, 'neon', ['path' => $this->langDir]);

			$this->appManager->clearCache();
		}
		return $title;
	}
}