PayBeer
=======

Application d'administration pour le système de prépaiement du ChillOut.

# Installation

Installation de Docker et docker-compose :

**Arch Linux / Manjaro**
```bash
sudo pacman -S docker docker-compose
sudo gpasswd -a $USER docker
sudo systemctl enable --now docker
```

**Ubuntu**
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
sudo usermod -aG docker $USER
sudo apt-get install docker-compose
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

Accéder à l'application :
- Symfony: [http://localhost:8000](http://localhost:8000)
- phpMyAdmin: [http://localhost:8080](http://localhost:8080)
- MailHog: [http://localhost:8025](http://localhost:8025)