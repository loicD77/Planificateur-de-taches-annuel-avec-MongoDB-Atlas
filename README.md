# Planificateur de corvées avec MongoDB Atlas

## Auteur
Loïc Darras - Licence professionnelle Projet Web et Mobile


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
- **Système de filtrage par année et tri croissant/décroissant**
