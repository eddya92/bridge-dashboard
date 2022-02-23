# bridgexl-dashboard
![BridgeXL](https://www.multilevelitalia.it/wp-content/uploads/2018/07/logo_mlm-1.png)

Sorgente PHP di area Dashboard, piattaforma di gestione Network e Multilvevel Marketing [BridgeXL](https://backoffice.mybridge.it)


## Installazione
1-Cloniamo il progetto e entriamo nella cartella del progetto
```bash
git clone git@github.com:2rstudio/bridgexl-dashboard.git

cd bridgexl-dashboard
```

Assicurati di aver installato [Docker](https://docs.docker.com/engine/installation/ "Install Docker") e [Docker Compose](https://docs.docker.com/compose/install/ "Install Docker Compose").

2-pull dell'immagine prooph da docker che ci permette di  installare le dipendenze di composer, anche se non abbiamo php8
#### da LINUX
```bash
docker run --rm -it --volume $(pwd):/app prooph/composer:8.0 install
```
3-avviare il container docker
```bash
make up
```
4-installiamo gli ultimi pacchetti tramite npm
```bash
npm install
```
Assicurati di aver installato [YARN](https://classic.yarnpkg.com/en/docs/install/#debian-stable) .

5-building dei file css e js che ci occorrono per il progetto
```bash
yarn run dev
```

Sul tuo browser digita [http://localhost:8081](http://localhost:8081) per controllare se i container stanno girando.

## Personalizzazione

Puoi personalizzare i parametri dell'applicaizone copia il file `.env` in un nuovo file `.env.local`.

## Powered by 2R Studio

![2rstudio](https://www.2rstudio.it/images/logo_b.png)

BridgeXL è mantenuto dallo staff di [2R Studio SRL](https://www.2rstudio.it).

Per maggiori informazioni e documentazione puoi visitare il nostro sito web di riferimento [www.multilevelitalia.it](https://www.multilevelitalia.it).








