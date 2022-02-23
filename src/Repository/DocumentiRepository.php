<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\DocumentoCartellaViewModel;
use App\ViewModel\DocumentoFileViewModel;

interface DocumentiRepository{
	public const TAG_DOCUMENTI = 'documenti';

	public function getDocumenti(string $_locale, int $id_cartella) : ?DocumentoCartellaViewModel;
}
