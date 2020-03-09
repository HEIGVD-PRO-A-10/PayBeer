PayBeer
=======

Application d'administration pour le système de prépaiement du ChillOut.

# Installation

Installation de Docker et docker-compose :

**Arch Linux / Manjaro**
```bash
sudo pacman -S docker docker-compose
sudo gpasswd -a $USER docker
```

**Ubuntu**
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
sudo usermod -aG docker $USER
```

Récupération du projet :
```bash
git clone git@github.com:HEIGVD-PRO-A-10/PayBeer.git
cd PayBeer
```
Lancement des conteneurs :
```bash
docker-compose up -d
```