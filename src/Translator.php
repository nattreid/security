<?php
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

	public function __construct($langDir, \Kdyby\Translation\Translator $translator = null, TranslationWriter $writer = null, AppManager $appManager)
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
	public function translate($name)
	{
		if ($this->translator !== null) {
			return $this->translator->translate('crm.roles.' . $name);
		}
		return $name;
	}

	/**
	 * @param string $name
	 * @param string $title
	 * @return string
	 */
	public function set($name, $title)
	{
		if ($this->translator !== null) {
			$translator = $this->translator;
			$catalogue = $translator->getCatalogue($translator->getLocale());
			foreach ($catalogue->all('crm') as $key => $value) {
				$catalogue->set($key, $value, 'crm');
			}
			$catalogue->set('roles.' . $name, $title, 'crm');

			$invalidateCache = !file_exists($this->langDir);
			$this->writer->writeTranslations($catalogue, 'neon', ['path' => $this->langDir]);

			if ($invalidateCache) {
				$this->appManager->clearCache();
			}
		}
		return $title;
	}
}