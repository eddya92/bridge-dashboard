<?php
declare(strict_types=1);

namespace App\Service;

use IntlDateFormatter;

final class LocaleDateFormat
{
	private $locale;
	private $pattern;

	public function __construct($pattern, $locale = 'en_US') {
		$this->setLocale($locale);
		$this->setPattern($pattern);
	}

	public function setLocale($locale) {
		$this->locale = $locale;
	}

	public function setPattern($pattern) {
		$this->pattern = $pattern;
	}

	public function localeFormat($locale, $date) {
		$this->setLocale($locale);
		return $this->format($date);
	}

	public function format($date) {
		$formatter = new IntlDateFormatter($this->locale, IntlDateFormatter::FULL, IntlDateFormatter::FULL);
		$formatter->setPattern($this->pattern);
		return $formatter->format($date);
	}
}