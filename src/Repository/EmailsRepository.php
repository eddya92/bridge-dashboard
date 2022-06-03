<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\EmailTemplateViewModel;
use Generator;

interface EmailsRepository{
	public const TAG_EMAILS = 'emails';

	/**
	 * @param string $_locale
	 *
	 * @return iterable<EmailTemplateViewModel>
	 */
	public function getEmailTemplates(string $_locale) : iterable;

	/**
	 * @param string $nome
	 * @param string $email
	 * @param int    $idEmail
	 *
	 * @return array
	 */
	public function inviaInvito(string $nome, string $email, int $idEmail) : array;

	/**
	 * Api che torna elenco email inviate
	 *
	 * @return iterable<EmailTemplateViewModel>
	 */
	public function getElencoEmailInviate() : iterable;
}
