# Bridge XL - Dashboard

Cose da fare/correggere/completare

### Authentication

- [x] Durata sessione
- [x] Redirect su login
- [x] Rimuovere controlli di utente loggato dai controller
- [ ] Verificare aggiornamento di token da remoto

### Authorization

- [ ] Leggere sempre i dati dell'account
- [ ] Separare l'utente dall'account
- [ ] Definire e usare i ROLE_\<qualcosa\> per diversificare gli IBO dai Customer
- [ ] Verificare e gestire re-login su un ROLE diverso (es.: refresh su pagina scaduta, login su un utente con ROLE
  diverso da quello precedente)

### Richieste autenticate

- [x] Usare la giusta generazione della chiave

### Risposte da WS

- [ ] Definire formato di risposta per dati ed errori

### Organizzazione del codice

- [x] Correzione namespace
- [ ] Strutturazione namespace
- [ ] Non usare gli iteratori quando non servono
- [ ] Non usare funzioni quando non servono (vedi: array_push())

### Code quality

- [ ] Coding standard
- [ ] Static analysis
- [ ] Hook di formattazione su commit

### Registrazione

- [ ] Possibilmente esterna _(riparlarne con Marco e Francesco)_
