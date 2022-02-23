<?php
declare(strict_types=1);

namespace App\Widget\DatiSpedizione;

use App\Repository\IndirizziRepository;
use Twig\Environment;

final class DatiSpedizione{
	public function __construct(
		private Environment         $twig,
		private IndirizziRepository $indirizziSpedizioneRepository
	){
	}

	public function main(int $id, $_locale) : string{
		$indirizzi = [];
		$indirizzoSelezionato = null;
		$indirizziGenerator = $this->indirizziSpedizioneRepository->getElencoIndirizziSpedizioneSalvati($_locale);

		if($indirizziGenerator != null){
			foreach($indirizziGenerator as $indirizzoEntity){
				if($id >= 0){
					if($indirizzoEntity->getId() === $id){
						$indirizzoSelezionato = $indirizzoEntity;
					}else{
						if($indirizzoEntity->isPrincipale()){
							$indirizzoSelezionato = $indirizzoEntity;
						}
					}
				}
				$indirizzi[] = $indirizzoEntity;
			}
		}

		return $this->twig->render(
			'widgets/dati_spedizione/dati_spedizione.html.twig', [
				'indirizzi'           => $indirizzi,
				'indirizzoPrincipale' => $indirizzoSelezionato,
			]

		);
	}
}
