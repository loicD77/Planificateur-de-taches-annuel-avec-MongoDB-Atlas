# Planificateur de corvées avec MongoDB Atlas

## Auteur
Loïc Darras - Licence professionnelle Projet Web et Mobile

---

## Liens du projet
- **Lien principal du projet** : [https://www.projet-web-training.ovh/licence07/planning/login.php](https://www.projet-web-training.ovh/licence07/planning/login.php)
- **Lien du brouillon (répartition 13 dates/4 colonnes)** : [https://www.projet-web-training.ovh/licence07/planningbrouillon/admin.php](https://www.projet-web-training.ovh/licence07/planningbrouillon/admin.php)

---

## Contexte du projet

Ce projet a été réalisé dans le cadre du module *Mini-projets MongoDB*. J'avais initialement prévu de développer un forum, mais en raison du temps consacré à un autre projet (Space Invaders en 2D sous Java et OpenGL), j'ai choisi de me concentrer sur un planning. Ce dernier me semblait plus simple et adapté au temps restant.

---

## Fonctionnalités

### Fonctionnalités développées et attendues dans le sujet :
1. **Répartition des éplucheurs sur les 52 semaines** :  
   - Partiellement finalisé. Les utilisateurs saisissent les dates manuellement via un champ d'ajout ou de modification.
2. **Sélection de l'année** :  
   - Finalisé. Les corvées sont affichées en fonction de l'année sélectionnée.
3. **Stockage et gestion des données avec MongoDB** :  
   - Finalisé. Les données sont stockées dans un cluster MongoDB Atlas.
4. **Structure JSON des collections** :  
   - Finalisé et consultable sur MongoDB Atlas.
5. **Calcul des statistiques via MongoDB** :  
   - Partiellement finalisé. Calcul du nombre de tâches par utilisateur, par humeur, et comparaison entre tâches terminées et non terminées.
6. **Accès conditionné par connexion utilisateur** :  
   - Finalisé. Le projet distingue les privilèges entre un administrateur et un utilisateur classique.
7. **Intégration MVC** :  
   - Tentative non finalisée. Des erreurs de redirection (malgré un fichier `.htaccess`) ont empêché une implémentation complète.

---

### Fonctionnalités attendues mais non développées :
- **Validation dynamique des filtres (année et ordre)**.
- **Statistiques avancées sur les corvées (par humeur et utilisateur)**.
- **Optimisation de l'interface utilisateur** (notamment pour le choix des tâches et des utilisateurs).
- **Filtrage et tri interactif en temps réel via des requêtes MongoDB optimisées**.

---

### Fonctionnalités personnelles ajoutées :
- **Thème sombre/clair** pour l'interface utilisateur.
- **Gestion sécurisée des connexions** avec privilèges pour les administrateurs.
- **Statistiques par humeur et comparaison entre tâches terminées et non terminées.**
- **Système de filtrage par année et tri croissant/décroissant**.

---

### Écrans du projet
1. ![Corvée A](https://github.com/loicD77/Planificateur-de-taches-annuel-avec-MongoDB-Atlas/blob/main/assets/img/corv%C3%A9e%20A.png)
2. ![Corvée B](https://github.com/loicD77/Planificateur-de-taches-annuel-avec-MongoDB-Atlas/blob/main/assets/img/corv%C3%A9e%20B.png)
3. ![Corvée C](https://github.com/loicD77/Planificateur-de-taches-annuel-avec-MongoDB-Atlas/blob/main/assets/img/corv%C3%A9e%20C.png)
4. ![Corvée D](https://github.com/loicD77/Planificateur-de-taches-annuel-avec-MongoDB-Atlas/blob/main/assets/img/corv%C3%A9e%20D.png)
5. ![Corvée E](https://github.com/loicD77/Planificateur-de-taches-annuel-avec-MongoDB-Atlas/blob/main/assets/img/corv%C3%A9e%20E.png)
6. ![Corvée F](https://github.com/loicD77/Planificateur-de-taches-annuel-avec-MongoDB-Atlas/blob/main/assets/img/corv%C3%A9e%20F.png)
7. ![Corvée G](https://github.com/loicD77/Planificateur-de-taches-annuel-avec-MongoDB-Atlas/blob/main/assets/img/corv%C3%A9e%20G.png)

---
