# 📅 Event Management App

## 📖 Description
Cette application est un **système de gestion d'événements** développé avec **Laravel 10**, **Inertia.js**, **Vue.js 3** et **Moment.js**.  
Elle permet de créer, modifier, supprimer et consulter des événements à venir, avec la possibilité de filtrer les événements par intervalle de dates grâce à un **component dateRangePicker**.

L'interface peut être un **calendrier mensuel** ou une **liste classique**, selon le choix de l’utilisateur.

## 🚀 Fonctionnalités

- **CRUD complet pour les événements**  
  - Ajout et modification via une modal  
  - Suppression d’événements  
  - Liste des événements à venir  

- **Filtrage par intervalle de dates**  
  - Component `dateRangePicker` pour sélectionner une période  
  - Retourne un tableau `[date_debut, date_fin]`  
  - Filtre les événements à venir côté frontend et backend (scope Laravel)  

- **Interface moderne et réactive** grâce à Inertia.js et Vue 3  

- *(Optionnel)* Hébergement en ligne pour un accès public  

## 🛠️ Technologies utilisées

- **Backend** : Laravel 10, Eloquent ORM, scopes pour filtrage par intervalle  
- **Frontend** : Vue.js 3, Inertia.js, Moment.js  
- **UI / Interaction** : modals pour CRUD, dateRangePicker pour filtrage  
- **Optionnel** : déploiement en ligne (Heroku, Laravel Forge, Vercel, etc.)

## ⚙️ Installation

1. **Clonez le dépôt** :  
```bash
git clone https://github.com/ton-compte/event-management-app.git
cd event-management-app
```

2. **Installez les dépendances PHP** :
```
composer install
```

3. **Installez les dépendances frontend** :
```
npm install
```

4. **Configurez l’environnement** :
**Copiez .env.example et configurez la base de données et les clés** :
```
cp .env.example .env
php artisan key:generate
```

5. **Migrate la base de données** :
```
php artisan migrate
```

6. **Compilez les assets** :
```
npm run dev
```

7. **Lancez le serveur Laravel** :
```
php artisan serve
```
