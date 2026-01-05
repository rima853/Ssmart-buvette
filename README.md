# Smart Buvette

Ce projet est une application web développée dans le cadre de notre formation à l’École Marocaine des Sciences de l’Ingénieur (EMSI) – Tanger.  
L’objectif est de digitaliser les services de la buvette universitaire afin d’améliorer la gestion des commandes, réduire les files d’attente et optimiser l’expérience des étudiants et du personnel.

---

## 📌 Description

Smart Buvette permet :  
- La consultation du menu et des produits disponibles  
- La précommande et la réservation de produits  
- La gestion des commandes côté staff  
- L’envoi et la réception de messages entre étudiants et staff  
- La réinitialisation sécurisée des mots de passe via email (Phpmailer)  

Le projet suit une architecture web à deux tiers avec un **frontend** (HTML, CSS, JavaScript) et un **backend** (PHP, MySQL).  
Le développement a suivi le **modèle Waterfall**, incluant l’analyse des besoins, la conception, l’implémentation et l’intégration.

---

## 🛠️ Technologies utilisées

- **Frontend :** HTML, CSS, JavaScript  
- **Backend :** PHP  
- **Base de données :** MySQL  
- **Librairies / Services :** Phpmailer pour l’envoi d’emails  
- **Architecture :** Web à deux tiers (Client / Serveur)

---

## 🚀 Fonctionnalités principales

### Pour les étudiants
- Consulter le menu  
- Passer des précommandes ou réserver des produits  
- Consulter l’historique des commandes  
- Envoyer des messages au staff  

### Pour le staff
- Gérer les commandes reçues  
- Consulter les messages des étudiants  
- Superviser le fonctionnement de la buvette via l’application  

---

## ⚙️ Installation / Lancement

1. Cloner le repository :
```bash
git clone https://github.com/ton-username/smart-buvette.git
2. Installer et configurer XAMPP / WAMP ou tout serveur PHP compatible
3. Importer la base de données MySQL fournie  via phpMyAdmin
4. Configurer le fichier connect.php avec vos informations de base de données :
$db_host = '127.0.0.1';
$db_name = 'smart_buvette';
$db_user = 'root';
$db_pass = '';
5. Placer le projet dans le dossier htdocs (ou équivalent)
6. Lancer l’application via http://localhost/nom_du_dossier
7. Configurer Phpmailer pour la réinitialisation des mots de passe


---

## 👥 Membres du groupe

- Rim Aassifar (GitHub :https://github.com/rima853)  
- Ichrak El Fahsi (GitHub :https://github.com/ichrak000 )  
- Khaoula El Mazouzi (GitHub :https://github.com/khaoulakhaoula20maazouzi-hue )
