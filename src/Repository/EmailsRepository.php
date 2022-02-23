<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\EmailTemplateViewModel;
use Generator;

interface EmailsRepository{
	public const TAG_EMAILS = 'emails';

	public function getEmailTemplates(string $_locale) : ?Generator;

	public function inviaInvito(string $nome, string $email, int $idEmail) : array;

	public function getElencoEmailInviate() : ?Generator;
}
