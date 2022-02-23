/**
 * Restituisce un array contenente i campi da evidenziare come errori
 * @param password nuova password
 * @param password_confirm conferma nuova password
 * @param old_password vecchia password se presente
 * @returns {string[]} array di elementi da evidenziare, OK se tutto bene
 */
function check_password(password, password_confirm, old_password = null){
	var out = [];
	out['status'] = false;
	out['password'] = false;
	out['password_confirm'] = false;
	out['old_password'] = false;
	//Verifico se la nuova password ha almeno 8 caratteri
	if(password.length < 8){
		out['password'] = true;
		return out;
	}
	//Verifico i valori delle password immesse e controllo se le due nuove password sono uguali
	if(password != password_confirm){
		out['password'] = true;
		out['password_confirm'] = true;
		return out;
	}

	//Se presente anche il campo old_password verifico che la nuova password e la vecchia siano differenti
	if(old_password != null){
		if(old_password.length == 0){
			out['old_password'] = true;
			return out;
		}
	}
	out['status'] = true;
	return out;
}