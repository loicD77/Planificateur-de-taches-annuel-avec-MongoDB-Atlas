document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('theme-toggle');
    const body = document.body;

    // Initialiser le thème à partir du localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark-mode') {
        body.classList.add('dark-mode');
        toggleButton.textContent = 'Mode clair';
    } else {
        body.classList.remove('dark-mode');
        toggleButton.textContent = 'Mode sombre';
    }

    // Basculer entre les thèmes clair et sombre
    toggleButton.addEventListener('click', () => {
        if (body.classList.contains('dark-mode')) {
            body.classList.remove('dark-mode');
            toggleButton.textContent = 'Mode sombre';
            localStorage.setItem('theme', 'light-mode');
        } else {
            body.classList.add('dark-mode');
            toggleButton.textContent = 'Mode clair';
            localStorage.setItem('theme', 'dark-mode');
        }
    });

    // Chargement des statistiques
    const statsUsersList = document.getElementById('stats-users-list');
    const statsMoodsList = document.getElementById('stats-moods-list');
    const year = new Date().getFullYear();

    fetch(`api/getStatistics.php?year=${year}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statsUsersList.innerHTML = '';
                data.statsByUser.forEach(stat => {
                    const li = document.createElement('li');
                    li.textContent = `Utilisateur: ${stat.userId}, Tâches: ${stat.taskCount}`;
                    statsUsersList.appendChild(li);
                });

                statsMoodsList.innerHTML = '';
                data.statsByMood.forEach(stat => {
                    const li = document.createElement('li');
                    li.textContent = `Humeur: ${stat.mood}, Tâches: ${stat.moodCount}`;
                    statsMoodsList.appendChild(li);
                });
            }
        });
});
