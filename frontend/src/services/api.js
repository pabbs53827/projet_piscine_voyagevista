const API_URL = 'http://localhost/voyagevista/backend/controllers';

export async function fetchDestinations() {
    const url = API_URL + '/DestinationController.php';

    const response = await fetch(url);

    if (!response.ok) {
        throw new Error('Erreur serveur : ' + response.status);
    }

    const json = await response.json();

    if (!json.success) {
        throw new Error(json.message || 'Erreur inconnue');
    }

    return json.data;
}
