<?php
declare(strict_types=1);

namespace App\Repository;

use App\ViewModel\SponsorViewModel;

interface SponsorRepository{
	public const TAG_SPONSOR = 'sponsor';

	public function getSponsor() : ?SponsorViewModel;
}
